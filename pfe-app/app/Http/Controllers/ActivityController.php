<?php

namespace App\Http\Controllers;

use App\Models\ActivityImage;
use App\Models\Center;
use Auth;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Schedule;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $cities = require app_path('Data/cities.php');

        $query = Activity::with(['center', 'category', 'reviews', 'images', 'favourites'])
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
            ->orderByDesc('updated_at')
            ->get();

        $categories = Category::all();

        return view('center.activities', compact('center', 'activities', 'categories'));
    }

    public function show(Activity $activity)
    {
        $activity->load(['center', 'category', 'schedules', 'images', 'reviews.user', 'favourites']);
        return view('activity', compact('activity'));
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
            'schedules.*.end_time' => 'nullable|after:schedules.*.start_time',
            'image' => 'nullable|image|max:5120',
            [
                'image.max' => 'Image size must not exceed 5MB.',
                'image.image' => 'The file must be an image (jpg, png, etc.).',
            ]
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
            $category = Category::find($request->category_id);
            $slug = $category ? $category->slug : 'activity';
            $filename = $slug . '-' . $activity->id . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/activities'), $filename);
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => 'images/activities/' . $filename,
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
            'schedules.*.day_of_week' => 'required_with:schedules.*|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedules.*.start_time' => 'required_with:schedules.*',
            'schedules.*.end_time' => 'required_with:schedules.*',
            'image' => 'nullable|image|max:5120',
            [
                'image.max' => 'The image size must not exceed 5MB.',
                'image.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg).',
            ]
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
                if (isset($schedule['day_of_week']) && isset($schedule['start_time']) && isset($schedule['end_time'])) {
                    Schedule::create([
                        'activity_id' => $activity->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                    ]);
                }
            }
        }

        // Image
        if ($request->hasFile('image')) {
            // امسح الصورة القديمة من الـ disk
            $oldImage = $activity->images()->first();
            if ($oldImage) {
                $oldPath = public_path($oldImage->image_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $activity->images()->delete();
            }

            $filename = $activity->category->slug . '-' . $activity->id . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/activities'), $filename);
            ActivityImage::create([
                'activity_id' => $activity->id,
                'image_path' => 'images/activities/' . $filename,
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

        // امسح الصورة من الـ disk
        $image = $activity->images()->first();
        if ($image) {
            $path = public_path($image->image_path);
            if (file_exists($path)) {
                unlink($path);
            }
        }

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
