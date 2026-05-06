<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $initialCategories = $categories->take(8);
        $hasMoreCategories = $categories->count() > 8;
        $activitiesCount   = Activity::where('is_active', true)->count();
        $centersCount      = Center::where('is_active', true)->count();

        // Popular activities (default)
        $activities = Activity::with(['center', 'category', 'reviews', 'images', 'favourites'])
            ->where('is_active', true)
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(3)
            ->get();

        // ===== PERSONALIZED RECOMMENDATIONS =====
        $recommendations     = collect();
        $recommendationTitle = null;
        $recommendationLabel = null;

        if (Auth::check()) {
            $user = Auth::user();

            // 1. جمع الـ preferred category IDs من bookings وfavourites
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

            // Merge — bookings أعطيها priority أكتر
            $preferredCategoryIds = collect($bookedCategoryIds)
                ->merge($favCategoryIds)
                ->unique()
                ->take(3)
                ->toArray();

            if (!empty($preferredCategoryIds)) {
                // ابحث عن activities من نفس الـ categories
                // بس ما تبين activities حجزها المستخدم مسبقاً
                $bookedActivityIds = $user->bookings()
                    ->with('schedule.activity')
                    ->get()
                    ->pluck('schedule.activity.id')
                    ->filter()
                    ->toArray();

                $query = Activity::with(['center', 'category', 'reviews', 'images', 'favourites'])
                    ->where('is_active', true)
                    ->whereIn('category_id', $preferredCategoryIds)
                    ->whereNotIn('id', $bookedActivityIds)
                    ->withCount('bookings');

                // Location-based sorting إذا عنده location بالـ session
                $userLat = $request->session()->get('user_lat');
                $userLng = $request->session()->get('user_lng');

                $recs = $query->get();

                if ($userLat && $userLng) {
                    // Sort by distance
                    $recs = $recs->map(function ($act) use ($userLat, $userLng) {
                        $lat = $act->center->lat;
                        $lng = $act->center->lng;
                        if (!$lat || !$lng) { $act->distance = 999999; return $act; }
                        $dLat = deg2rad($lat - $userLat);
                        $dLng = deg2rad($lng - $userLng);
                        $a    = sin($dLat/2)**2 + cos(deg2rad($userLat)) * cos(deg2rad($lat)) * sin($dLng/2)**2;
                        $act->distance = 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
                        return $act;
                    })->sortBy('distance');
                } else {
                    // Sort by rating
                    $recs = $recs->sortByDesc(fn($act) => $act->reviews->avg('rating') ?? 0);
                }

                $recommendations = $recs->take(3);

                if ($recommendations->isNotEmpty()) {
                    $recommendationLabel = '✦ RECOMMENDED FOR YOU';
                    $recommendationTitle = '';
                }
            }
        }

        return view('home', compact(
            'categories',
            'initialCategories',
            'hasMoreCategories',
            'activities',
            'activitiesCount',
            'centersCount',
            'recommendations',
            'recommendationTitle',
            'recommendationLabel'
        ));
    }

    // Save user location to session (called via AJAX)
    public function saveLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        $request->session()->put('user_lat', $request->lat);
        $request->session()->put('user_lng', $request->lng);
        return response()->json(['success' => true]);
    }
}
