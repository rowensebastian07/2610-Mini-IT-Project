<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Club;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Helper method to verify if the user is a Committee Member of the specific club.
     * This prevents regular members from accessing create/edit/delete actions.
     */
    private function authorizeCommittee(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        /*
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized action. Only committee members can manage posts.');
        } */
    }

    /**
     * Display a listing of the posts on the homepage.
     */
    public function index()
    {
        $posts = Post::with('club')->latest()->get();
        return view('welcome', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(Club $club)
    {
        $this->authorizeCommittee($club);
        return view('posts.create', compact('club'));
    }

    /**
     * Store a newly created post in storage and notify members.
     */
    public function store(Request $request, Club $club)
    {
        $this->authorizeCommittee($club);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $validated['user_id'] = Auth::id();

        // Create the post via relationship
        $post = $club->posts()->create($validated);

        // Notify all club members except the author
        foreach ($club->users as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification(
                    $club, 
                    "New Post in {$club->name}: {$post->title}"
                ));
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Post created and members notified!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $this->authorizeCommittee($post->club);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorizeCommittee($post->club);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('clubs.show', $post->club->id)
                         ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorizeCommittee($post->club);
        
        $post->delete();

        return redirect()->route('clubs.show', $post->club->id)
                         ->with('success', 'Post deleted successfully!');
    }
}