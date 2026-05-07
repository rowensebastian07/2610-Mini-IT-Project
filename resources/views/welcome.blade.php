@extends('layouts.app')
<x-top-nav />



@section('content')

<div class="sub-header">
    <h1>WELCOME TO MMU's CLUB & SOCIETY OFFICIAL WEBPAGE</h1>
</div>


<nav>
    @if (Route::has('login'))
        <div class="fixed top-0 right-0 p-6 text-right z-10">
            @auth
                
                <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900">Register</a>
                @endif
            @endauth
        </div>
    @endif
</nav>

<h2>Latest Posts</h2>

<!-- Posts feed -->
@forelse($posts as $post)
    <div class="post-card">
        <h3 class="post-title">{{ $post->title }}</h3>

        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
        @endif

        <p class="post-content">{{ $post->content }}</p>
        <small class="post-meta">Posted in: {{ $post->club->name }}</small>
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
