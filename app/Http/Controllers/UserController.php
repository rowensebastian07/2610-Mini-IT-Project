<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Club;

class UserController extends Controller
{
    /**
     * Follow a club (add club ID to user's followed_clubs JSON).
     */
    public function followClub(Club $club)
    {
        $user = Auth::user();
        $followed = $user->followed_clubs ?? [];

        if (!in_array($club->id, $followed)) {
            $followed[] = $club->id;
            $user->followed_clubs = $followed;
            $user->save();
        }

        return back()->with('success', 'You are now following this club.');
    }

    /**
     * Unfollow a club (remove club ID from user's followed_clubs JSON).
     */
    public function unfollowClub(Club $club)
    {
        $user = Auth::user();
        $followed = $user->followed_clubs ?? [];

        $user->followed_clubs = array_values(array_diff($followed, [$club->id]));
        $user->save();

        return back()->with('success', 'You unfollowed this club.');
    }

    /**
     * Dashboard: show only clubs/events the user follows.
     */
    public function dashboard()
    {
        $user = auth()->user();

        // Get IDs from the JSON column
        $clubIds = $user->followed_clubs ?? [];

        // Only fetch clubs the user follows
        $followedClubs = Club::whereIn('id', $clubIds)
            ->with(['posts', 'events'])
            ->get();

        // Flatten events from followed clubs
        $events = $followedClubs->pluck('events')->flatten();

        return view('dashboard', [
            'followedClubs' => $followedClubs,
            'events'        => $events,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

    $user->save();

    return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');

    }
}
