@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@section('content')

<div class="sub-header">
    <h1>WELCOME TO MMU's CLUB & SOCIETY OFFICIAL WEBPAGE</h1>
</div>

@if (auth()->user())
    <h2>Followed Clubs</h2>
        @forelse($followedPosts as $post)
            <div class="post-card" style="position:relative; padding-bottom:40px;">
                <h3 class="post-title">{{ $post->title }}</h3>

                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
                @endif

                <p class="post-content">{{ $post->content }}</p>
                <small class="post-meta">Posted in: {{ $post->club->name }}</small>

                <!-- Likes & Comments -->
                <div style="position:absolute; bottom:10px; right:10px; display:flex; gap:15px;">
                    <button type="button" class="like-btn {{ $post->likedByUser ? 'liked' : '' }}" data-id="{{ $post->id }}">
                        <i class="{{ $post->likedByUser ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                        <span id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
                    </button>

                    <button type="button" class="comment-toggle" data-id="{{ $post->id }}">
                        <i class="fa-regular fa-comment"></i> 
                        <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                    </button>
                </div>
            </div>
        @empty
            @if($clubIds == null)
            <div class="null-div">
                <h3 class="null-h3">No followed clubs yet? Why don't you discover some new clubs to keep up to date with?</h3>
                <a href="{{ url('/clubs') }}" class="discover-btn">
                    <span class="discover-span"></span>
                    <span class="discover-span"></span>
                    <p class="discover-text">Find New Clubs</p>
                </a>
            </div>
            @else
                <p>No posts yet.</p>
            @endif
        @endforelse

        <!-- Floating mini comment popup -->
        <div id="commentPopup" class="comment-popup" style="display:none;">
            <div class="popup-content">
                <span class="close">&times;</span>
                <div id="popupComments" class="popup-comments"></div>
                <form id="popupForm">
                    @csrf
                    <textarea name="body" placeholder="Write a comment..." required></textarea>
                    <button type="submit">Post</button>
                </form>
            </div>
        </div>


    <h2>Other Clubs</h2>
        @forelse($otherPosts as $post)
        
        <div class="post-card" style="position:relative; padding-bottom:40px;">
            <h3 class="post-title">{{ $post->title }}</h3>

            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
            @endif

            <p class="post-content">{{ $post->content }}</p>
            <small class="post-meta">Posted in: {{ $post->club->name }}</small>

            <!-- Likes & Comments -->
            <div style="position:absolute; bottom:10px; right:10px; display:flex; gap:15px;">
                <button type="button" class="like-btn {{ $post->likedByUser ? 'liked' : '' }}" data-id="{{ $post->id }}">
                    <i class="{{ $post->likedByUser ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                    <span id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
                </button>

                <button type="button" class="comment-toggle" data-id="{{ $post->id }}">
                    <i class="fa-regular fa-comment"></i> 
                    <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
                </button>
            </div>
        </div>
    @empty
        <p>No posts yet.</p>
    @endforelse

    <!-- Floating mini comment popup -->
    <div id="commentPopup" class="comment-popup" style="display:none;">
        <div class="popup-content">
            <span class="close">&times;</span>
            <div id="popupComments" class="popup-comments"></div>
            <form id="popupForm">
                @csrf
                <textarea name="body" placeholder="Write a comment..." required></textarea>
                <button type="submit">Post</button>
            </form>
        </div>
    </div>

@else

<h2>Latest Posts</h2>

    @forelse($posts as $post)
    <div class="post-card" style="position:relative; padding-bottom:40px;">
        <h3 class="post-title">{{ $post->title }}</h3>

        @if($post->image)
            <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
        @endif

        <p class="post-content">{{ $post->content }}</p>
        <small class="post-meta">Posted in: {{ $post->club->name }}</small>

        <!-- Likes & Comments -->
        <div style="position:absolute; bottom:10px; right:10px; display:flex; gap:15px;">
            <button type="button" class="like-btn {{ $post->likedByUser ? 'liked' : '' }}" data-id="{{ $post->id }}">
                <i class="{{ $post->likedByUser ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                <span id="like-count-{{ $post->id }}">{{ $post->likes_count }}</span>
            </button>

            <button type="button" class="comment-toggle" data-id="{{ $post->id }}">
                <i class="fa-regular fa-comment"></i> 
                <span id="comment-count-{{ $post->id }}">{{ $post->comments_count }}</span>
            </button>
        </div>
    </div>
@empty
    <p>No posts yet.</p>
@endforelse

<!-- Floating mini comment popup -->
<div id="commentPopup" class="comment-popup" style="display:none;">
    <div class="popup-content">
        <span class="close">&times;</span>
        <div id="popupComments" class="popup-comments"></div>
        <form id="popupForm">
            @csrf
            <textarea name="body" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post</button>
        </form>
    </div>
</div>

@endif

<!-- Scroll-to-top button -->
<button id="scrollTopBtn" class="hidden">
    Latest Post <span class="arrow"></span>
</button>

@endsection

@push('styles')
<style>
.comment-popup {
    position: fixed;
    bottom: 60px;
    right: 40px;
    width: 450px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.25);
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}
.popup-content { padding: 20px; }
.popup-comments {
    max-height: 320px;
    overflow-y: auto;
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.comment-bubble {
    background: #e6e6e6;
    border-radius: 15px;
    padding: 8px 12px;
    display: inline-block;
    max-width: 90%;
    word-wrap: break-word;
}
.popup-content textarea {
    width: 100%;
    height: 80px;
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 8px;
    resize: none;
    font-size: 15px;
}
.popup-content button {
    margin-top: 5px;
    background: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}
.popup-content button:hover { background: #0056b3; }
.close { float: right; font-size: 18px; cursor: pointer; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.comment-bubble strong { color: #007bff; }

.like-btn {
    border: none;
    background: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    color: black; 
}

.like-btn i {
    color: black;
    transition: color 0.3s ease;
}

.like-btn.liked i {
    color: #e0245e;
}

.like-btn span {
    color: black;
}

.like-btn:active {
    transform: scale(1.3);
}

/* Hearts that rain down */
.heart {
  position: fixed;
  top: -20px;
  font-size: 40px;            /* larger hearts */
  animation: fall 6s linear forwards; /* longer duration */
  pointer-events: none;
}

@keyframes fall {
  0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
  100% { transform: translateY(120vh) rotate(360deg); opacity: 0; }
}

</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById("commentPopup");
    const popupComments = document.getElementById("popupComments");
    const popupForm = document.getElementById("popupForm");
    const closeBtn = popup.querySelector(".close");
    let currentPostId = null;

    // ✅ Like button logic
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.dataset.id;
            const icon = btn.querySelector('i');
            const countEl = document.getElementById(`like-count-${postId}`);

            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log('Liked state:', data.liked); 

                if (data.liked) {
                    btn.classList.add('liked');
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');

                    // Raining hearts effect
                   for (let i = 0; i < 100; i++) {   
                    const heart = document.createElement('i');
                    heart.className = 'fa-solid fa-heart heart';
                    heart.style.left = Math.random() * window.innerWidth + 'px';
                    heart.style.fontSize = (30 + Math.random() * 20) + 'px'; // bigger
                    heart.style.color = ['#e0245e', '#ff69b4', '#ff1493'][Math.floor(Math.random()*3)];
                    heart.style.animationDelay = (Math.random() * 1.5) + 's';
                    document.body.appendChild(heart);

                    setTimeout(() => heart.remove(), 6000); // match animation duration
                }


                } else {
                    btn.classList.remove('liked');
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                }

                countEl.textContent = data.likes_count;
            })
            .catch(err => console.error('Like error:', err));
        });
    });

    // ✅ Open popup
    document.querySelectorAll('.comment-toggle').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            currentPostId = btn.dataset.id;
            popup.style.display = "block";

            fetch(`/posts/${currentPostId}/comments`)
                .then(res => res.json())
                .then(comments => {
                    popupComments.innerHTML = comments.map(c => `
                        <div class="comment-bubble">
                            <strong>${c.user.name}</strong>: ${c.body}
                        </div>
                    `).join('');
                    popupComments.scrollTop = popupComments.scrollHeight;
                });

            Echo.channel(`post.${currentPostId}`)
                .listen('CommentPosted', (e) => {
                    popupComments.innerHTML += `
                        <div class="comment-bubble">
                            <strong>${e.comment.user.name}</strong>: ${e.comment.body}
                        </div>
                    `;
                    popupComments.scrollTop = popupComments.scrollHeight;

                    const countEl = document.getElementById(`comment-count-${currentPostId}`);
                    countEl.textContent = parseInt(countEl.textContent) + 1;
                });
        });
    });

    // ✅ Close popup
    closeBtn.addEventListener('click', () => {
        popup.style.display = "none";
        popupComments.innerHTML = '';
    });

    // ✅ Submit comment
    popupForm.addEventListener('submit', e => {
        e.preventDefault();
        const body = popupForm.querySelector('textarea').value;

        fetch(`/posts/${currentPostId}/comment`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ body })
        })
        .then(res => res.json())
        .then(data => {
            popupForm.querySelector('textarea').value = '';
            popupComments.innerHTML += `
                <div class="comment-bubble">
                    <strong>${data.user.name}</strong>: ${data.body}
                </div>
            `;
            popupComments.scrollTop = popupComments.scrollHeight;

            const countEl = document.getElementById(`comment-count-${currentPostId}`);
            countEl.textContent = parseInt(countEl.textContent) + 1;
        })
        .catch(err => console.error('Comment error:', err));
    });
});
</script>




