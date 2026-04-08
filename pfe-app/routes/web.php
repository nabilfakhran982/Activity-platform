<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CenterController;

Route::get('/test', function () {
    abort(401);
});

// Web Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/activities', [ActivityController::class, 'index'])->name('activities');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');


Route::get('/for-centers', [CenterController::class, 'index'])->name('for-centers');

Route::middleware(['auth'])->group(function () {
    Route::post('/center-register', [CenterController::class, 'store'])->name('center.register');
});

Route::middleware(['auth', 'center_owner'])->group(function () {
    Route::get('/center/dashboard', [CenterController::class, 'dashboard'])->name('center.dashboard');
    Route::get('/center/{center}/activities', [CenterController::class, 'activities'])->name('center.activities');
    Route::get('/center/{center}/bookings', [CenterController::class, 'bookings'])->name('center.bookings');
});

// Protected routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
