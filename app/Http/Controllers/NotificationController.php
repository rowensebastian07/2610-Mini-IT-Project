<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ✅ Show all notifications for the logged-in user
   public function index()
{
    $notifications = Auth::user()->notifications;
    return view('clubs.notify', compact('notifications'));
}


    // ✅ Mark a single notification as read
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    // ✅ Mark all notifications as read
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    // ✅ Delete a notification
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
