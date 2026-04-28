<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    // Keyword → category slug mapping — slugs لازم تطابق الـ DB بالضبط
    private array $keywordMap = [
        'boxing'       => 'boxing',
        'karate'       => 'martial-arts',
        'martial arts' => 'martial-arts',
        'taekwondo'    => 'martial-arts',
        'judo'         => 'martial-arts',
        'jiu jitsu'    => 'martial-arts',
        'pilates'      => 'pilates',       // DB slug = pilates
        'yoga'         => 'pilates',       // ما في yoga category — map لـ pilates
        'swimming'     => 'swimming',
        'swim'         => 'swimming',
        'dance'        => 'dance',
        'dancing'      => 'dance',
        'fitness'      => 'fitness-gym',
        'gym'          => 'fitness-gym',
        'crossfit'     => 'fitness-gym',
        'football'     => 'football',
        'soccer'       => 'football',
        'art'          => 'arts-crafts',
        'craft'        => 'arts-crafts',
        'painting'     => 'arts-crafts',
        'drawing'      => 'arts-crafts',
        'hiking'       => 'adventure-outdoor',
        'climbing'     => 'adventure-outdoor',
        'outdoor'      => 'adventure-outdoor',
    ];

    // AI slugs → DB slugs correction
    private array $slugCorrections = [
        'pilates-yoga'     => 'pilates',
        'yoga'             => 'pilates',
        'martial'          => 'martial-arts',
        'adventure'        => 'adventure-outdoor',
        'fitness'          => 'fitness-gym',
        'arts'             => 'arts-crafts',
    ];

    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $queryLower = strtolower($query);

        $aiResponse = $this->parseQueryWithAI($query);

        // ===== OVERRIDES =====

        // 1. Manual multi-activity detection من الـ query مباشرة
        $foundSlugs = [];
        foreach ($this->keywordMap as $word => $slug) {
            if (str_contains($queryLower, $word)) {
                if (!in_array($slug, $foundSlugs)) {
                    $foundSlugs[] = $slug;
                }
            }
        }

        if (!empty($foundSlugs)) {
            // الـ PHP manual detection أدق من الـ AI — override دايماً
            $aiResponse['category_slug'] = implode(',', $foundSlugs);
            $aiResponse['keyword'] = null;
        } elseif (!empty($aiResponse['category_slug'])) {
            // إذا الـ AI حدد category بس الـ manual ما لاقى شي — صحح الـ AI slugs
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $corrected = array_map(fn($s) => $this->slugCorrections[$s] ?? $s, $slugs);
            $aiResponse['category_slug'] = implode(',', array_unique($corrected));
            $aiResponse['keyword'] = null;
        }

        // 2. إذا في category_slug — شيل الـ keyword دايماً
        if (!empty($aiResponse['category_slug'])) {
            $aiResponse['keyword'] = null;
        }

        // 3. إذا في "for all/all ages" — شيل أي age filter
        if (preg_match('/\b(for all|all ages|everyone|any age)\b/i', $query)) {
            $aiResponse['min_age'] = null;
            $aiResponse['max_age'] = null;
        }

        // 4. Boxing بدون ذكر عمر → null ages
        if (str_contains($queryLower, 'boxing') &&
            !preg_match('/\b(kid|child|adult|age|year|14|16|12)\b/i', $query)) {
            $aiResponse['min_age'] = null;
            $aiResponse['max_age'] = null;
        }

        \Log::info('AI Response (after overrides): ', $aiResponse);

        $dbQuery = Activity::with(['center', 'category', 'reviews', 'images', 'schedules', 'favourites'])
            ->where('is_active', true);

        // Category filter
        if (!empty($aiResponse['category_slug'])) {
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $dbQuery->whereHas('category', fn($q) => $q->whereIn('slug', $slugs));
        }

        // Age filter
        if (!empty($aiResponse['min_age']) || !empty($aiResponse['max_age'])) {
            $targetAge = $aiResponse['min_age'] ?? $aiResponse['max_age'];

            $dbQuery->where(function ($q) use ($targetAge) {
                $q->where(function ($sub) use ($targetAge) {
                    $sub->whereNull('min_age')
                        ->orWhere('min_age', '<=', $targetAge)
                        ->orWhere('min_age', 0);
                })->where(function ($sub) use ($targetAge) {
                    $sub->whereNull('max_age')
                        ->orWhere('max_age', '>=', $targetAge)
                        ->orWhere('max_age', 0);
                });
            });
        }

        // City/neighborhood filter
        if (!empty($aiResponse['city'])) {
            $city = $aiResponse['city'];
            $dbQuery->whereHas('center', fn($q) =>
                $q->where('city', 'like', '%' . $city . '%')
                  ->orWhere('address', 'like', '%' . $city . '%')
            );
        }

        // Private filter
        if (!empty($aiResponse['is_private'])) {
            $dbQuery->where('is_private', true);
        }

        // Keyword filter — بس إذا ما في category_slug
        if (!empty($aiResponse['keyword']) && empty($aiResponse['category_slug'])) {
            $keyword = $aiResponse['keyword'];
            $dbQuery->where(fn($q) =>
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
            );
        }

        // Day of week filter
        if (!empty($aiResponse['days_of_week']) && is_array($aiResponse['days_of_week'])) {
            $days = array_map('strtolower', $aiResponse['days_of_week']);
            $dbQuery->whereHas('schedules', fn($q) => $q->whereIn('day_of_week', $days));
        } elseif (!empty($aiResponse['day_of_week'])) {
            $dbQuery->whereHas('schedules', fn($q) =>
                $q->where('day_of_week', strtolower($aiResponse['day_of_week']))
            );
        }

        // Level filter
        if (!empty($aiResponse['level'])) {
            $dbQuery->where('level', $aiResponse['level']);
        }

        // Time filter
        if (!empty($aiResponse['start_time'])) {
            $parsed = date('H:i:s', strtotime($aiResponse['start_time']));
            if ($parsed && $parsed !== '00:00:00') {
                $dbQuery->whereHas('schedules', fn($q) =>
                    $q->whereTime('start_time', $parsed)
                );
            }
        }

        $activities = $dbQuery->get();

        \Log::info('Final SQL: ' . $dbQuery->toSql());
        \Log::info('Bindings: ', $dbQuery->getBindings());

        // Sort by rating
        $activities = $activities->sortByDesc(fn($act) => $act->reviews->avg('rating') ?? 0);

        // Near me sort
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        if ($userLat && $userLng) {
            $activities = $activities->sortBy(function ($act) use ($userLat, $userLng) {
                $centerLat = $act->center->lat;
                $centerLng = $act->center->lng;
                if (!$centerLat || !$centerLng) return 999999;
                $dLat = deg2rad($centerLat - $userLat);
                $dLng = deg2rad($centerLng - $userLng);
                $a = sin($dLat / 2) ** 2 +
                     cos(deg2rad($userLat)) * cos(deg2rad($centerLat)) * sin($dLng / 2) ** 2;
                return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
            });
        }

        $html = '';
        foreach ($activities as $act) {
            $html .= view('components.activity-card', ['act' => $act])->render();
        }

        return response()->json([
            'html'       => $html,
            'count'      => $activities->count(),
            'ai_summary' => $aiResponse['summary'] ?? null,
            'parsed'     => $aiResponse,
        ]);
    }

    private function parseQueryWithAI(string $query): array
    {
        $categories = \App\Models\Category::pluck('name', 'slug')->toJson();
        $cities = json_encode(require app_path('Data/cities.php'));

        $prompt = <<<PROMPT
You are a search assistant for Activio, a Lebanese activity booking platform.

Extract the following information from the user's search query and return ONLY a valid JSON object with no extra text:

{
  "category_slug": "slug(s) from the list comma-separated or null",
  "keyword": "only if NO category matched, else null",
  "min_age": number or null,
  "max_age": number or null,
  "city": "city or neighborhood name as mentioned or null",
  "is_private": true or false,
  "days_of_week": ["array", "of", "days"] or null,
  "day_of_week": "single day in lowercase or null",
  "summary": "A short 1-sentence friendly English summary of what you understood",
  "level": "beginner, intermediate, or advanced or null",
  "start_time": "24-hour format HH:MM:SS or null. Convert AM/PM to 24h. Examples: 6:00 PM = 18:00:00, 7:00 AM = 07:00:00, 18:00 = 18:00:00"
}

Available category slugs: {$categories}
Available cities: {$cities}

CRITICAL RULES (in priority order):
1. If user names ONE activity → set category_slug to the EXACT matching slug from the list, keyword = null
2. If user names MULTIPLE activities → set category_slug as comma-separated EXACT slugs from the list, keyword = null
3. If category_slug is set → keyword MUST be null
4. ALWAYS use the exact slugs from Available category slugs list — never invent slugs
5. "for all", "all ages", "everyone", "any age" → min_age = null, max_age = null
6. "kids", "children", "child", "boy", "girl" → max_age = 12, min_age = null
7. "boxing" with NO age mention → min_age = null, max_age = null
8. "boxing adult" or "adult boxing" → min_age = 14, max_age = null
9. "adults" with no activity → min_age = 14, max_age = null
10. "adults" with activity (not boxing) → min_age = 16, max_age = null
11. Neighborhood → put in "city" as-is
12. ONE day → "day_of_week" only
13. MULTIPLE days → "days_of_week" array only
14. "private" or "one-on-one" → is_private = true
15. "beginner", "beginners", "starter" → level = "beginner"
16. "intermediate" → level = "intermediate"
17. "advanced", "expert" → level = "advanced"
18. Return ONLY JSON, no markdown

MOOD MAPPING (only when NO specific activity named):
- "relaxing", "calm", "chill", "unwind", "stress relief" → "pilates"
- "energetic", "intense", "burn", "sweat" → "fitness-gym,boxing"
- "creative", "artistic" → "arts-crafts"
- "discipline", "focus", "confidence" → "martial-arts"
- "outdoor", "nature", "adventure" → "adventure-outdoor"

User query: "{$query}"
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 400,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

            \Log::info('Anthropic raw response: ', $response->json());

            $text = $response->json()['content'][0]['text'] ?? '{}';
            $text = preg_replace('/```json|```/', '', $text);
            $text = trim($text);

            return json_decode($text, true) ?? [];

        } catch (\Exception $e) {
            \Log::error('Anthropic error: ' . $e->getMessage());
            return [];
        }
    }
}
