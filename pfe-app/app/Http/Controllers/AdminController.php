<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\Center;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'centers' => Center::count(),
            'activities' => Activity::count(),
            'bookings' => Booking::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentBookings = Booking::with(['user', 'schedule.activity.center'])->latest()->take(5)->get();

        // ===== CHART DATA =====

        // 1. Bookings per month (last 6 months)
        $bookingsRaw = Booking::where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn($b) => $b->created_at->format('M Y'))
            ->map(fn($group) => $group->count());

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $months[$label] = $bookingsRaw[$label] ?? 0;
        }

        // 2. Most popular categories (top 5)
        $popularCategories = Booking::selectRaw('categories.name as category, COUNT(*) as total')
            ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
            ->join('activities', 'schedules.activity_id', '=', 'activities.id')
            ->join('categories', 'activities.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 3. Revenue per month (confirmed bookings, last 6 months)
        $revenueRaw = Booking::where('status', 'confirmed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->with('schedule.activity')
            ->get()
            ->groupBy(fn($b) => $b->created_at->format('M Y'))
            ->map(fn($group) => $group->sum(fn($b) => $b->schedule?->activity?->price ?? 0));

        $revenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M Y');
            $revenue[$label] = round($revenueRaw[$label] ?? 0, 2);
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentBookings',
            'months',
            'popularCategories',
            'revenue'
        ));
    }

    // ===== USERS =====
    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['error' => 'Cannot deactivate admin'], 403);
        }
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['is_active' => $user->is_active]);
    }
    public function destroyUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['error' => 'Cannot delete admin'], 403);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    // ===== CENTERS =====
    public function centers()
    {
        $centers = Center::with('user')->withCount('activities')->latest()->paginate(20);
        return view('admin.centers', compact('centers'));
    }

    public function toggleCenter(Center $center)
    {
        $center->update(['is_active' => !$center->is_active]);
        return response()->json(['is_active' => $center->is_active]);
    }

    public function destroyCenter(Center $center)
    {
        $center->delete();
        return response()->json(['success' => true]);
    }

    // ===== ACTIVITIES =====
    public function activities()
    {
        $activities = Activity::with(['center', 'category'])->latest()->paginate(20);
        return view('admin.activities', compact('activities'));
    }

    public function toggleActive(Activity $activity)
    {
        $activity->update(['is_active' => !$activity->is_active]);
        return response()->json(['is_active' => $activity->is_active]);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(['success' => true]);
    }

    // ===== BOOKINGS =====
    public function bookings()
    {
        $bookings = Booking::with(['user', 'schedule.activity.center'])->latest()->get();
        $pending = $bookings->where('status', 'pending');
        $confirmed = $bookings->where('status', 'confirmed');
        $cancelled = $bookings->where('status', 'cancelled');

        return view('admin.bookings', compact('bookings', 'pending', 'confirmed', 'cancelled'));
    }

    // ===== REVIEWS =====
    public function reviews()
    {
        $reviews = Review::with(['user', 'booking.schedule.activity.center'])->latest()->paginate(20);
        return view('admin.reviews', compact('reviews'));
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return response()->json(['success' => true]);
    }

    // ===== PROFILE =====
    public function profile()
    {
        return view('admin.profile');
    }


}
