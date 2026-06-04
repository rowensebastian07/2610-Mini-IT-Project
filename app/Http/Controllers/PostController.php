<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Club;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;   
use Carbon\Carbon;
use App\Models\PostComment;
use App\Events\CommentPosted;

class PostController extends Controller
{
    private function authorizeCommittee(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized action. Only committee members can manage posts.');
        }
    }

    public function index()
    {
        $posts = Post::with('club')->latest()->get();
        return view('welcome', compact('posts'));
    }

    public function create(Club $club)
    {
        $this->authorizeCommittee($club);
        return view('posts.create', compact('club'));
    }

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

        // ✅ Create the post via relationship
        $post = $club->posts()->create($validated);

        // ✅ Notify ALL club members (including sender)
        foreach ($club->users as $member) {
            $member->notify(new ClubNotification(
                $club,
                "New Post in {$club->name}: {$post->title}",
                'post' // pass type so UI can show badge
            ));
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Post created and members notified!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorizeCommittee($post->club);
        return view('posts.edit', compact('post'));
    }

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

    public function destroy(Post $post)
    {
        $this->authorizeCommittee($post->club);
        $post->delete();

        return redirect()->route('clubs.show', $post->club->id)
                         ->with('success', 'Post deleted successfully!');
    }

public function like(Post $post)
{
    $userId = auth()->id();

    // Check if this user already liked
    $existing = $post->likes()->where('user_id', $userId)->first();

    if ($existing) {
        // Unlike
        $existing->delete();
    } else {
        // Like
        $post->likes()->create(['user_id' => $userId]);
    }

    return response()->json(['likes_count' => $post->likes()->count()]);
}


public function comment(Request $request, Post $post)
{
    $request->validate(['body' => 'required|string']);

    $comment = $post->comments()->create([
        'user_id' => auth()->id(),
        'body' => $request->body,
    ]);

    broadcast(new CommentPosted($comment))->toOthers();

    return response()->json($comment->load('user'));
}


public function getComments(Post $post)
{
    return response()->json(
        $post->comments()->with('user')->latest()->get()
    );
}


}
