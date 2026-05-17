@extends('layouts.app')
<x-top-nav />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@section('content')

<div class="sub-header">
    <h1>WELCOME TO MMU's CLUB & SOCIETY OFFICIAL WEBPAGE</h1>
</div>

<h2>Latest Posts</h2>

<!-- Posts feed -->
@forelse($posts as $post)
    <div class="post-card" style="position:relative; padding-bottom:40px;">
        <h3 class="post-title">{{ $post->title }}</h3>

        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
        @endif

        <p class="post-content">{{ $post->content }}</p>
        <small class="post-meta">Posted in: {{ $post->club->name }}</small>

                <!-- Likes & Comments in bottom corner -->
                <div style="position:absolute; bottom:10px; right:10px; display:flex; gap:15px;">
            <!-- Like button with animated heart -->
            <form method="POST" action="{{ route('posts.like', $post->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="like-btn">
                    <i class="fas fa-heart"></i> {{ $post->likes_count }}
                </button>
            </form>

            <!-- Comment count -->
            <a href="{{ route('posts.show', $post->id) }}" class="comment-btn">
                <i class="fas fa-comment"></i> {{ $post->comments_count }}
            </a>
        </div>

    </div>
@empty
    <p>No posts yet.</p>
@endforelse


<!-- Scroll-to-top button -->
<button id="scrollTopBtn" class="hidden">
    Latest Post <span class="arrow"></span>
</button>


@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollBtn = document.getElementById("scrollTopBtn");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 300) {
            scrollBtn.classList.remove("hidden");
            scrollBtn.style.animation = "bounce 1.5s infinite";
        } else {
            scrollBtn.classList.add("hidden");
            scrollBtn.style.animation = "none";
        }
    });

    scrollBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
});
</script>
@endpush