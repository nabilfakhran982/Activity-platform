<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CenterController extends Controller
{
    public function index()
    {
        $categoriesCount = Category::count();
        $usersCount = User::where('role', 'user')->count();
        return view('for-centers', compact('categoriesCount', 'usersCount'));
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect('/register');
        }
        return view('center-register');
    }
}
