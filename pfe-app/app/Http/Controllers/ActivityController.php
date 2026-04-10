<?php

namespace App\Http\Controllers;

use App\Models\ActivityImage;
use App\Models\Center;
use Auth;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Category;
use Schedule;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $cities = require app_path('Data/cities.php');

        $query = Activity::with(['center', 'category', 'reviews', 'images'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('age')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('min_age')->orWhere('min_age', '<=', $request->age);
            })->where(function ($q) use ($request) {
                $q->whereNull('max_age')->orWhere('max_age', '>=', $request->age);
            });
        }

        if ($request->filled('city')) {
            $query->whereHas('center', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->filled('address')) {
            $query->whereHas('center', function ($q) use ($request) {
                $q->where('address', 'like', '%' . $request->address . '%');
            });
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $activities = $query->get();

        return view('activities', compact('activities', 'categories', 'cities'));
    }

    public function activities(Center $center)
    {
        if ($center->user_id !== Auth::id()) {
            abort(403);
        }

        $activities = Activity::with(['category', 'schedules', 'images'])
            ->where('center_id', $center->id)
            ->get();

        $categories = Category::all();

        return view('center.activities', compact('center', 'activities', 'categories'));
    }

    public function store(Request $request, Center $center)
    {
        if ($center->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'is_private' => 'boolean',
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.start_time' => 'required',
            'schedules.*.end_time' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $activity = Activity::create([
            'center_id' => $center->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'level' => $request->level,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'is_private' => $request->boolean('is_private'),
            'is_active' => true,
        ]);

        // Schedules
        if ($request->schedules) {
            foreach ($request->schedules as $schedule) {
                Schedule::create([
                    'activity_id' => $activity->id,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }
        }

        // Image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('activities', 'public');
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'activity' => $activity->load(['category', 'schedules', 'images']),
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->center->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'min_age' => 'nullable|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'is_private' => 'boolean',
            'schedules' => 'nullable|array',
            'image' => 'nullable|image|max:2048',
        ]);

        $activity->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'level' => $request->level,
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'is_private' => $request->boolean('is_private'),
        ]);

        // Schedules — امسح القديمة وحط الجديدة
        if ($request->schedules) {
            $activity->schedules()->delete();
            foreach ($request->schedules as $schedule) {
                Schedule::create([
                    'activity_id' => $activity->id,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                ]);
            }
        }

        // Image
        if ($request->hasFile('image')) {
            $activity->images()->delete();
            $path = $request->file('image')->store('activities', 'public');
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => $path,
            ]);
        }

        return response()->json([
            'success' => true,
            'activity' => $activity->fresh(['category', 'schedules', 'images']),
        ]);
    }

    public function destroy(Activity $activity)
    {
        if ($activity->center->user_id !== Auth::id())
            abort(403);
        $activity->schedules()->delete();
        $activity->images()->delete();
        $activity->delete();
        return response()->json(['success' => true]);
    }

    public function toggleActive(Activity $activity)
    {
        if ($activity->center->user_id !== Auth::id())
            abort(403);
        $activity->update(['is_active' => !$activity->is_active]);
        return response()->json(['is_active' => $activity->is_active]);
    }
}
