<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Dashboard: show only clubs/events the user follows.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $clubIds = $user->followed_clubs ?? [];

        $followedClubs = Club::whereIn('id', $clubIds)
            ->with(['posts', 'events'])
            ->get();

        $events = $followedClubs->pluck('events')->flatten();

        return view('dashboard', [
            'followedClubs' => $followedClubs,
            'events'        => $events,
        ]);
    }

    /**
     * Register/store new user with default mmu.png picture.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => Hash::make($data['password']),
            'profile_picture' => 'images/mmu.png', 
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome aboard!');
    }

    public function destroy(User $user)
{
    // Only allow deleting your own account
    if (Auth::id() !== $user->id) {
        return back()->with('error', 'You can only delete your own account.');
    }

    $user->delete(); // ✅ Hard delete (permanent removal)

    return redirect('/')->with('success', 'Your account has been deleted.');
}


    /**
     * Follow a club.
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
     * Unfollow a club.
     */
    public function unfollowClub(Club $club)
    {
        $user = Auth::user();
        $followed = $user->followed_clubs ?? [];

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
        } elseif (!$user->profile_picture) {
            // ✅ Fallback if user never had a picture
            $user->profile_picture = 'images/mmu.png';
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }

    /**
     * Search users by name/email.
     */
public function search(Request $request)
{
    $user = Auth::user();
    $q = $request->input('q');

    if (!$q) {
        return response()->json([
            'results' => [],
            'remaining' => 10
        ]);
    }

    // ✅ Only search registered members in users table
    $users = User::where('name', 'like', "%{$q}%")
        ->orWhere('email', 'like', "%{$q}%")
        ->limit(10)
        ->get();

    // If not logged in, skip counter
    if (!$user) {
        return response()->json([
            'results' => $users->map(fn($u) => [
                'id' => $u->id,
                'text' => $u->name . " (" . $u->email . ")"
            ]),
            'remaining' => 10
        ]);
    }

    // Count searches today
    $searchCount = DB::table('search_logs')
        ->where('user_id', $user->id)
        ->whereDate('created_at', Carbon::today())
        ->count();

    if ($searchCount >= 10) {
        return response()->json([
            'error' => 'Daily search limit reached (10).',
            'remaining' => 0,
            'results' => []
        ], 429);
    }

    // Log attempt
    DB::table('search_logs')->insert([
        'user_id' => $user->id,
        'query' => $q,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $remaining = max(0, 10 - ($searchCount + 1));

    return response()->json([
        'results' => $users->map(fn($u) => [
            'id' => $u->id,
            'text' => $u->name . " (" . $u->email . ")"
        ]),
        'remaining' => $remaining
    ]);
}

}