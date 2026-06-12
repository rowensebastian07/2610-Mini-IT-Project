

@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<div class="settings-div">

    <!-- Sub-header -->
        <h1>Your Profile</h1>


    <div class="profile-content">
    <!-- Profile Card -->
    <div class="profile-container">
        <div class="icon-bar">
            <button class="edit-icon" id="edit-profile">✏️</button>
            <form method="POST" action="{{ route('users.destroy', Auth::user()->id) }}" 
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-icon">🗑️</button>
            </form>
        </div>

        <!-- Public View -->
        <div id="profile-view">
          <img src="{{ Auth::user()->profile_picture ?? asset('images/mmu.png') }}" alt="Profile Picture">

            <h2>{{ Auth::user()->name }}</h2>
            <p>{{ Auth::user()->email }}</p>
        </div>

        <!-- Edit Form (hidden by default) -->
        <form id="profile-edit" method="POST" action="{{ route('dashboard.update') }}" enctype="multipart/form-data" style="display:none;">
            @csrf
            @method('PATCH')

            <input type="text" name="name" value="{{ Auth::user()->name }}" placeholder="Your Name" required>
            <input type="email" name="email" value="{{ Auth::user()->email }}" placeholder="Your Email" required>
            <input type="file" name="profile_picture">

            <button type="submit" class="btn">Save Changes</button>
            <button type="button" class="btn logout-btn" id="cancel-edit">Cancel</button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn logout-btn">Log Out</button>
        </form>
    </div>

    <!-- Main Dashboard Content -->
    <main>
        <div class="club-and-events">
            
            <!-- Clubs Section -->
            <section>
                <h2 class="text-xl font-bold mb-4">Your Followed Clubs</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($followedClubs as $club)
                        <div class="club-card">
                            <img src="{{ asset($club->profile_picture) }}" class="club-image-rect" alt="{{ $club->name }}">
                            <div class="club-section">
                            <h3 class="text-xl font-bold">{{ $club->name }}</h3>
                            <p class="text-gray-600">{{ $club->description }}</p>
                            <a href="{{ route('clubs.show', $club->id) }}" class="btn mt-4">View Club</a>
                            </div>
                        </div>
                    @empty
                        <div class="club-card text-gray-600">
                            <p>You are not following any clubs yet.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Events Section -->
            <section>
                <h2 class="text-xl font-bold mb-4">Your Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($events as $event)
                        <div class="event-card 
                            {{ \Carbon\Carbon::parse($event->date)->isPast() && !\Carbon\Carbon::parse($event->date)->isToday() 
                                ? 'event-passed' 
                                : 'event-upcoming' }}">
                            <h3 class="text-xl font-bold">{{ $event->title }}</h3>
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                @if($event->time) at {{ $event->time }} @endif
                            </p>
                            <p class="text-gray-500">{{ $event->location ?? 'No location set' }}</p>
                        </div>
                    @empty
                        <div class="event-card text-gray-600">
                            <p>No events yet. Future events will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#edit-profile').on('click', function() {
        $('#profile-view').hide();
        $('#profile-edit').show();
    });

    $('#cancel-edit').on('click', function() {
        $('#profile-edit').hide();
        $('#profile-view').show();
    });
});
</script>
@endsection

