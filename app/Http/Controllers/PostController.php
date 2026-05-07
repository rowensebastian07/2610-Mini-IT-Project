<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
   public function index()
{
    $posts = Post::with('club')->latest()->get();
    return view('welcome', compact('posts'));
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

        $club->posts()->create($validated);

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
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

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('clubs.show', $post->club->id)
                         ->with('success', 'Post deleted successfully!');
    }
}
