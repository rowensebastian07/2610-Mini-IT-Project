@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/club-content.css') }}">
@endpush

@section('content')
    <!-- Sub-header -->
    <div class="club-banner-placeholder">
    <h2>{{ $club->name }}</h2>
    </div>

    <div class="sub-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="flex:1;"></div>
        <h1 style="flex:1; text-align:center; margin:0;">{{ $club->name }}</h1>
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
                    <span style="color:gray; font-weight:bold; margin-right:10px;">Not Following</span>
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

        <a href="{{ route('posts.create', $club->id) }}" class="btn-blue">Create Post</a>
        <a href="{{ route('events.create', ['club' => $club->id]) }}" class="btn-green">Add Event</a>
        <a href="{{ route('clubs.edit', $club->id) }}" class="btn-yellow">Edit Club</a>
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
                <div class="posts-section" style="margin-top:20px; display:block; width:100%;">
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
                    <section class="club-section">
                        <div class="events-section">
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
                                                    <td>{{ $event->date }}</td>
                                                    <td>{{ $event->time }}</td>
                                                    <td>
                                                        <input type="url" 
                                                            value="{{ $event->drive_link ?? '' }}" 
                                                            placeholder="Paste Drive link..." 
                                                            onchange="updateDriveLink({{ $event->id }}, this.value)" 
                                                            class="inline-input" />
                                                        @if($event->drive_link)
                                                            <a href="{{ $event->drive_link }}" target="_blank" class="btn-blue" stlye=margin-left:5px>
                                                                View</a>
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

            <div class="info-card">
                <h4>Contact & FAQ</h4>
                <p><strong>Email:</strong> {{ $club->email ?? 'N/A' }}</p>
                <a href="#faq" class="link-text">Frequently Asked Questions</a>
            </div>
        </div>
    </div>

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
</script>
@endpush

