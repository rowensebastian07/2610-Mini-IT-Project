<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Post;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClubController extends Controller
{
    /**
     * Show the form to send a notification.
     */
    public function showNotifyForm(Club $club)
    {
        // Security check
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        return view('clubs.notify', compact('club'));
    }
    
    /**
     * Process and send the notification.
     */
    public function sendUpdate(Request $request, Club $club) // Fixed: Use Club $club for consistency
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

    // Temporary store function as we figure out how to reroute things

    public function store(Request $request,  \App\Models\Club $clubs)
    {

        

        $validated = $request->validate([
        'name'   => 'required|string|max:255',
        'category' => 'required',
        'profile_picture'   => 'required|image|max:2048',
        
        
          ]);
          

        if ($request->hasFile('profile_picture')) {
        $validated['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
         }
        else{
            $profile_picture = "images/1.png";
        }

        $validated['owner_id'] = Auth::id();

        Club::create($validated);

        return redirect()->route('navigation')
                        ->with('success', 'Club created successfully!');

        
    }

    public function index()
    {

        $clubs = Club::all(); // Better than empty logic
        return view('navigation', compact('clubs'));
    }

    public function create(\App\Models\Club $club)
    {
      return view('create-clubs.create', compact('club'));
    }


}