<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{   
    /*send an update to all members of a specific club. */
    public function sendUpdate(Request $request, $id)
    {
        // 1. SECURITY CHECK: Don't forget this!
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        // 2. VALIDATION: Ensure the message isn't empty
        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $messageContent = $request->input('message');

        // 3. GET MEMBERS: Use the relationship defined in your Club model
        // Note: Make sure your Club model has a 'users' or 'members' relationship
        $members = $club->users; 

        // 4. SEND NOTIFICATION: Exclude the sender so they don't notify themselves
        foreach ($members as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification($club, $messageContent));
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('status', 'Notification sent to ' . ($members->count() - 1) . ' members!');
    }

    public function index()
    {
        $clubs = Club::all(); // Better than empty logic
        return view('welcome', compact('clubs'));
    }
}