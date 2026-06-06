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
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClubTermController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
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
    
    // Dashboard & Profile
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    $this->patch('/dashboard', [UserController::class, 'updateProfile'])->name('dashboard.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::post('/clubs/{club}/contact', [ClubController::class, 'updateContact'])->name('clubs.updateContact');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications Feed
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Notifications/Notify Logic
    Route::get('/clubs/{club}/notify', [ClubController::class, 'showNotifyForm'])->name('clubs.notify.form');
    Route::post('/clubs/{club}/notify', [ClubController::class, 'sendUpdate'])->name('clubs.notify.send');

    // Follow Logic
    Route::post('/clubs/{club}/follow', [UserController::class, 'followClub'])->name('clubs.follow');
    Route::delete('/clubs/{club}/unfollow', [UserController::class, 'unfollowClub'])->name('clubs.unfollow');
        
    // Club Creation Routes (Before Management Middleware)
    Route::get('/create-clubs', [ClubController::class, 'create'])->name('create-clubs.create');
    Route::post('/create-clubs', [ClubController::class, 'store'])->name('create-clubs.store');
    Route::get('/create-clubs/{club}/edit', [ClubController::class, 'edit'])->name('create-clubs.edit');

    // Likes + Comments
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
    Route::get('/posts/{post}/comments', [PostController::class, 'getComments']);
    Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');

    // Search & Invites
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/committee/search', [ClubController::class, 'searchCommittee'])->name('committee.search');
    Route::post('/clubs/{club}/invite/respond', [ClubController::class, 'respondToInvite'])->name('committee.invite.respond');

    // =========================================================================
    //  ISOLATED CLUB MANAGEMENT & AUTHORIZATION ROUTE GROUP
    // =========================================================================
    Route::middleware(['club.management'])->group(function () {
        
        // 1. Club Properties Management
        Route::get('/clubs/{club}/edit', [ClubController::class, 'edit'])->name('clubs.edit');
        Route::put('/clubs/{club}', [ClubController::class, 'update'])->name('clubs.update');
        Route::delete('/clubs/{club}', [ClubController::class, 'destroy'])->name('clubs.destroy');
        Route::put('/clubs/{club}/verify', [ClubController::class, 'updateVerify'])->name('clubs.updateVerify');

        // FIXED THEME ROUTE: Distinct URI path to avoid overriding core club edits
        Route::put('/clubs/{club}/theme', [ClubController::class, 'updateTheme'])->name('clubs.updateTheme');

        // 2. Events Creation and Modification
        Route::get('/clubs/{club}/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/clubs/{club}/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/clubs/{club}/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/clubs/{club}/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/clubs/{club}/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/upload-files', [EventController::class, 'uploadFiles'])->name('events.uploadFiles');
        Route::delete('/events/{event}/delete-photo', [EventController::class, 'deletePhoto'])->name('events.deletePhoto');

        // 3. Posts Creation and Modification
        Route::get('/clubs/{club}/posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/clubs/{club}/posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // FAQ Management
        Route::get('/clubs/{id}/faq/edit', [ClubController::class, 'faqEdit'])->name('clubs.faq.edit');
        Route::put('/clubs/{id}/faq', [ClubController::class, 'updateFaq'])->name('clubs.faq.update');

        // 4. Committee and Terms Assignments
        Route::get('/clubs/{club}/committee', [ClubController::class, 'committee'])->name('clubs.committee');
        Route::post('/clubs/{club}/committee', [ClubController::class, 'addCommitteeMember'])->name('clubs.committee.add');
        Route::delete('/clubs/{club}/committee/{id}', [ClubController::class, 'removeCommitteeMember'])->name('clubs.committee.remove');
        Route::put('/clubs/{club}/committee/{id}/update', [ClubController::class, 'updateCommitteeMember'])->name('clubs.committee.update');
        Route::post('/clubs/{club}/terms/assign', [ClubTermController::class, 'assignMember'])->name('clubs.terms.assign');
    });
    // =========================================================================

    // Non-Protected View fallbacks inside Dashboard context
    Route::get('/clubs/{club}/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/uploads', [EventController::class, 'viewUploads'])->name('events.viewUploads');

    // Club Chatroom Page
    Route::get('/clubs/{club}/chatroom', [ClubController::class, 'chatroom'])->name('clubs.chatroom');
    Route::post('/clubs/{club}/messages', [MessageController::class, 'store'])->name('clubs.messages.store');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Marketplace
    Route::get('/clubs/{club}/marketplace', [ProductController::class, 'index'])->name('clubs.marketplace');
    Route::get('/clubs/{club}/marketplace/admin', [ProductController::class, 'adminDashboard'])->name('marketplace.admin');
    Route::get('/clubs/{club}/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/clubs/{club}/products', [ProductController::class, 'store'])->name('products.store');
    Route::resource('products', ProductController::class)->except(['index', 'create', 'store']);
    Route::post('/products/{product}/soldout', [ProductController::class, 'markSoldOut'])->name('products.soldout');
    Route::post('/clubs/{club}/treasurer/update', [ProductController::class, 'updateTreasurer'])->name('treasurer.update');
    Route::resource('cart', CartController::class);
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/products/{product}/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/products/{product}/payment', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/products/{product}/sales', [ProductController::class, 'sales'])->name('products.sales');
    Route::post('/orders/{order}/verify', [OrderController::class, 'verify'])->name('orders.verify');
});

require __DIR__ . '/auth.php';