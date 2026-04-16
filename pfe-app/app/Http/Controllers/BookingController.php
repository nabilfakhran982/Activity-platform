<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request, Schedule $schedule)
    {
        // تأكد إنو ما حجز قبل
        $existing = Booking::where('user_id', Auth::id())
            ->where('schedule_id', $schedule->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You already booked this session.'], 422);
        }

        $booking = Booking::create([
            'user_id'     => Auth::id(),
            'schedule_id' => $schedule->id,
            'status'      => 'pending',
            'notes'       => null,
        ]);

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }
}
