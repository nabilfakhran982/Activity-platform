<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Category;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Activity::with(['center', 'category', 'reviews', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by age
        if ($request->filled('age')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('min_age')
                    ->orWhere('min_age', '<=', $request->age);
            })->where(function ($q) use ($request) {
                $q->whereNull('max_age')
                    ->orWhere('max_age', '>=', $request->age);
            });
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->whereHas('center', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $activities = $query->get();

        return view('activities', compact('activities', 'categories'));
    }
}
