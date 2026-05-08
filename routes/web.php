<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Event;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/


// Route::get('/clubs/search', [ClubController::class, 'apiSearch'])->name('clubs.api-search'); // global search bar

// Homepage – lists posts (and clubs if you want)
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/clubs/search', [ClubController::class, 'search'])->name('clubs.search');

// Navigation and Public Calendar
Route::get('/navigation', [ClubController::class, 'index'])->name('navigation');
Route::get('/calendar', function () {
    $events = Event::all(); 
    return view('calendar.index', compact('events'));
})->name('calendar.index');



// Club Details and Listing
Route::get('/clubs', [ClubController::class, 'list'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

// Follow/Unfollow Clubs
Route::post('/clubs/{club}/follow', [UserController::class, 'followClub'])->name('clubs.follow');
Route::delete('/clubs/{club}/unfollow', [UserController::class, 'unfollowClub'])->name('clubs.unfollow');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - shows profile + followed clubs/events
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::patch('/dashboard', [UserController::class, 'updateProfile'])->name('dashboard.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications Feed
    Route::get('/notifications', fn() => auth()->user()->notifications)->name('notifications.index');

    // Committee Club Management (Notifications)
    Route::get('/clubs/{club}/notify', [ClubController::class, 'showNotifyForm'])->name('clubs.notify.form');
    Route::post('/clubs/{club}/notify', [ClubController::class, 'sendUpdate'])->name('clubs.notify.send');

    // Posts Management
    Route::resource('posts', PostController::class)->except(['create', 'store']);
    Route::get('/clubs/{club}/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/clubs/{club}/posts', [PostController::class, 'store'])->name('posts.store');

    // Events Management
    Route::get('/clubs/{club}/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/clubs/{club}/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/clubs/{club}/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/clubs/{club}/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/clubs/{club}/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/clubs/{club}/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::patch('/clubs/{club}/events/{event}/passed', [EventController::class, 'markPassed'])
    ->name('events.markPassed');


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


