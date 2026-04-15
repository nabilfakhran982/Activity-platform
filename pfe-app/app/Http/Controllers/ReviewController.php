<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        // تأكد إنو الـ booking تبعه
        if ($booking->user_id !== Auth::id()) abort(403);

        // تأكد إنو ما في review قديمة
        if ($booking->review) {
            return response()->json(['error' => 'Already reviewed'], 422);
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $review = Review::create([
            'user_id'     => Auth::id(),
            'activity_id' => $booking->schedule->activity_id,
            'booking_id'  => $booking->id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
        ]);

        return response()->json(['success' => true, 'review' => $review]);
    }
}
