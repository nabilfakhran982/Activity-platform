<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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

    private array $dayOrder = [
        'monday' => 0,
        'tuesday' => 1,
        'wednesday' => 2,
        'thursday' => 3,
        'friday' => 4,
        'saturday' => 5,
        'sunday' => 6,
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

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);
        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    private function formatTime(string $time): string
    {
        $mins = $this->timeToMinutes($time);
        $h = intdiv($mins, 60);
        $m = $mins % 60;
        $display = $h > 12 ? $h - 12 : ($h === 0 ? 12 : $h);
        return sprintf('%d:%02d %s', $display, $m, $h >= 12 ? 'PM' : 'AM');
    }

    public function index()
    {
        $suggestions = \App\Models\Category::withCount('activities')
            ->having('activities_count', '>', 0)
            ->orderByDesc('activities_count')
            ->take(8)
            ->get();

        // ===== PERSONALIZED RECOMMENDATIONS =====
        $recommendations     = collect();
        $recommendationTitle = null;
        $recommendationLabel = null;

        if (Auth::check()) {
            $user = Auth::user();

            // جمع الـ preferred category IDs من bookings وfavourites
            $bookedCategoryIds = $user->bookings()
                ->with('schedule.activity')
                ->get()
                ->pluck('schedule.activity.category_id')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->toArray();

            $favCategoryIds = $user->favourites()
                ->with('activity')
                ->get()
                ->pluck('activity.category_id')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->toArray();

            // Merge — bookings أعطيها priority
            $preferredCategoryIds = collect($bookedCategoryIds)
                ->merge($favCategoryIds)
                ->unique()
                ->take(3)
                ->toArray();

            if (!empty($preferredCategoryIds)) {
                $bookedActivityIds = $user->bookings()
                    ->with('schedule.activity')
                    ->get()
                    ->pluck('schedule.activity.id')
                    ->filter()
                    ->toArray();

                $recommendations = \App\Models\Activity::with(['center', 'category', 'reviews', 'images', 'favourites'])
                    ->where('is_active', true)
                    ->whereIn('category_id', $preferredCategoryIds)
                    ->whereNotIn('id', $bookedActivityIds)
                    ->withCount('bookings')
                    ->orderByDesc('bookings_count')
                    ->get();

                if ($recommendations->isNotEmpty()) {
                    $recommendationLabel = '✦ RECOMMENDED FOR YOU';
                    $recommendationTitle = '';
                }
            }
        }

        return view('search', compact('suggestions', 'recommendations', 'recommendationLabel', 'recommendationTitle'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $queryLower = strtolower($query);
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        $aiResponse = $this->parseQueryWithAI($query);

        // ===== KEYWORD OVERRIDES =====
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

        // ===== VALIDATE CATEGORY =====
        if (!empty($aiResponse['category_slug'])) {
            $slugs = array_map('trim', explode(',', $aiResponse['category_slug']));
            $existingSlugs = \App\Models\Category::whereIn('slug', $slugs)->pluck('slug')->toArray();
            $missingSlugs = array_diff($slugs, $existingSlugs);

            if (empty($existingSlugs)) {
                return response()->json([
                    'html' => '',
                    'count' => 0,
                    'ai_summary' => $aiResponse['summary'] ?? null,
                    'category_not_found' => true,
                    'suggestions_html' => $this->renderActivities($this->popularActivities(3)),
                ]);
            }
            if (!empty($missingSlugs)) {
                $aiResponse['category_slug'] = implode(',', $existingSlugs);
            }
        }

        // ===== INITIAL QUERY (day+time combined in single whereHas) =====
        $activities = $this->buildQuery($aiResponse)->get();

        // ===== CITY FALLBACK =====
        $isFallback = false;
        $fallbackCity = null;
        if ($activities->isEmpty() && !empty($aiResponse['city'])) {
            $fallbackCity = $aiResponse['city'];
            $fallbackResponse = $aiResponse;
            $fallbackResponse['city'] = null;
            $activities = $this->buildQuery($fallbackResponse)->get();
            $isFallback = $activities->isNotEmpty();
        }

        // ===== FALLBACK STATE =====
        $isDayFallback = false;
        $suggestedDay = null;
        $isTimeFallback = false;
        $isTimeOnDayFallback = false;
        $suggestedTime = null;
        $availableTimesOnDay = null;

        $requestedDay  = $aiResponse['day_of_week'] ?? ($aiResponse['days_of_week'][0] ?? null);
        $requestedTime = !empty($aiResponse['start_time']) ? $aiResponse['start_time'] : null;

        if ($activities->isEmpty()) {

            // ─── CASE A: both day AND time specified ───────────────────────────
            if ($requestedDay && $requestedTime) {

                // A1: try same day, any time — day exists but not at requested time
                $dayOnly = $aiResponse;
                $dayOnly['start_time'] = null;
                $dayOnlyActs = $this->buildQuery($dayOnly)->get();

                if ($dayOnlyActs->isNotEmpty()) {
                    $requestedMins = $this->timeToMinutes($requestedTime);

                    // For each activity, find closest schedule time that is ON the requested day
                    $dayOnlyActs = $dayOnlyActs->map(function ($act) use ($requestedMins, $requestedDay) {
                        $closestDiff = PHP_INT_MAX;
                        $closestTime = null;
                        foreach ($act->schedules as $s) {
                            if (strtolower($s->day_of_week) !== strtolower($requestedDay)) continue;
                            $diff = abs($this->timeToMinutes($s->start_time) - $requestedMins);
                            if ($diff < $closestDiff) {
                                $closestDiff = $diff;
                                $closestTime = $s->start_time;
                            }
                        }
                        $act->time_diff   = $closestDiff;
                        $act->closest_time = $closestTime;
                        return $act;
                    })->sortBy('time_diff');

                    $activities          = $dayOnlyActs;
                    $isTimeOnDayFallback = true;
                    $suggestedTime       = $this->formatTime($activities->first()->closest_time ?? $requestedTime);

                    // Collect every distinct session time available on the requested day
                    $availableTimesOnDay = $activities
                        ->flatMap(fn($act) => $act->schedules)
                        ->filter(fn($s) => strtolower($s->day_of_week) === strtolower($requestedDay))
                        ->pluck('start_time')->unique()->sort()->values()
                        ->map(fn($t) => $this->formatTime($t))
                        ->implode(', ');

                } else {
                    // A2: day itself has no matching activities — find closest day (prioritise time too)
                    $none = $aiResponse;
                    $none['day_of_week']   = null;
                    $none['days_of_week']  = null;
                    $none['start_time']    = null;
                    $all = $this->buildQuery($none)->get();

                    if ($all->isNotEmpty()) {
                        $reqDayNum     = $this->dayOrder[strtolower($requestedDay)] ?? 0;
                        $requestedMins = $this->timeToMinutes($requestedTime);

                        $all = $all->map(function ($act) use ($reqDayNum, $requestedMins) {
                            $bestDayDiff  = PHP_INT_MAX;
                            $bestTimeDiff = PHP_INT_MAX;
                            $bestDay      = null;
                            $bestTime     = null;
                            foreach ($act->schedules as $s) {
                                $dayNum  = $this->dayOrder[strtolower($s->day_of_week)] ?? 0;
                                $dayDiff = min(abs($dayNum - $reqDayNum), 7 - abs($dayNum - $reqDayNum));
                                $timeDiff = abs($this->timeToMinutes($s->start_time) - $requestedMins);
                                if (
                                    $dayDiff < $bestDayDiff ||
                                    ($dayDiff === $bestDayDiff && $timeDiff < $bestTimeDiff)
                                ) {
                                    $bestDayDiff  = $dayDiff;
                                    $bestTimeDiff = $timeDiff;
                                    $bestDay      = $s->day_of_week;
                                    $bestTime     = $s->start_time;
                                }
                            }
                            $act->day_diff    = $bestDayDiff;
                            $act->closest_day = $bestDay;
                            $act->time_diff   = $bestTimeDiff;
                            $act->closest_time = $bestTime;
                            return $act;
                        })->sortBy('day_diff');

                        $minDayDiff   = $all->first()?->day_diff ?? PHP_INT_MAX;
                        $dayProximity = $all->filter(fn($act) => $act->day_diff === $minDayDiff && $minDayDiff <= 3);
                        if ($dayProximity->isNotEmpty()) {
                            $activities    = $dayProximity;
                            $isDayFallback = true;
                            $suggestedDay  = ucfirst($activities->first()->closest_day ?? '');
                        }
                    }
                }

            // ─── CASE B: only time specified ───────────────────────────────────
            } elseif ($requestedTime && !$requestedDay) {
                $requestedMins = $this->timeToMinutes($requestedTime);
                $noTime = $aiResponse;
                $noTime['start_time'] = null;
                $all = $this->buildQuery($noTime)->get();

                if ($all->isNotEmpty()) {
                    $all = $all->map(function ($act) use ($requestedMins) {
                        $closestDiff = PHP_INT_MAX;
                        $closestTime = null;
                        foreach ($act->schedules as $s) {
                            $diff = abs($this->timeToMinutes($s->start_time) - $requestedMins);
                            if ($diff < $closestDiff) {
                                $closestDiff = $diff;
                                $closestTime = $s->start_time;
                            }
                        }
                        $act->time_diff    = $closestDiff;
                        $act->closest_time = $closestTime;
                        return $act;
                    })->sortBy('time_diff');

                    // Only show results within 90 minutes of the requested time
                    $timeProximity = $all->filter(fn($act) => $act->time_diff <= 90);

                    // Widen to 180 min if nothing found within 90
                    if ($timeProximity->isEmpty()) {
                        $timeProximity = $all->filter(fn($act) => $act->time_diff <= 180);
                    }

                    if ($timeProximity->isNotEmpty()) {
                        $activities     = $timeProximity;
                        $isTimeFallback = true;
                        // Suggested time = the closest schedule time found globally
                        $suggestedTime  = $this->formatTime($activities->first()->closest_time);
                    }
                }

            // ─── CASE C: only day specified ────────────────────────────────────
            } elseif ($requestedDay && !$requestedTime) {
                $reqDayNum = $this->dayOrder[strtolower($requestedDay)] ?? 0;
                $noDay = $aiResponse;
                $noDay['day_of_week']  = null;
                $noDay['days_of_week'] = null;
                $all = $this->buildQuery($noDay)->get();

                if ($all->isNotEmpty()) {
                    $all = $all->map(function ($act) use ($reqDayNum) {
                        $closestDiff = PHP_INT_MAX;
                        $closestDay  = null;
                        foreach ($act->schedules as $s) {
                            $dayNum = $this->dayOrder[strtolower($s->day_of_week)] ?? 0;
                            $diff   = min(abs($dayNum - $reqDayNum), 7 - abs($dayNum - $reqDayNum));
                            if ($diff < $closestDiff) {
                                $closestDiff = $diff;
                                $closestDay  = $s->day_of_week;
                            }
                        }
                        $act->day_diff    = $closestDiff;
                        $act->closest_day = $closestDay;
                        return $act;
                    })->sortBy('day_diff');

                    $minDayDiff   = $all->first()?->day_diff ?? PHP_INT_MAX;
                    $dayProximity = $all->filter(fn($act) => $act->day_diff === $minDayDiff && $minDayDiff <= 3);
                    if ($dayProximity->isNotEmpty()) {
                        $activities    = $dayProximity;
                        $isDayFallback = true;
                        $suggestedDay  = ucfirst($activities->first()->closest_day ?? '');
                    }
                }
            }
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
                $a = sin($dLat / 2) ** 2 + cos(deg2rad($userLat)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;
                $act->distance = 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
                return $act;
            })->sortBy('distance');

            $nearbyActivities = $activities->filter(fn($act) => $act->distance <= 20);

            if ($nearbyActivities->isEmpty()) {

                // Try day+time-aware fallback without near-me constraint
                if ($requestedDay) {
                    $reqDayNum    = $this->dayOrder[strtolower($requestedDay)] ?? 0;
                    $categoryName = !empty($aiResponse['category_slug'])
                        ? \App\Models\Category::whereIn('slug', explode(',', $aiResponse['category_slug']))->pluck('name')->implode(' & ')
                        : 'activities';

                    // Step 1: same day anywhere in Lebanon, any time
                    $sameDayQuery = $aiResponse;
                    $sameDayQuery['start_time'] = null;
                    $allOnDay = $this->buildQuery($sameDayQuery)->get();

                    if ($allOnDay->isNotEmpty()) {
                        if ($requestedTime) {
                            $requestedMins          = $this->timeToMinutes($requestedTime);
                            $requestedTimeFormatted = $this->formatTime($requestedTime);

                            // Sort activities by how close their Tuesday session is to requested time
                            $allOnDay = $allOnDay->map(function ($act) use ($requestedMins, $requestedDay) {
                                $closestDiff = PHP_INT_MAX;
                                $closestTime = null;
                                foreach ($act->schedules as $s) {
                                    if (strtolower($s->day_of_week) !== strtolower($requestedDay)) continue;
                                    $diff = abs($this->timeToMinutes($s->start_time) - $requestedMins);
                                    if ($diff < $closestDiff) {
                                        $closestDiff = $diff;
                                        $closestTime = $s->start_time;
                                    }
                                }
                                $act->time_diff    = $closestDiff;
                                $act->closest_time = $closestTime;
                                return $act;
                            })->sortBy('time_diff');

                            // Collect all unique times available on that day (sorted ascending)
                            $availTimes = $allOnDay
                                ->flatMap(fn($act) => $act->schedules)
                                ->filter(fn($s) => strtolower($s->day_of_week) === strtolower($requestedDay))
                                ->pluck('start_time')->unique()->sort()->values()
                                ->map(fn($t) => $this->formatTime($t))
                                ->implode(', ');

                            $nearFallbackMsg = "No {$categoryName} near you on " . ucfirst($requestedDay) . " at {$requestedTimeFormatted} — available sessions on " . ucfirst($requestedDay) . ": {$availTimes}";
                        } else {
                            $nearFallbackMsg = "No {$categoryName} near you on " . ucfirst($requestedDay) . " — showing sessions on " . ucfirst($requestedDay) . " from other locations";
                        }

                        return response()->json([
                            'html'                    => $this->renderActivities($allOnDay),
                            'count'                   => $allOnDay->count(),
                            'ai_summary'              => $aiResponse['summary'] ?? null,
                            'is_day_fallback'         => true,
                            'is_time_on_day_fallback' => (bool) $requestedTime,
                            'fallback_message'        => $nearFallbackMsg,
                            'suggestions_html'        => '',
                        ]);
                    }

                    // Step 2: day doesn't exist anywhere — find closest available day
                    $relaxedForDay = $aiResponse;
                    $relaxedForDay['day_of_week']  = null;
                    $relaxedForDay['days_of_week'] = null;
                    $relaxedForDay['start_time']   = null;
                    $allForDay = $this->buildQuery($relaxedForDay)->get();

                    $withDay = $allForDay->map(function ($act) use ($reqDayNum) {
                        $closestDiff = PHP_INT_MAX;
                        $closestDay  = null;
                        foreach ($act->schedules as $s) {
                            $dayNum = $this->dayOrder[strtolower($s->day_of_week)] ?? 0;
                            $diff   = min(abs($dayNum - $reqDayNum), 7 - abs($dayNum - $reqDayNum));
                            if ($diff < $closestDiff) {
                                $closestDiff = $diff;
                                $closestDay  = $s->day_of_week;
                            }
                        }
                        $act->day_diff    = $closestDiff;
                        $act->closest_day = $closestDay;
                        return $act;
                    })->sortBy('day_diff');

                    $nearMinDiff = $withDay->first()?->day_diff ?? PHP_INT_MAX;
                    $withDay = $withDay->filter(fn($act) => $act->day_diff === $nearMinDiff && $nearMinDiff <= 3);

                    if ($withDay->isNotEmpty()) {
                        $nearSuggestedDay = ucfirst($withDay->first()->closest_day ?? '');

                        return response()->json([
                            'html'             => $this->renderActivities($withDay),
                            'count'            => $withDay->count(),
                            'ai_summary'       => $aiResponse['summary'] ?? null,
                            'is_day_fallback'  => true,
                            'suggested_day'    => $nearSuggestedDay,
                            'fallback_message' => "No {$categoryName} near you on " . ucfirst($requestedDay) . " — showing closest sessions on {$nearSuggestedDay}",
                            'suggestions_html' => '',
                        ]);
                    }
                }

                // No day fallback either — show nearest city suggestions
                $nearestCity = $activities->first()?->center?->city ?? null;
                if ($activities->isEmpty()) {
                    return response()->json([
                        'html'              => '',
                        'count'             => 0,
                        'ai_summary'        => $aiResponse['summary'] ?? null,
                        'category_not_found' => true,
                        'suggestions_html'  => $this->renderActivities($this->popularActivities(3)),
                    ]);
                }
                $suggestedActivities = $nearestCity
                    ? $activities->filter(fn($act) => $act->center->city === $nearestCity)
                    : $activities->take(6);

                return response()->json([
                    'html'              => '',
                    'count'             => 0,
                    'ai_summary'        => $aiResponse['summary'] ?? null,
                    'is_near_empty'     => true,
                    'nearest_city'      => $nearestCity,
                    'suggestions_html'  => $this->renderActivities($suggestedActivities),
                    'suggestions_count' => $suggestedActivities->count(),
                ]);
            } else {
                $activities = $nearbyActivities;

                // Recompute suggestedDay/availableTimesOnDay from the activities that actually passed
                // the distance filter — the pre-filter set may have had a different first element
                if ($isDayFallback && $activities->isNotEmpty()) {
                    $firstDay = $activities->first()->closest_day ?? null;
                    if ($firstDay) {
                        $suggestedDay = ucfirst($firstDay);
                    }
                }
                if ($isTimeOnDayFallback && $activities->isNotEmpty() && $requestedDay) {
                    $availableTimesOnDay = $activities
                        ->flatMap(fn($act) => $act->schedules)
                        ->filter(fn($s) => strtolower($s->day_of_week) === strtolower($requestedDay))
                        ->pluck('start_time')->unique()->sort()->values()
                        ->map(fn($t) => $this->formatTime($t))
                        ->implode(', ');
                }
            }
        } else {
            $activities = $activities->sortByDesc(fn($act) => $act->reviews->avg('rating') ?? 0);
        }

        // ===== NO RESULTS =====
        if ($activities->isEmpty()) {
            return response()->json([
                'html'             => '',
                'count'            => 0,
                'ai_summary'       => $aiResponse['summary'] ?? null,
                'suggestions_html' => $this->renderActivities($this->popularActivities(3)),
            ]);
        }

        $requestedTimeFormatted = $requestedTime ? $this->formatTime($requestedTime) : null;
        $categoryLabel = !empty($aiResponse['category_slug'])
            ? \App\Models\Category::whereIn('slug', explode(',', $aiResponse['category_slug']))->pluck('name')->implode(' & ')
            : 'activities';

        // Build fallback message — always include time context when available
        $fallbackMessage = null;

        if ($isTimeOnDayFallback) {
            $timesStr = $availableTimesOnDay ?: $suggestedTime;
            $fallbackMessage = "No {$categoryLabel} on " . ucfirst($requestedDay ?? '') . " at {$requestedTimeFormatted} — available sessions on " . ucfirst($requestedDay ?? '') . ": {$timesStr}";

        } elseif ($isDayFallback) {
            if ($requestedTime && $suggestedDay) {
                // Compute what times exist on the suggested day from the shown activities
                $suggestedDayLower = strtolower($suggestedDay);
                $timesOnSugDay = $activities
                    ->flatMap(fn($act) => $act->schedules)
                    ->filter(fn($s) => strtolower($s->day_of_week) === $suggestedDayLower)
                    ->pluck('start_time')->unique()->sort()->values()
                    ->map(fn($t) => $this->formatTime($t))
                    ->implode(', ');

                if ($timesOnSugDay) {
                    $fallbackMessage = "No {$categoryLabel} on " . ucfirst($requestedDay ?? '') . " at {$requestedTimeFormatted} — closest sessions on {$suggestedDay}: {$timesOnSugDay}";
                } else {
                    $fallbackMessage = "No {$categoryLabel} on " . ucfirst($requestedDay ?? '') . " — showing closest sessions on {$suggestedDay}";
                }
            } else {
                $fallbackMessage = "No {$categoryLabel} on " . ucfirst($requestedDay ?? '') . " — showing closest sessions on {$suggestedDay}";
            }

        } elseif ($isTimeFallback) {
            $fallbackMessage = "No {$categoryLabel} at exactly {$requestedTimeFormatted} — showing nearest available sessions at {$suggestedTime}";
        }

        return response()->json([
            'html'                    => $this->renderActivities($activities),
            'count'                   => $activities->count(),
            'ai_summary'              => $aiResponse['summary'] ?? null,
            'parsed'                  => $aiResponse,
            'is_fallback'             => $isFallback,
            'fallback_city'           => $fallbackCity,
            'is_time_fallback'        => $isTimeFallback,
            'is_time_on_day_fallback' => $isTimeOnDayFallback,
            'is_day_fallback'         => $isDayFallback,
            'suggested_day'           => $suggestedDay,
            'suggested_time'          => $suggestedTime,
            'fallback_message'        => $fallbackMessage,
            'suggestions_html'        => '',
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

        if (!empty($aiResponse['level']))
            $dbQuery->where('level', $aiResponse['level']);

        // ── Day + time: combined in a SINGLE whereHas so they must match the same schedule record ──
        $day        = null;
        $days       = null;
        $timeParsed = null;

        if (!empty($aiResponse['days_of_week']) && is_array($aiResponse['days_of_week'])) {
            $days = array_map('strtolower', $aiResponse['days_of_week']);
        } elseif (!empty($aiResponse['day_of_week'])) {
            $day = strtolower($aiResponse['day_of_week']);
        }

        if (!empty($aiResponse['start_time'])) {
            $p = date('H:i:s', strtotime($aiResponse['start_time']));
            if ($p && $p !== '00:00:00') {
                $timeParsed = $p;
            }
        }

        if ($days !== null) {
            $dbQuery->whereHas('schedules', function ($q) use ($days, $timeParsed) {
                $q->whereIn('day_of_week', $days);
                if ($timeParsed) {
                    $q->whereTime('start_time', $timeParsed);
                }
            });
        } elseif ($day !== null) {
            $dbQuery->whereHas('schedules', function ($q) use ($day, $timeParsed) {
                $q->where('day_of_week', $day);
                if ($timeParsed) {
                    $q->whereTime('start_time', $timeParsed);
                }
            });
        } elseif ($timeParsed !== null) {
            $dbQuery->whereHas('schedules', fn($q) => $q->whereTime('start_time', $timeParsed));
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
13. Times like "1:00", "2:00" without AM/PM in a daytime context → assume PM (13:00, 14:00); early hours like "6:00", "7:00", "8:00" assume AM

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
