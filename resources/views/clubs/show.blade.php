@extends('layouts.app')

@section('content')
    <!-- Sub-header -->
    <div class="sub-header" style="display:flex; justify-content:space-between; align-items:center;">
        <!-- Left spacer -->
        <div style="flex:1;"></div>

        <!-- Club name centered -->
        <h1 style="flex:1; text-align:center; margin:0;">{{ $club->name }}</h1>

        <!-- Follow/Unfollow section on right -->
        <div class="follow-section" style="flex:1; text-align:right;">
            @auth
                @if(in_array($club->id, auth()->user()->followed_clubs ?? []))
                    <span style="color:green; font-weight:bold; margin-right:10px;">Following</span>
                    <form method="POST" action="{{ route('clubs.unfollow', $club->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-red">Unfollow</button>
                    </form>
                @else
                    <span style="color:gray; font-weight:bold; margin-right:10px;">Not Following</span>
                    <form method="POST" action="{{ route('clubs.follow', $club->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-green">Follow</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>

    <!-- Club card -->
    <div class="club-card-header">
        <img src="{{ asset('images/' . $club->profile_picture) }}" class="club-image-rect" alt="{{ $club->name }}">
        <p class="club-description">{{ $club->description }}</p>

        <!-- Create Post / Add Event buttons -->
        <a href="{{ route('posts.create', $club->id) }}" class="btn btn-blue">Create Post</a>
        <a href="{{ route('events.create', ['club' => $club->id]) }}" class="btn btn-green">Add Event</a>
    </div>

    <!-- Posts Section -->
    <h2 class="posts-title">Posts</h2>
    @forelse($club->posts as $post)
        <div class="post-card">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>

            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="post-image" alt="Post image">
            @endif

            <div class="mt-2">
                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-green">Edit</a>
                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-red">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <p>No posts yet for this club.</p>
    @endforelse

    <!-- Events Section -->
    <h2 class="posts-title text-center">Events</h2>
    @if($club->events->count())
        <div class="events-table-wrapper">
            <table class="events-table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Date</th>
                        <th>Time</th>
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
                                <a href="{{ route('events.edit', ['club' => $club->id, 'event' => $event->id]) }}" class="btn btn-green">Edit</a>
                                <form action="{{ route('events.destroy', ['club' => $club->id, 'event' => $event->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-red">Delete</button>
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
@endsection
