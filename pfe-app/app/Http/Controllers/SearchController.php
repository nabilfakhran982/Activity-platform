<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    private array $keywordMap = [
        'boxing' => 'boxing',
        'karate' => 'martial-arts',
        'martial arts' => 'martial-arts',
        'taekwondo' => 'martial-arts',
        'judo' => 'martial-arts',
        'jiu jitsu' => 'martial-arts',
        'pilates' => 'pilates',
        'yoga' => 'pilates',
        'swimming' => 'swimming',
        'swim' => 'swimming',
        'fitness' => 'fitness-gym',
        'gym' => 'fitness-gym',
        'crossfit' => 'fitness-gym',
        'football' => 'football',
        'soccer' => 'football',
        'basketball' => 'basketball',
        'tennis' => 'tennis',
        'padel' => 'padel',
        'art' => 'arts-crafts',
        'craft' => 'arts-crafts',
        'painting' => 'arts-crafts',
        'drawing' => 'arts-crafts',
        'photography' => 'photography',
        'photo' => 'photography',
        'cooking' => 'cooking-classes',
        'cook' => 'cooking-classes',
        'drama' => 'drama-acting',
        'theater' => 'drama-acting',
        'theatre' => 'drama-acting',
        'acting' => 'drama-acting',
        'coding' => 'coding-robotics',
        'programming' => 'coding-robotics',
        'robotics' => 'coding-robotics',
        'language' => 'languages',
        'languages' => 'languages',
        'skiing' => 'skiing-snowboarding',
        'snowboard' => 'skiing-snowboarding',
        'water sports' => 'water-sports',
        'surfing' => 'water-sports',
        'hiking' => 'adventure-outdoor',
        'climbing' => 'adventure-outdoor',
        'outdoor' => 'adventure-outdoor',
    ];

    private array $slugCorrections = [
        'pilates-yoga' => 'pilates',
        'yoga' => 'pilates',
        'martial' => 'martial-arts',
        'adventure' => 'adventure-outdoor',
        'fitness' => 'fitness-gym',
        'arts' => 'arts-crafts',
        'cooking' => 'cooking-classes',
        'drama' => 'drama-acting',
        'coding' => 'coding-robotics',
        'language' => 'languages',
        'skiing' => 'skiing-snowboarding',
        'water' => 'water-sports',
    ];

    private function renderActivities($activities): string
    {
        $html = '';
        foreach ($activities as $act) {
            $html .= view('components.activity-card', ['act' => $act])->render();
        }
        return $html;
    }

    private function popularActivities(int $take = 3)
    {
        return Activity::with(['center', 'category', 'reviews', 'images', 'schedules', 'favourites'])
            ->where('is_active', true)
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take($take)
            ->get();
    }

    public function index()
    {
        $suggestions = \App\Models\Category::withCount('activities')
            ->having('activities_count', '>', 0)
            ->orderByDesc('activities_count')
            ->take(8)
            ->get();

        return view('search', compact('suggestions'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $queryLower = strtolower($query);
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        $aiResponse = $this->parseQueryWithAI($query);

        // ===== OVERRIDES =====
        $foundSlugs = [];
        foreach ($this->keywordMap as $word => $slug) {
            if (str_contains($queryLower, $word)) {
                if (!in_array($slug, $foundSlugs))
                    $foundSlugs[] = $slug;
            }
        }

        if (!empty($foundSlugs)) {
            $aiResponse['category_slug'] = implode(',', $foundSlugs);
            $aiResponse['keyword'] = null;
        } elseif (!empty($aiResponse['category_slug'])) {
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $corrected = array_map(fn($s) => $this->slugCorrections[$s] ?? $s, $slugs);
            $aiResponse['category_slug'] = implode(',', array_unique($corrected));
            $aiResponse['keyword'] = null;
        }

        if (!empty($aiResponse['category_slug']))
            $aiResponse['keyword'] = null;

        if (preg_match('/\b(for all|all ages|everyone|any age)\b/i', $query)) {
            $aiResponse['min_age'] = null;
            $aiResponse['max_age'] = null;
        }

        if (
            str_contains($queryLower, 'boxing') &&
            !preg_match('/\b(kid|child|adult|age|year|14|16|12)\b/i', $query)
        ) {
            $aiResponse['min_age'] = null;
            $aiResponse['max_age'] = null;
        }

        // ===== CHECK: category slug موجود بالـ DB؟ =====
        if (!empty($aiResponse['category_slug'])) {
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $existingSlugs = \App\Models\Category::whereIn('slug', $slugs)->pluck('slug')->toArray();
            $missingSlugs = array_diff($slugs, $existingSlugs);

            if (empty($existingSlugs)) {
                // كل الـ slugs مش موجودة بالـ DB
                return response()->json([
                    'html' => '',
                    'count' => 0,
                    'ai_summary' => $aiResponse['summary'] ?? null,
                    'category_not_found' => true,
                    'suggestions_html' => $this->renderActivities($this->popularActivities(3)),
                ]);
            }

            // شيل الـ slugs الغلط وخلي بس الموجودة
            if (!empty($missingSlugs)) {
                $aiResponse['category_slug'] = implode(',', $existingSlugs);
            }
        }

        // ===== BUILD QUERY =====
        $activities = $this->buildQuery($aiResponse)->get();

        // ===== FALLBACK — ما في نتائج بالمدينة =====
        $isFallback = false;
        $fallbackCity = null;

        if ($activities->isEmpty() && !empty($aiResponse['city'])) {
            $fallbackCity = $aiResponse['city'];
            $fallbackResponse = $aiResponse;
            $fallbackResponse['city'] = null;
            $activities = $this->buildQuery($fallbackResponse)->get();
            $isFallback = $activities->isNotEmpty();
        }

        // ===== NEAR ME =====
        if ($userLat && $userLng) {
            $activities = $activities->map(function ($act) use ($userLat, $userLng) {
                $lat = $act->center->lat;
                $lng = $act->center->lng;
                if (!$lat || !$lng) {
                    $act->distance = 999999;
                    return $act;
                }
                $dLat = deg2rad($lat - $userLat);
                $dLng = deg2rad($lng - $userLng);
                $a = sin($dLat / 2) ** 2 +
                    cos(deg2rad($userLat)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
                $act->distance = 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
                return $act;
            })->sortBy('distance');

            $nearbyActivities = $activities->filter(fn($act) => $act->distance <= 20);

            if ($nearbyActivities->isEmpty()) {
                $nearestCity = $activities->first()?->center?->city ?? null;

                // إذا ما في activities أصلاً — category_not_found
                if ($activities->isEmpty()) {
                    return response()->json([
                        'html' => '',
                        'count' => 0,
                        'ai_summary' => $aiResponse['summary'] ?? null,
                        'category_not_found' => true,
                        'suggestions_html' => $this->renderActivities($this->popularActivities(3)),
                    ]);
                }

                $suggestedActivities = $nearestCity
                    ? $activities->filter(fn($act) => $act->center->city === $nearestCity)
                    : $activities->take(6);

                return response()->json([
                    'html' => '',
                    'count' => 0,
                    'ai_summary' => $aiResponse['summary'] ?? null,
                    'is_near_empty' => true,
                    'nearest_city' => $nearestCity,
                    'suggestions_html' => $this->renderActivities($suggestedActivities),
                    'suggestions_count' => $suggestedActivities->count(),
                ]);
            }

            $activities = $nearbyActivities;
        } else {
            $activities = $activities->sortByDesc(fn($act) => $act->reviews->avg('rating') ?? 0);
        }

        // ===== NO RESULTS =====
        if ($activities->isEmpty()) {
            return response()->json([
                'html' => '',
                'count' => 0,
                'ai_summary' => $aiResponse['summary'] ?? null,
                'suggestions_html' => $this->renderActivities($this->popularActivities(3)),
            ]);
        }

        return response()->json([
            'html' => $this->renderActivities($activities),
            'count' => $activities->count(),
            'ai_summary' => $aiResponse['summary'] ?? null,
            'parsed' => $aiResponse,
            'is_fallback' => $isFallback,
            'fallback_city' => $fallbackCity,
            'suggestions_html' => '',
        ]);
    }

    private function buildQuery(array $aiResponse)
    {
        $dbQuery = Activity::with(['center', 'category', 'reviews', 'images', 'schedules', 'favourites'])
            ->where('is_active', true);

        if (!empty($aiResponse['category_slug'])) {
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $dbQuery->whereHas('category', fn($q) => $q->whereIn('slug', $slugs));
        }

        if (!empty($aiResponse['min_age']) || !empty($aiResponse['max_age'])) {
            $targetAge = $aiResponse['min_age'] ?? $aiResponse['max_age'];
            $dbQuery->where(function ($q) use ($targetAge) {
                $q->where(function ($sub) use ($targetAge) {
                    $sub->whereNull('min_age')->orWhere('min_age', '<=', $targetAge)->orWhere('min_age', 0);
                })->where(function ($sub) use ($targetAge) {
                    $sub->whereNull('max_age')->orWhere('max_age', '>=', $targetAge)->orWhere('max_age', 0);
                });
            });
        }

        if (!empty($aiResponse['city'])) {
            $city = $aiResponse['city'];
            $dbQuery->whereHas(
                'center',
                fn($q) =>
                $q->where('city', 'like', '%' . $city . '%')
                    ->orWhere('address', 'like', '%' . $city . '%')
            );
        }

        if (!empty($aiResponse['is_private']))
            $dbQuery->where('is_private', true);

        if (!empty($aiResponse['keyword']) && empty($aiResponse['category_slug'])) {
            $keyword = $aiResponse['keyword'];
            $dbQuery->where(
                fn($q) =>
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
            );
        }

        if (!empty($aiResponse['days_of_week']) && is_array($aiResponse['days_of_week'])) {
            $days = array_map('strtolower', $aiResponse['days_of_week']);
            $dbQuery->whereHas('schedules', fn($q) => $q->whereIn('day_of_week', $days));
        } elseif (!empty($aiResponse['day_of_week'])) {
            $dbQuery->whereHas(
                'schedules',
                fn($q) =>
                $q->where('day_of_week', strtolower($aiResponse['day_of_week']))
            );
        }

        if (!empty($aiResponse['level']))
            $dbQuery->where('level', $aiResponse['level']);

        if (!empty($aiResponse['start_time'])) {
            $parsed = date('H:i:s', strtotime($aiResponse['start_time']));
            if ($parsed && $parsed !== '00:00:00') {
                $dbQuery->whereHas('schedules', fn($q) => $q->whereTime('start_time', $parsed));
            }
        }

        return $dbQuery;
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
  "start_time": "24-hour format HH:MM:SS or null"
}

Available category slugs: {$categories}
Available cities: {$cities}

CRITICAL RULES:
1. If user names ONE activity → category_slug = exact matching slug, keyword = null
2. If user names MULTIPLE activities → category_slug = comma-separated exact slugs, keyword = null
3. If category_slug is set → keyword MUST be null
4. ALWAYS use exact slugs from the list — never invent slugs
5. "for all", "all ages", "everyone", "any age" → min_age = null, max_age = null
6. "kids", "children", "child" → max_age = 12, min_age = null
7. "boxing" with NO age mention → min_age = null, max_age = null
8. "adults" → min_age = 16, max_age = null
9. "private" or "one-on-one" → is_private = true
10. "beginner/s" → level = "beginner", "intermediate" → level = "intermediate", "advanced" → level = "advanced"
11. Return ONLY JSON, no markdown
12. If query mentions an activity NOT in the available slugs list → category_slug = null, keyword = the activity name

MOOD MAPPING (only when NO specific activity named):
- "relaxing", "calm", "chill", "unwind" → "pilates"
- "energetic", "intense", "burn", "sweat" → "fitness-gym,boxing"
- "creative", "artistic" → "arts-crafts"
- "discipline", "focus", "confidence" → "martial-arts"
- "outdoor", "nature", "adventure" → "adventure-outdoor"

User query: "{$query}"
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                        'model' => 'claude-haiku-4-5-20251001',
                        'max_tokens' => 400,
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);

            $text = $response->json()['content'][0]['text'] ?? '{}';
            $text = trim(preg_replace('/```json|```/', '', $text));
            return json_decode($text, true) ?? [];

        } catch (\Exception $e) {
            \Log::error('Anthropic error: ' . $e->getMessage());
            return [];
        }
    }
}
