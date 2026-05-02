<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Club;
use App\Notifications\ClubNotification;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'club_id' => 'required|exists:clubs,id',
        ]);

        $club = Club::findOrFail($request->club_id);

        // AUTHORIZATION
        // Checks if the logged-in user is a committee member of THIS specific club
        if (!$club->members()->where('user_id', Auth::id())->where('role', 'committee')->exists()) {
            abort(403, 'Unauthorized: Only committee members can post updates.');
        }

        // Create the Post
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'club_id' => $club->id,
            'user_id' => Auth::id(),
        ]);

        // NOTIFICATION
        // Fetch only members of this club to notify them
        $members = $club->members;
        foreach ($members as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification($club, "New Post: " . $post->title));
            }
        }

        return redirect()->back()->with('success', 'Post created and members notified!');
    }
}
