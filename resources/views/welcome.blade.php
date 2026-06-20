@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@section('content')

<div class="sub-header">
    <h1>WELCOME TO MMU's CLUB & SOCIETY OFFICIAL WEBPAGE</h1>
</div>

@if (auth()->user())
    <h2>Followed Clubs</h2>
    @forelse($followedPosts as $post)
        <div class="post-card">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>

            {{-- ✅ Gallery only if media exists --}}
            @if($post->media->count() > 0)
                <div class="post-gallery-wrapper" data-index="0">
                    <div class="post-gallery">
                        @foreach($post->media as $index => $media)
                            <div class="media-item" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                <img src="{{ asset('storage/' . $media->path) }}" class="post-image" alt="Post image">
                            </div>
                        @endforeach
                    </div>

                    {{-- ✅ Arrows + counter only if more than one image --}}
                    @if($post->media->count() > 1)
                        <button class="scroll-btn left" onclick="changeMedia(this, -1)">←</button>
                        <button class="scroll-btn right" onclick="changeMedia(this, 1)">→</button>
                        <div class="media-counter">
                            <span class="current-index">1</span>/<span class="total-count">{{ $post->media->count() }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <small class="post-meta">Posted in: {{ $post->club->name }}</small>

            <div style="display:flex; gap:15px; margin-top:10px;">
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
        <div class="null-div">
            <h3 class="null-h3">NO FOLLOWED CLUBS YET? WHY DON'T YOU DISCOVER SOME NEW CLUBS TO KEEP UP TO DATE WITH?</h3>
            <a href="{{ url('/clubs') }}" class="discover-btn">
                <span class="discover-span"></span>
                <span class="discover-span"></span>
                <p class="discover-text">Find New Clubs</p>
            </a>
        </div>
    @endforelse

    <h2>Other Clubs</h2>
    @forelse($otherPosts as $post)
        <div class="post-card">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>

            @if($post->media->count() > 0)
                <div class="post-gallery-wrapper" data-index="0">
                    <div class="post-gallery">
                        @foreach($post->media as $index => $media)
                            <div class="media-item" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                <img src="{{ asset('storage/' . $media->path) }}" class="post-image" alt="Post image">
                            </div>
                        @endforeach
                    </div>

                    @if($post->media->count() > 1)
                        <button class="scroll-btn left" onclick="changeMedia(this, -1)">←</button>
                        <button class="scroll-btn right" onclick="changeMedia(this, 1)">→</button>
                        <div class="media-counter">
                            <span class="current-index">1</span>/<span class="total-count">{{ $post->media->count() }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <small class="post-meta">Posted in: {{ $post->club->name }}</small>

            <div style="display:flex; gap:15px; margin-top:10px;">
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
@else
    <h2>Latest Posts</h2>
    @forelse($posts as $post)
        <div class="post-card">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>

            @if($post->media->count() > 0)
                <div class="post-gallery-wrapper" data-index="0">
                    <div class="post-gallery">
                        @foreach($post->media as $index => $media)
                            <div class="media-item" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                <img src="{{ asset('storage/' . $media->path) }}" class="post-image" alt="Post image">
                            </div>
                        @endforeach
                    </div>

                    @if($post->media->count() > 1)
                        <button class="scroll-btn left" onclick="changeMedia(this, -1)">←</button>
                        <button class="scroll-btn right" onclick="changeMedia(this, 1)">→</button>
                        <div class="media-counter">
                            <span class="current-index">1</span>/<span class="total-count">{{ $post->media->count() }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <small class="post-meta">Posted in: {{ $post->club->name }}</small>

            <div style="display:flex; gap:15px; margin-top:10px;">
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
@endif

<!-- Background overlay -->
<div id="commentOverlay" class="comment-overlay"></div>

<!-- Floating comment popup -->
<div id="commentPopup" class="comment-popup" style="display:none;">
    <div class="popup-content">
        <span class="close">&times;</span>
        <h3 id="popupTitle" class="popup-title"></h3>
        <div id="popupComments" class="popup-comments"></div>
        <form id="popupForm">
            @csrf
            <textarea name="body" placeholder="Write a comment..." required></textarea>
            <button type="submit">Post</button>
        </form>
    </div>
</div>

<!-- Scroll-to-top button -->
<button id="scrollTopBtn" class="hidden">
    Latest Post <span class="arrow"></span>
</button>

@endsection



@push('styles')
<style>
.comment-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    z-index: 1000; /* above overlay */
    display: none;
    flex-direction: column;
    animation: fadeInPopup 0.3s ease;
}

@keyframes fadeInPopup {
    from { opacity: 0; transform: translate(-50%, -45%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}
.popup-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
}

.popup-title {
    font-size: 18px;
    font-weight: 600;
    color: #007bff;
    margin-bottom: 12px;
    text-align: center;
}

.popup-comments {
    flex: 1;
    max-height: 350px;
    overflow-y: auto;
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-right: 5px;
}

.comment-bubble {
    background: #f5f5f5;
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 14px;
    color: #333;
    line-height: 1.4;
}
.comment-bubble strong {
    color: #007bff;
}

.popup-content textarea {
    width: 100%;
    height: 80px;
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 10px;
    resize: none;
    font-size: 15px;
    transition: border-color 0.2s ease;
}
.popup-content textarea:focus {
    border-color: #007bff;
    outline: none;
}

.popup-content button {
    margin-top: 10px;
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s ease;
}
.popup-content button:hover {
    background: #0056b3;
}

.close {
    float: right;
    font-size: 20px;
    cursor: pointer;
    color: #555;
    transition: color 0.2s ease;
}
.close:hover {
    color: #e0245e;
}

@keyframes fadeInOverlay {
    from { opacity: 0; }
    to { opacity: 1; }
}

.comment-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(2px);
    z-index: 999; /* below popup */
    display: none;
    animation: fadeInOverlay 0.3s ease;
}

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
  font-size: 40px;            
  animation: fall 6s linear forwards; 
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
    const overlay = document.getElementById("commentOverlay");
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
                if (data.liked) {
                    btn.classList.add('liked');
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');

                    // ❤️ Raining hearts effect
                    for (let i = 0; i < 100; i++) {
                        const heart = document.createElement('i');
                        heart.className = 'fa-solid fa-heart heart';
                        heart.style.left = Math.random() * window.innerWidth + 'px';
                        heart.style.fontSize = (30 + Math.random() * 20) + 'px';
                        heart.style.color = ['#e0245e', '#ff69b4', '#ff1493'][Math.floor(Math.random() * 3)];
                        heart.style.animationDelay = (Math.random() * 1.5) + 's';
                        document.body.appendChild(heart);
                        setTimeout(() => heart.remove(), 6000);
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

    // ✅ Open comment popup
    document.querySelectorAll('.comment-toggle').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            currentPostId = btn.dataset.id;
            popup.style.display = "block";
            overlay.style.display = "block";

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
        overlay.style.display = "none";
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

    // ✅ Image carousel logic
    document.querySelectorAll(".post-card").forEach(card => {
        const wrapper = card.querySelector(".post-gallery-wrapper");
        if (!wrapper) return;

        const items = wrapper.querySelectorAll(".media-item");
        const counter = card.querySelector(".media-counter .current-index");
        const total = card.querySelector(".media-counter .total-count");
        let currentIndex = 0;

        if (items.length > 0) {
            items.forEach((item, i) => item.style.display = i === 0 ? "block" : "none");
            if (total) total.textContent = items.length;
            if (counter) counter.textContent = 1;
        }

        wrapper.querySelectorAll(".scroll-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const direction = btn.classList.contains("left") ? -1 : 1;

                // Hide current image
                items[currentIndex].style.display = "none";

                // Calculate next index
                currentIndex = (currentIndex + direction + items.length) % items.length;

                // Show next image
                items[currentIndex].style.display = "block";

                // Update counter
                if (counter) counter.textContent = currentIndex + 1;
            });
        });
    });
});
</script>
@endpush




