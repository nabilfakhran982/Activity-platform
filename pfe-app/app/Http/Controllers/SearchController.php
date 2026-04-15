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

        // Category filter
        if (!empty($aiResponse['category_slug'])) {
            $dbQuery->whereHas('category', fn($q) =>
                $q->where('slug', $aiResponse['category_slug'])
            );
        }

        // Age filter — kids: max_age يصغّر، adults: min_age يكبر
        if (!empty($aiResponse['max_age'])) {
            // أي activity يكون min_age تبعها أقل أو يساوي max_age المطلوب
            $dbQuery->where(fn($q) =>
                $q->whereNull('min_age')->orWhere('min_age', '<=', $aiResponse['max_age'])
            );
        }

        if (!empty($aiResponse['min_age'])) {
            // أي activity يكون max_age تبعها أكبر أو يساوي min_age المطلوب أو null
            $dbQuery->where(fn($q) =>
                $q->whereNull('max_age')->orWhere('max_age', '>=', $aiResponse['min_age'])
            );
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

        // Keyword filter
        if (!empty($aiResponse['keyword'])) {
            $keyword = $aiResponse['keyword'];
            $dbQuery->where(fn($q) =>
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
            );
        }

        // Day of week filter — يدعم أكتر من يوم
        if (!empty($aiResponse['days_of_week']) && is_array($aiResponse['days_of_week'])) {
            $days = array_map('strtolower', $aiResponse['days_of_week']);
            $dbQuery->whereHas('schedules', fn($q) =>
                $q->whereIn('day_of_week', $days)
            );
        } elseif (!empty($aiResponse['day_of_week'])) {
            $dbQuery->whereHas('schedules', fn($q) =>
                $q->where('day_of_week', strtolower($aiResponse['day_of_week']))
            );
        }

        $activities = $dbQuery->get();

        // نبني الـ HTML بالـ server باستخدام الـ component
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
- If user mentions "adults" or "adult" → set min_age to 16, max_age to null
- If user mentions a neighborhood (Achrafieh, Hamra, Gemmayzeh, Mar Mikhael, etc.) → put it in "city" as-is
- If user mentions ONE day → use "day_of_week" field
- If user mentions MULTIPLE days → use "days_of_week" array (e.g. ["thursday", "wednesday"])
- Day abbreviations: "tues"/"tue" = tuesday, "wed" = wednesday, "thur"/"thurs"/"thues" = thursday, "sat" = saturday, "sun" = sunday, "mon" = monday, "fri" = friday
- If user mentions "private" or "one-on-one" → set is_private to true
- Return ONLY the JSON, no markdown, no explanation

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
