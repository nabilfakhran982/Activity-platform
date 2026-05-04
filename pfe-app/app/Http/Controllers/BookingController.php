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
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'status' => 'pending',
            'booking_date' => now()->toDateString(),
            'notes' => null,
        ]);

        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled']);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // ===== Send notification to user =====
        if ($request->status !== $oldStatus) {
            $activityTitle = $booking->schedule?->activity?->title ?? 'your activity';
            $centerName = $booking->schedule?->activity?->center?->name ?? 'the center';

            if ($request->status === 'confirmed') {
                \App\Models\Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'Booking Confirmed! 🎉',
                    'message' => "Your booking for \"{$activityTitle}\" at {$centerName} has been confirmed. See you there!",
                    'type' => 'success',
                    'icon' => 'check_circle',
                ]);
            } elseif ($request->status === 'cancelled') {
                \App\Models\Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'Booking Cancelled',
                    'message' => "Unfortunately, your booking for \"{$activityTitle}\" at {$centerName} has been cancelled.",
                    'type' => 'warning',
                    'icon' => 'cancel',
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // confirmed with review — ما يحذف
        if ($booking->status === 'confirmed' && $booking->review) {
            return response()->json(['error' => 'Cannot delete a reviewed booking.'], 422);
        }

        $booking->delete();
        return response()->json(['success' => true]);
    }
}
