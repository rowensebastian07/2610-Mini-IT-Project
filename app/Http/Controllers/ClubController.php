<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Post;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ClubController extends Controller
{
    // Homepage: list all clubs and posts
    public function index()
    {
        $clubs = Club::with('posts')->get();
        $posts = Post::with('club')->latest()->get();

        return view('index', compact('clubs', 'posts'));
    }

    // Navigation/list view
    public function list()
    {
        $clubs = Club::all();
        return view('navigation', compact('clubs'));
    }

    // Show a single club page
    public function show(Club $club)
    {
        $club->load(['posts', 'events']);
        return view('clubs.show', compact('club'));
    }

    public function edit($id)
    {
        $club = Club::findOrFail($id);
        return view('clubs.edit', compact('club'));
    }

    public function update(Request $request, $id)
    {
        $club = Club::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $filename = time() . '.' . $request->profile_picture->extension();
            $request->profile_picture->storeAs('public/images', $filename);
            $club->profile_picture = $filename;
        }

        $club->name = $request->name;
        $club->description = $request->description;
        $club->save();

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Club updated successfully!');
    }

    public function destroy($id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        return redirect()->route('clubs.index')
                         ->with('success', 'Club deleted successfully!');
    }

    /**
     * Show the form to send a notification.
     */
    public function showNotifyForm(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        return view('clubs.notify', compact('club'));
    }

    /**
     * Process and send the notification.
     */
    public function sendUpdate(Request $request, Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $messageContent = $request->input('message');
        $members = $club->users;

        foreach ($members as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification($club, $messageContent));
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('status', 'Notification sent to ' . ($members->count() - 1) . ' members!');
    }

   public function search(Request $request) {
    $query = $request->input('query');
    $clubs = Club::where('name','like',"%{$query}%")
                 ->orWhere('description','like',"%{$query}%")
                 ->with('events')
                 ->paginate(10);
    return view('clubs.search', compact('clubs','query'));
}   
    
}
