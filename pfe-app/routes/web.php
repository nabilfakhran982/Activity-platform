<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CenterController;

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

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::post('/search', [SearchController::class, 'search'])->name('search.query');

Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activity.show');
Route::post('/schedule/{schedule}/book', [BookingController::class, 'store'])->name('booking.store');

Route::post('/booking/{booking}/status', [BookingController::class, 'updateStatus'])->middleware(['auth', 'center_owner']);
Route::delete('/booking/{booking}/delete', [BookingController::class, 'destroy'])->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::post('/activity/{activity}/favourite', [FavouriteController::class, 'toggle'])->name('activity.favourite');
    Route::post('/booking/{booking}/review', [ReviewController::class, 'store'])->name('booking.review');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/center-register', [CenterController::class, 'store'])->name('center.register');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read']);
});

Route::middleware(['auth', 'center_owner'])->group(function () {
    Route::get('/center/dashboard', [CenterController::class, 'dashboard'])->name('center.dashboard');
    Route::post('/center/{center}/toggle-active', [CenterController::class, 'toggleActive'])->name('center.toggle-active');
    Route::post('/center/{center}/update', [CenterController::class, 'update'])->name('center.update');
    Route::delete('/center/{center}/delete', [CenterController::class, 'destroy'])->name('center.destroy');
    Route::get('/center/{center}/bookings', [CenterController::class, 'bookings'])->name('center.bookings');

    Route::get('/center/{center}/activities', [ActivityController::class, 'activities'])->name('center.activities');
    Route::post('/center/{center}/activities', [ActivityController::class, 'store'])->name('center.activities.store');
    Route::post('/activity/{activity}/update', [ActivityController::class, 'update'])->name('activity.update');
    Route::delete('/activity/{activity}/delete', [ActivityController::class, 'destroy'])->name('activity.destroy');
    Route::post('/activity/{activity}/toggle-active', [ActivityController::class, 'toggleActive'])->name('activity.toggle-active');

    Route::get('/center/{center}/bookings', [CenterController::class, 'bookings'])
        ->name('center.bookings');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/centers', [AdminController::class, 'centers'])->name('centers');
    Route::post('/centers/{center}/toggle', [AdminController::class, 'toggleCenter'])->name('centers.toggle');
    Route::delete('/centers/{center}', [AdminController::class, 'destroyCenter'])->name('centers.destroy');
    Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
    Route::delete('/activity/{activity}/delete', [AdminController::class, 'destroy'])->name('activity.destroy');
    Route::post('/activity/{activity}/toggle-active', [AdminController::class, 'toggleActive'])->name('activity.toggle-active');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
});
// Protected routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__ . '/auth.php';
