<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Dashboard: show only clubs/events the user follows.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get IDs from the JSON column
        $clubIds = $user->followed_clubs ?? [];

        // Fetch clubs the user follows along with their posts and events
        $followedClubs = Club::whereIn('id', $clubIds)
            ->with(['posts', 'events'])
            ->get();

        // Flatten events from followed clubs to show in a single list/calendar view
        $events = $followedClubs->pluck('events')->flatten();

        return view('dashboard', [
            'followedClubs' => $followedClubs,
            'events'        => $events,
        ]);
    }

    /**
     * Follow a club (add club ID to user's followed_clubs JSON array).
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

        return back()->with('success', 'You are now following ' . $club->name . '.');
    }

    /**
     * Unfollow a club (remove club ID from user's followed_clubs JSON array).
     */
    public function unfollowClub(Club $club)
    {
        $user = Auth::user();
        $followed = $user->followed_clubs ?? [];

        // Remove the ID and reset array keys
        $user->followed_clubs = array_values(array_diff($followed, [$club->id]));
        $user->save();

        return back()->with('success', 'You unfollowed ' . $club->name . '.');
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
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

    public function search(Request $request)
{
    $q = $request->input('q');
    $users = \App\Models\User::where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->limit(10)
                ->get(['id','name','email']);

    return response()->json($users);
}

}