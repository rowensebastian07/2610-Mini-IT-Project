<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EventController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage - Lists clubs and posts
Route::get('/', [ClubController::class, 'index'])->name('home');

// Navigation and Public Calendar
Route::get('/navigation', function () {
    return view('navigation');
})->name('navigation');

Route::get('/calendar', function () {
    $events = Event::all();
    return view('calendar.index', compact('events'));
})->name('calendar.index');

// Club Details and Listing
Route::get('/clubs', [ClubController::class, 'list'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications Feed (JSON or View)
    Route::get('/notifications', function () {
    return auth()->user()->notifications; // Or return a view
        })->name('notifications.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
});

require __DIR__.'/auth.php';



Route::get('/navigation', function () {
    return view('navigation'); // loads navigation.blade.php
})->name('navigation');



// Rowen routes 
// Homepage → list clubs + posts
Route::get('/', [ClubController::class, 'index'])->name('home');

// Club detail
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

// Clubs list page (optional separate route)
Route::get('/clubs', [ClubController::class, 'list'])->name('clubs.index');

    // Posts Management
    // We use resource for edit/update/destroy, and manual routes for creation linked to a club
    Route::resource('posts', PostController::class)->except(['create', 'store']);
    Route::get('/clubs/{club}/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/clubs/{club}/posts', [PostController::class, 'store'])->name('posts.store');

    // Events Management
    Route::get('/clubs/{club}/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/clubs/{club}/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/clubs/{club}/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/clubs/{club}/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/clubs/{club}/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');


require __DIR__ . '/auth.php';