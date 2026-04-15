<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function toggle(Activity $activity)
    {
        $existing = Favourite::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        Favourite::create([
            'user_id'     => Auth::id(),
            'activity_id' => $activity->id,
        ]);

        return response()->json(['saved' => true]);
    }

    public function check(Activity $activity)
    {
        $saved = Favourite::where('user_id', Auth::id())
            ->where('activity_id', $activity->id)
            ->exists();

        return response()->json(['saved' => $saved]);
    }
}
