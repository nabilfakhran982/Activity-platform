<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CenterController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();
        $usersCount = User::where('role', 'user')->count();
        return view('for-centers', compact('categoriesCount', 'usersCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'address'     => 'required|string',
            'city'        => 'required|string',
            'phone'       => 'nullable|string',
        ]);

        $user = User::find(Auth::id());

        Center::create([
            'user_id'     => $user->id,
            'name'        => $request->name,
            'description' => $request->description,
            'address'     => $request->address,
            'city'        => $request->city,
            'phone'       => $request->phone,
        ]);

        $user->update(['role' => 'center_owner']);

        return response()->json(['success' => true]);
    }

    public function dashboard()
    {
        $centers = Center::where('user_id', Auth::id())
            ->withCount('activities')
            ->get();

        $totalActivities = $centers->sum('activities_count');

        $totalBookings = \App\Models\Booking::whereHas('schedule.activity', function ($q) use ($centers) {
            $q->whereIn('center_id', $centers->pluck('id'));
        })->count();

        $pendingBookings = \App\Models\Booking::whereHas('schedule.activity', function ($q) use ($centers) {
            $q->whereIn('center_id', $centers->pluck('id'));
        })->where('status', 'pending')->count();

        return view('center.dashboard', compact('centers', 'totalActivities', 'totalBookings', 'pendingBookings'));
    }
}
