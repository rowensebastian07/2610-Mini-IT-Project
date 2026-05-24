@php
    $themes = config('themes');

    $selectedTheme = $themes[$club->theme] ?? $themes['default'];
@endphp

<style>
:root{
    --bg: {{ $selectedTheme['bg'] }};
    --text: {{ $selectedTheme['text'] }};
    --content-box: {{ $selectedTheme['content-box'] }};
    --shadow-color: {{ $selectedTheme['shadow-color'] }};
    --post-colour: {{ $selectedTheme['post-colour'] }};
}
</style>

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/club-content.css') }}">
@endpush

@section('content')
    <!-- Sub-header -->
   <div class="club-banner">
    @if($club->banner_image)
        <img src="{{ asset('storage/' . $club->banner_image) }}" alt="{{ $club->name }} Banner" class="banner-img">
    @else
        <div class="club-banner-placeholder">
            <h2>{{ $club->name }}</h2>
        </div>
    @endif
</div>


    <div class="sub-header" style="display:flex; justify-content:space-between; align-items:center; background-color:var(--content-box);">
        <div style="flex:1;"></div>
        <h1 style="flex:1; text-align:center; margin:0; color:var(--text);">{{ $club->name }}</h1>
        <div class="follow-section" style="flex:1; text-align:right;">
            @auth
                @if(in_array($club->id, auth()->user()->followed_clubs ?? []))
                    <span style="color:green; font-weight:bold; margin-right:10px;">Following</span>
                    <form method="POST" action="{{ route('clubs.unfollow', $club->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-red">Unfollow</button>
                    </form>
                @else
                    <span style="color:var(--text); font-weight:bold; margin-right:10px;">Not Following</span>
                    <form method="POST" action="{{ route('clubs.follow', $club->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-green">Follow</button>
                    </form>
                @endif
            @endauth

            <div style="margin-top:5px; font-weight:bold;">
                Followers: {{ $club->followersCount() }}
            </div>
        </div>
    </div>

    <!-- Club card -->
    <div class="club-card-header">
        <img src="{{ asset('images/' . $club->profile_picture) }}" class="club-image-rect" alt="{{ $club->name }}">
        <p class="club-description">{{ $club->description }}</p>


        @auth
            <a href="{{ route('posts.create', $club->id) }}" class="btn-blue">Create Post</a>
            <a href="{{ route('events.create', ['club' => $club->id]) }}" class="btn-green">Add Event</a>
            <a href="{{ route('clubs.edit', $club->id) }}" class="btn-yellow">Edit Club</a>
            @if (auth()->user()->role === \App\Enums\ClubRole::PRESIDENT || auth()->user()->is_admin )
                <form action="{{ route('clubs.destroy', $club->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this club?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-red">Delete Club</button>
                </form>
            @endif

        @endauth
    </div>

    <!-- Club Content -->
    <div class="club-container">
        <div class="club-main">
            <section class="club-section">
                <h3>About Us</h3>
                <p>{{ $club->description }}</p>
            </section>

            <section class="club-section">
                <!-- Posts Section -->
                <div class="posts-section" style="margin-top:20px; display:block; width:100%;" id="posts-section">
                    <h2 class="posts-title">Posts</h2>
                    @forelse($club->posts as $post)
                        <div class="post-card">
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->content }}</p>

                            @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" class="post-image" alt="Post image">
                            @endif

                            <div class="mt-2">
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn-green">Edit</a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-red">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p>No posts yet for this club.</p>
                    @endforelse
                </div>
            </section>
            
            <section class="club-section">
                <div class="club-main">
                    <section class="club-section" >
                        <div class="events-section" id="events-section" >
                            <h2 class="posts-title">Events</h2>
                            
                            @if($club->events->count())
                                <div class="events-table-wrapper">
                                    <table class="events-table">
                                        <thead>
                                            <tr>
                                                <th>Event Title</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Photos</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($club->events as $event)
                                                <tr>
                                                  <td>{{ $event->title }}</td>
                                                  <td>{{ $event->time }}</td>
                                                  <td>{{ $event->time }}</td>

                                                <td>
                                                        <form action="{{ route('events.uploadFiles', $event->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="file" name="event_files[]" multiple class="inline-input" />
                                                            <button type="submit" class="btn-blue" style="margin-left:5px;">Upload</button>
                                                        </form>

                                                        @if($event->uploads)
                                                            <a href="{{ route('events.viewUploads', $event->id) }}" 
                                                            class="btn-green" style="margin-left:5px;" target="_blank">
                                                            View Photos
                                                            </a>
                                                        @endif
                                                    </td>

                                                    <td class="action-cell">
                                                        <a href="{{ route('events.edit', ['club' => $club->id, 'event' => $event->id]) }}" class="btn-green">Edit</a>
                                                        <form action="{{ route('events.destroy', ['club' => $club->id, 'event' => $event->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this event?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-red">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center">No events yet for this club.</p>
                            @endif
                        </div>
                    </section>
                </div>
            </section>
        </div>

        <div class="club-sidebar">
            <div class="info-card">
                <div class="jump-btns">
                    <a class="jump-anchor" href="#posts-section">View Posts</a>
                    <a class="jump-anchor" href="#events-section">View Events</a>
                </div>
                
                
            </div>
            <div class="info-card">
                <h4>Membership</h4>
                <p>Join us to get the latest updates and participate in exclusive events.</p>
                
                <!-- try to make registration state with registration open check box -->
                @if(/*$club->registration_open && */ $club->registration_link)
                    <a href="{{ $club->registration_link }}" class="btn-join" target="_blank">
                        Register Now
                    </a>
                @elseif($club->registration_open)
                    <span class="btn-join disabled" style="background:#ccc; cursor:not-allowed;">
                        Registration URL not set
                    </span>
                @else
                    <span class="btn-join disabled" style="background:#ccc; cursor:not-allowed;">
                        Registration closed
                    </span>
                @endif
            </div>

            <div class="info-card">
                <h4>Committee</h4>
                <a href="/clubs/{{ $club->id }}/committee" class="link-text">View Committee Members</a>
            </div>

        

            <!-- CONTACT & FAQ CARD -->
            <div class="info-card contact-card">
                <div class="icon-bar">
                    <button class="edit-icon" id="edit-contact">✏️</button>
                </div>

                <!-- Public View -->
                <div id="contact-view">
                    <h4>Contact & FAQ</h4>
                    <p><strong>Email:</strong> {{ $club->email ?? 'N/A' }}</p>
                    <p><strong>Instagram:</strong> {{ $club->instagram ?? 'N/A' }}</p>
                    <p><strong>Website:</strong> {{ $club->website ?? 'N/A' }}</p>
                    <a href="#faq" class="link-text">Frequently Asked Questions</a>
                </div>

                <!-- Edit Form (hidden by default) -->
                <form id="contact-edit"
                    action="{{ route('clubs.updateContact', $club->id) }}"
                    method="POST"
                    style="display:none;">
                    @csrf
                    <input type="email" name="email" value="{{ old('email', $club->email) }}" placeholder="Club Email">
                    <input type="text" name="instagram" value="{{ old('instagram', $club->instagram) }}" placeholder="Instagram URL">
                    <input type="text" name="website" value="{{ old('website', $club->website) }}" placeholder="Website URL">

                    <button type="submit" class="btn">Save Changes</button>
                    <button type="button" class="btn logout-btn" id="cancel-contact">Cancel</button>
                </form>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            $(document).ready(function() {
                // Show edit form
                $('#edit-contact').on('click', function() {
                    $('#contact-view').hide();
                    $('#contact-edit').show();
                });

                // Cancel edit
                $('#cancel-contact').on('click', function() {
                    $('#contact-edit').hide();
                    $('#contact-view').show();
                });
            });
            </script>


            <!-- CLUB CHATROOM CARD -->
            <div class="info-card chatroom-card">
                <h4>Club Chatroom</h4>
                <p>Interact with other members in real time!</p>
                <a href="{{ route('clubs.chatroom', $club->id) }}" class="btn btn-primary">Open Chatroom</a>
            </div>

<!-- Theme preview -->
 @if (auth()->user()->role === \App\Enums\ClubRole::PRESIDENT || auth()->user()->role === \App\Enums\ClubRole::COMMITTEE || auth()->user()->is_admin )
                
    <div id="preview-div">
        <div id="theme-menu" style="position: relative;">
                <div>
                    
                <form action="{{ route('clubs.updateTheme', $club->id) }}" method="POST" enctype="multipart/form-data" >
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="theme" id="theme" value="{{ $club->theme }}" id="form-themes">
                        @foreach($themes as $themeName => $theme)
                                <button type="button" onclick="changeTheme()" name="btn-preview-theme" class="btn-preview-theme" 
                                data-value="{{ $themeName }}" 
                                data-bg =   "{{ $theme['bg'] }}"
                                data-text = "{{ $theme['text'] }}"
                                data-context = "{{ $theme['content-box'] }}"
                                data-shadow="{{ $theme['shadow-color'] }}"
                                data-post = "{{ $theme['post-colour'] }}"
                                style="width:40px; 
                                height:40px; border:black solid 1px; border-radius: 2em;
                                margin:1em 0.5em; background:linear-gradient({{ $theme['bg'] }}, 
                                {{ $theme['content-box'] }});">
                                </button>
                        @endforeach
                


                    <button type="submit" class="btn-submit" style="position: absolute; bottom:1em; right:0em; transform:scale(0.9);">Update Theme</button>
                </form>
                </div>
            
            </div>
            

        <button id="preview-btn" onclick="openTheme()">
            <p id="theme-lbl">Theme</p>
            
                

        </button>
    </div>
    
@endif

 <!-- script for JSON photo collection  for events -->
@push('scripts')
<script>
function updateDriveLink(eventId, link) {
    fetch(`/events/${eventId}/drive-link`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ drive_link: link })
    })
    .then(res => res.json())
    .then(data => {
        console.log("Drive link updated:", data);
    })
    .catch(err => console.error("Error updating drive link:", err));
}
    // theme preview button 
    var div = document.getElementById('theme-menu');
    function openTheme(){
        if(div.style.display == 'block'){
            div.style.display = 'none';
        }
        else {
            div.style.display = 'block';
        }
    }



document.querySelectorAll('.btn-preview-theme').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('theme').value = btn.dataset.value;

        document.documentElement.style.setProperty('--bg', btn.dataset.bg);
        document.documentElement.style.setProperty('--text', btn.dataset.text);
        document.documentElement.style.setProperty('--content-box', btn.dataset.context);
        document.documentElement.style.setProperty('--shadow-color', btn.dataset.shadow);
        document.documentElement.style.setProperty('--post-colour', btn.dataset.post);

    });
    
});

// 1. Select all matching links
const links = document.querySelectorAll('.jump-anchor');

// 2. Loop through each link in the list
links.forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault(); // Stops URL from changing
    
    const targetId = this.getAttribute('href'); 
    const targetElement = document.querySelector(targetId);
    
    if (targetElement) {
      targetElement.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

</script>
@endpush

