<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CenterController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();
        $cities = require app_path('Data/cities.php');
        $usersCount = User::where('role', 'user')->count();
        return view('for-centers', compact('categoriesCount', 'usersCount', 'cities'));
    }

    public function store(Request $request)
    {

        $cities = require app_path('Data/cities.php');

        $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'address' => 'required|string',
                'city' => [
                    'required',
                    'string',
                    Rule::in($cities),
                ],
                'phone' => 'nullable|string',
            ],
            [
                'city.in' => 'Please select a valid city from the list.',
            ]
        );

        $user = User::find(Auth::id());

        $center = Center::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'phone' => $request->phone,
            'is_active' => true,
            'lat' => $request->lat ?: null,
            'lng' => $request->lng ?: null,
        ]);

        $user->update(['role' => 'center_owner']);

        return response()->json([
            'success' => true,
            'center' => $center->load('user') // Load the user relationship if needed
        ]);
    }

    public function dashboard()
    {
        $centers = Center::where('user_id', Auth::id())
            ->withCount('activities')
            ->orderByDesc('updated_at')
            ->get();

        $centerIds = $centers->pluck('id');

        $totalActivities = $centers->sum('activities_count');

        $totalBookings = Booking::whereHas('schedule.activity', function ($q) use ($centerIds) {
            $q->whereIn('center_id', $centerIds);
        })->count();

        $pendingBookings = Booking::whereHas('schedule.activity', function ($q) use ($centerIds) {
            $q->whereIn('center_id', $centerIds);
        })->where('status', 'pending')->count();

        // ===== CHART DATA =====

        // 1. Bookings per month (last 6 months)
        $bookingsPerMonth = Booking::whereHas('schedule.activity', function ($q) use ($centerIds) {
            $q->whereIn('center_id', $centerIds);
        })
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn($b) => $b->created_at->format('M Y'))
            ->map(fn($group) => $group->count());

        // Fill missing months with 0
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $months[$label] = $bookingsPerMonth[$label] ?? 0;
        }

        // 2. Most popular activities (top 5 by bookings)
        $popularActivities = Booking::selectRaw('schedules.activity_id, COUNT(*) as total')
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->join('activities', 'schedules.activity_id', '=', 'activities.id')
            ->whereIn('activities.center_id', $centerIds)
            ->groupBy('schedules.activity_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('schedule.activity')
            ->get()
            ->map(function ($booking) {
                $activity = \App\Models\Activity::find($booking->activity_id);
                return [
                    'title' => $activity?->title ?? 'Unknown',
                    'total' => $booking->total,
                ];
            });

        // 3. Revenue per month (last 6 months)
        $revenuePerMonth = Booking::whereHas('schedule.activity', function ($q) use ($centerIds) {
            $q->whereIn('center_id', $centerIds);
        })
            ->where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->with('schedule.activity')
            ->get()
            ->groupBy(fn($b) => $b->created_at->format('M Y'))
            ->map(fn($group) => $group->sum(fn($b) => $b->schedule?->activity?->price ?? 0));

        $revenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $revenue[$label] = round($revenuePerMonth[$label] ?? 0, 2);
        }

        return view('center.dashboard', compact(
            'centers',
            'totalActivities',
            'totalBookings',
            'pendingBookings',
            'months',
            'popularActivities',
            'revenue'
        ));
    }
    public function update(Request $request, Center $center)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        $center->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'phone' => $request->phone,
            'lat' => $request->lat ?: null,
            'lng' => $request->lng ?: null,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Center $center)
    {
        $center->delete();
        return response()->json(['success' => true]);
    }

    public function toggleActive(Center $center)
    {
        $center->update(['is_active' => !$center->is_active]);
        return response()->json(['is_active' => $center->is_active]);
    }

    public function bookings(Center $center)
    {
        $bookings = Booking::whereHas(
            'schedule.activity',
            fn($q) =>
            $q->where('center_id', $center->id)
        )->with(['schedule.activity', 'user', 'review'])
            ->latest()
            ->get();

        return view('center.bookings', compact('center', 'bookings'));
    }
}
