<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Center;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $activities = Activity::with(['center', 'category', 'reviews', 'images', 'favourites'])
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();


        $activitiesCount = Activity::where('is_active', true)->count();
        $centersCount = Center::where('is_active', true)->count();

        return view('home', compact('categories', 'activities', 'activitiesCount', 'centersCount'));
    }
}
