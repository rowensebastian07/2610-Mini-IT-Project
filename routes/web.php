<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Event;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

/* 
Keep public viewing routes outside auth
Keep edit/update/delete inside auth
*/ 

// Homepage – lists posts (and clubs if you want)
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/calendar', function () {
    $events = Event::all(); 
    return view('calendar.index', compact('events'));
})->name('calendar.index');

Route::get('/clubs/search', [ClubController::class, 'search'])->name('clubs.search');
Route::get('/clubs', [ClubController::class, 'list'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/clubs/{id}/faq', [ClubController::class, 'faqView'])->name('clubs.faq.view');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - shows profile + followed clubs/events
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::patch('/dashboard', [UserController::class, 'updateProfile'])->name('dashboard.update');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])
    ->name('users.destroy');

    //FAQ SECTION
    Route::post('/clubs/{club}/contact', [ClubController::class, 'updateContact'])
    ->name('clubs.updateContact');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications Feed
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/{id}/read', function ($id) {
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->markAsRead(); // sets read_at timestamp
    return response()->json(['success' => true]);
    
});


    // Notifications/Notify Logic
    Route::get('/clubs/{club}/notify', [ClubController::class, 'showNotifyForm'])->name('clubs.notify.form');
    Route::post('/clubs/{club}/notify', [ClubController::class, 'sendUpdate'])->name('clubs.notify.send');

    // Follow Logic
    Route::post('/clubs/{club}/follow', [UserController::class, 'followClub'])->name('clubs.follow');
    Route::delete('/clubs/{club}/unfollow', [UserController::class, 'unfollowClub'])->name('clubs.unfollow');
        
    // Nested post routes under clubs (create + store)
    Route::get('/create-clubs', [ClubController::class, 'create'])->name('create-clubs.create');
    Route::post('/create-clubs', [ClubController::class, 'store'])->name('create-clubs.store');

    Route::resource('clubs', ClubController::class)->except(['create', 'store']);

    // Events Management
    Route::get('/clubs/{club}/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/clubs/{club}/events', [EventController::class, 'store'])->name('events.store');

    Route::get('/clubs/{club}/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/clubs/{club}/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');

    Route::put('/clubs/{club}/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/clubs/{club}/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/upload-files', [EventController::class, 'uploadFiles'])->name('events.uploadFiles');
    Route::get('/events/{event}/uploads', [EventController::class, 'viewUploads'])
     ->name('events.viewUploads');
     // Delete a single photo from an event
    Route::delete('/events/{event}/delete-photo', [EventController::class, 'deletePhoto'])
     ->name('events.deletePhoto');


// Posts nested under clubs
Route::get('/clubs/{club}/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/clubs/{club}/posts', [PostController::class, 'store'])->name('posts.store');

// Other post routes
Route::resource('posts', PostController::class)->except(['create', 'store']);

// Likes + Comments
Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
Route::get('/posts/{post}/comments', [PostController::class, 'getComments']);
Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');




    // Route for create clubs
    Route::get('/create-clubs', [ClubController::class, 'create'])->name('create-clubs.create');
    Route::post('/create-clubs', [ClubController::class, 'store'])->name('create-clubs.store');

    // Route for updating club details

    Route::put('/clubs/{club}', [ClubController::class, 'update'])
    ->name('clubs.update');

    // Route for verifying clubs
    Route::put('/clubs/{club}/verify', [ClubController::class, 'updateVerify'])
    ->name('clubs.updateVerify');


    // Route for edit club // huh? -lzh
    Route::get('/create-clubs/{club}/edit', [ClubController::class, 'edit'])->name('create-clubs.edit');

//Committee page 
Route::get('/clubs/{club}/committee', [ClubController::class, 'committee'])->name('clubs.committee');
Route::post('/clubs/{club}/committee', [ClubController::class, 'addCommitteeMember'])->name('clubs.committee.add');
Route::delete('/clubs/{club}/committee/{id}', [ClubController::class, 'removeCommitteeMember'])->name('clubs.committee.remove');
Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
Route::get('/committee/search', [ClubController::class, 'searchCommittee'])->name('committee.search');
Route::post('/clubs/{club}/invite/respond', [ClubController::class, 'respondToInvite'])
     ->name('committee.invite.respond');
Route::put('/clubs/{club}/committee/{id}/update', [App\Http\Controllers\ClubController::class, 'updateCommitteeMember'])
    ->name('clubs.committee.update');

 //Club Chatroom Page
Route::get('/clubs/{club}/chatroom', [App\Http\Controllers\ClubController::class, 'chatroom'])
     ->name('clubs.chatroom');
     Route::post('/clubs/{club}/messages', [MessageController::class, 'store'])
    ->name('clubs.messages.store');
    Route::put('/messages/{message}', [App\Http\Controllers\MessageController::class, 'update'])
    ->name('messages.update');
 Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy']) ->name('messages.destroy');

    

});


require __DIR__ . '/auth.php';