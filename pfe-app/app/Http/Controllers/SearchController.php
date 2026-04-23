<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $aiResponse = $this->parseQueryWithAI($query);

        \Log::info('AI Response: ', $aiResponse);

        $dbQuery = Activity::with(['center', 'category', 'reviews', 'images', 'schedules', 'favourites'])
            ->where('is_active', true);

        // Category filter — يدعم فئة واحدة أو فئات متعددة مفصولة بفاصلة
        if (!empty($aiResponse['category_slug'])) {
            $slugs = explode(',', $aiResponse['category_slug']); // تحويل النص إلى مصفوفة

            $dbQuery->whereHas(
                'category',
                fn($q) => $q->whereIn('slug', $slugs) // استخدام whereIn بدل where
            );
        }

        // 2. فلتر العمر الاحترافي (Inclusive Age Filter)
        // هيدا اللوجيك بيضمن إنو الـ All Ages دايماً يبيّنوا
        if (!empty($aiResponse['min_age']) || !empty($aiResponse['max_age'])) {
            $targetAge = $aiResponse['min_age'] ?? $aiResponse['max_age'];

            $dbQuery->where(function ($q) use ($targetAge) {
                $q->where(function ($sub) use ($targetAge) {
                    // شرط البداية: لازم الكورس يبلش بعمرك أو أصغر (أو يكون للكل Null/0)
                    $sub->whereNull('min_age')
                        ->orWhere('min_age', '<=', $targetAge)
                        ->orWhere('min_age', 0);
                })->where(function ($sub) use ($targetAge) {
                    // شرط النهاية: لازم الكورس يخلص بعمرك أو أكبر (أو يكون للكل Null/0)
                    $sub->whereNull('max_age')
                        ->orWhere('max_age', '>=', $targetAge)
                        ->orWhere('max_age', 0);
                });
            });
        }

        // City/neighborhood filter
        if (!empty($aiResponse['city'])) {
            $city = $aiResponse['city'];
            $dbQuery->whereHas(
                'center',
                fn($q) =>
                $q->where('city', 'like', '%' . $city . '%')
                    ->orWhere('address', 'like', '%' . $city . '%')
            );
        }

        // Private filter
        if (!empty($aiResponse['is_private'])) {
            $dbQuery->where('is_private', true);
        }

        // Keyword filter
        if (!empty($aiResponse['keyword'])) {
            $keyword = $aiResponse['keyword'];
            $dbQuery->where(
                fn($q) =>
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
            );
        }

        // Day of week filter — يدعم أكتر من يوم
        if (!empty($aiResponse['days_of_week']) && is_array($aiResponse['days_of_week'])) {
            $days = array_map('strtolower', $aiResponse['days_of_week']);
            $dbQuery->whereHas(
                'schedules',
                fn($q) =>
                $q->whereIn('day_of_week', $days)
            );
        } elseif (!empty($aiResponse['day_of_week'])) {
            $dbQuery->whereHas(
                'schedules',
                fn($q) =>
                $q->where('day_of_week', strtolower($aiResponse['day_of_week']))
            );
        }

        $activities = $dbQuery->get();
        // هيدا السطر رح يطبعلك الـ SQL اللي عم يتنفذ فعلياً في الـ Log
        \Log::info('Final SQL: ' . $dbQuery->toSql());
        \Log::info('Bindings: ', $dbQuery->getBindings());

        // Sort by average rating (highest first)
        $activities = $activities->sortByDesc(function ($act) {
            return $act->reviews->avg('rating') ?? 0;
        });

        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        if ($userLat && $userLng) {
            $activities = $activities->sortBy(function ($act) use ($userLat, $userLng) {
                $centerLat = $act->center->lat;
                $centerLng = $act->center->lng;
                if (!$centerLat || !$centerLng)
                    return 999999;
                $dLat = deg2rad($centerLat - $userLat);
                $dLng = deg2rad($centerLng - $userLng);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                    cos(deg2rad($userLat)) * cos(deg2rad($centerLat)) *
                    sin($dLng / 2) * sin($dLng / 2);
                return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
            });
        }

        // نبني الـ HTML بالـ server باستخدام الـ component
        $html = '';
        foreach ($activities as $act) {
            $html .= view('components.activity-card', ['act' => $act])->render();
        }

        return response()->json([
            'html' => $html,
            'count' => $activities->count(),
            'ai_summary' => $aiResponse['summary'] ?? null,
            'parsed' => $aiResponse,
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
  "category_slug": "slug from the list or null",
  "keyword": "main activity type (e.g. karate, pilates) or null",
  "min_age": number or null,
  "max_age": number or null,
  "city": "city or neighborhood name as mentioned or null",
  "is_private": true or false,
  "days_of_week": ["array", "of", "days"] or null,
  "day_of_week": "single day in lowercase or null",
  "summary": "A short 1-sentence friendly English summary of what you understood"
}

Available category slugs: {$categories}
Available cities: {$cities}

Rules:
- If user mentions "kids", "children", "child", "boy", "girl" → set max_age to 12, min_age to null
- If user mentions "adults" or "adult" AND no specific activity is mentioned, OR "boxing adult" specifically → set min_age to 14, max_age to null
- If user methions "all" → set min_age and max_age to null
- If user mentions "adults" or "adult" with another activity (not boxing) → set min_age to 16, max_age to null
- If user mentions a neighborhood (Achrafieh, Hamra, Gemmayzeh, Mar Mikhael, etc.) → put it in "city" as-is
- If user mentions ONE day → use "day_of_week" field
- If user mentions MULTIPLE days → use "days_of_week" array (e.g. ["thursday", "wednesday"])
- Day abbreviations: "tues"/"tue" = tuesday, "wed" = wednesday, "thur"/"thurs"/"thues" = thursday, "sat" = saturday, "sun" = sunday, "mon" = monday, "fri" = friday
- If user mentions "private" or "one-on-one" → set is_private to true
- Return ONLY the JSON, no markdown, no explanation
- MOOD MAPPING:
  * "relaxing", "calm", "chill", "unwind", "stress relief" → set category_slug to "pilates-yoga"
  * "energetic", "intense", "burn", "sweat" → set category_slug to "fitness-gym,boxing" (return them comma-separated)
  * "creative", "artistic", "fun for kids" → set category_slug to "arts-crafts"
  * "discipline", "focus", "confidence" → set category_slug to "martial-arts"
  * "outdoor", "nature", "adventure" → set category_slug to adventure-outdoor
- If user says a SPECIFIC activity (e.g. "boxing", "karate"), use that category regardless of mood words
- If NO specific activity AND mood words are present, map to the category and set keyword accordingly

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
                        'messages' => [
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
