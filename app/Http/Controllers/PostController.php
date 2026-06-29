<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Club;
use App\Models\PostMedia;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PostComment;
use App\Events\CommentPosted;
use App\Models\Event;

class PostController extends Controller
{
public function index()
{
    try {
        // ✅ Fallback: empty array if user not logged in
        $clubIds = auth()->check()
            ? auth()->user()->clubs()->pluck('id')->toArray()
            : [];

        // ✅ Fetch posts
        $followedPosts = Post::with(['club', 'media', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->whereIn('club_id', $clubIds)
            ->latest()
            ->get();

        $followedClubs = Club::whereIn('id', $clubIds)
            ->with(['posts', 'events'])
            ->get();

        $otherPosts = Post::with(['club', 'media', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->whereNotIn('club_id', $clubIds)
            ->latest()
            ->get();

        // ✅ Combine all posts for Blade
        $posts = $followedPosts->merge($otherPosts);

        // ✅ Fetch all events for Blade
        $allEvents = Event::with('club')->latest()->get();
    } catch (\Exception $e) {
        // ✅ Fallback: empty collections if DB fails
        $followedPosts = collect();
        $followedClubs = collect();
        $otherPosts = collect();
        $posts = collect();
        $allEvents = collect();
    }

    // ✅ Pass everything to welcome.blade.php
    return view('welcome', compact('posts', 'followedPosts', 'followedClubs', 'otherPosts', 'allEvents'));
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
            //  Only allow images
            'media.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:51200',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['club_id'] = $club->id;

        // Create the post
        $post = Post::create($validated);

        // Handle multiple image uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                $post->media()->create([
                    'type' => 'image', // always image now
                    'path' => $path,
                ]);
            }
        }

        // Notify members
        foreach ($club->users as $member) {
            $member->notify(new ClubNotification(
                $club,
                "New Post in {$club->name}: {$post->title}",
                'post'
            ));
        }

        return redirect()->route('clubs.show', $club)
                         ->with('success', 'Post created with images!');
    }

    public function show(Post $post)
    {
        $post->load(['club', 'media', 'comments.user']); 
        return view('posts.show', compact('post'));
    }

    public function edit(Club $club, Post $post)
    {
        return view('posts.edit', compact('club', 'post'));
    }

    public function update(Request $request, Club $club, Post $post)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'media.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        $post->update($validated);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('posts', 'public');
                $post->media()->create([
                    'type' => 'image', 
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $club = $post->club;
        $post->delete();

        return redirect()->route('clubs.show', $club)
                         ->with('success', 'Post deleted successfully!');
    }

    // =========================================================================
    // Public/Interactive Interactions
    // =========================================================================
    public function like(Post $post)
    {
        $userId = auth()->id();
        $existing = $post->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $userId]);
            $liked = true;
        }

        $likesCount = $post->likes()->count();
        $likedUsers = $post->likes()->pluck('user_id')->toArray();

        $post->update([
            'likes_count' => $likesCount,
            'liked_users' => json_encode($likedUsers ?? []),
        ]);

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
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
