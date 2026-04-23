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

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentBookings'));
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
