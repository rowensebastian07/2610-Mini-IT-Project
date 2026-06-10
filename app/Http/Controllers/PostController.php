<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Club;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;   
use Carbon\Carbon;
use App\Models\PostComment;
use App\Events\CommentPosted;

class PostController extends Controller
{
    public function index(Club $club)
    {
        
        $user = Auth::user();

        $clubIds = $user->followed_clubs ?? [];
        $posts = Post::with('club')->latest()->get();


        $followedPosts = Post::with('club')
            ->whereIn('club_id', $clubIds)
            ->latest()
            ->get();

        $otherPosts = Post::with('club')
            ->whereNotIn('club_id', $clubIds)
            ->latest()
            ->get();

            return view('welcome', [
            'clubIds' => $clubIds,
            'followedPosts' => $followedPosts,
            'otherPosts' => $otherPosts
        ],
        compact('posts')
        );

    }

    public function create(Club $club)
    {
        return view('posts.create', compact('club'));
    }

    public function store(Request $request, Club $club)
    {
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

        // Notify ALL club members (including sender)
        foreach ($club->users as $member) {
            $member->notify(new ClubNotification(
                $club,
                "New Post in {$club->name}: {$post->title}",
                'post'
            ));
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Post created and members notified!');
    }

    public function show(Post $post)
    {
        $club = $post->club;
        return view('posts.show', compact('post'));
    }

    // Updated signatures to accept Club context matching web.php nested resource map
    public function edit(Club $club, Post $post)
    {
        return view('posts.edit', compact('club', 'post'));
    }

    public function update(Request $request, Club $club, Post $post)
    {        
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

    public function destroy(Club $club, Post $post)
    {   
        $clubId = $post->club_id;

        $post->delete();

        return redirect()->route('clubs.show', $clubId)
                         ->with('success', 'Post deleted successfully!');
    }

    // =========================================================================
    // Public/Interactive Interactions
    // =========================================================================
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