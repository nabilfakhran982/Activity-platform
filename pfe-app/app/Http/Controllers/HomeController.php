<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $activities = Activity::with(['center', 'category', 'reviews', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('home', compact('categories', 'activities'));
    }
}
