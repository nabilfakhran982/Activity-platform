<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\CreditService;

class ProfileController extends Controller
{
    protected CreditService $creditService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->creditService = new CreditService();
    }

    public function index()
    {
        $user = Auth::user()->load(['bookings.schedule.activity.center', 'bookings.schedule.activity.images', 'bookings.schedule.activity.category', 'bookings.review', 'favourites.activity.images', 'favourites.activity.category']);
        $userStats = $this->creditService->getStats($user);
        return view('profile', compact('user', 'userStats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json(['errors' => ['current_password' => ['Current password is incorrect']]], 422);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true]);
    }
}
