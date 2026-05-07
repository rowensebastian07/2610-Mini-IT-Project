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
Route::get('/', function () {
    return view('welcome');
});


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
        return auth()->user()->notifications;
    })->name('notifications.index');

    // Committee Club Management (Notifications)
    // FIXED: Changed {id} to {club} to match EventController pattern
    Route::get('/clubs/{club}/notify', [ClubController::class, 'showNotifyForm'])->name('clubs.notify.form');
    Route::post('/clubs/{club}/notify', [ClubController::class, 'sendUpdate'])->name('clubs.notify.send');

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
});



// Routes for creating club
Route::resource('clubs', ClubController::class)->except(['create', 'store']);

// Nested post routes under clubs (create + store)
Route::get('/create-clubs', [ClubController::class, 'create'])->name('create-clubs.create');
Route::post('/create-clubs', [ClubController::class, 'store'])->name('create-clubs.store');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});


require __DIR__ . '/auth.php';