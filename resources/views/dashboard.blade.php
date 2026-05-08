@extends('layouts.app')

@section('content')
<style>
    main {
        padding-top: 20px; /* reduced so content sits closer to nav */
    }

    .club-card,
    .event-card {
        background: grey;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: box-shadow 0.2s ease;
    }

    .club-card:hover,
    .event-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn {
        display: inline-block;
        background: #2563eb;
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
    }

    .btn:hover {
        background: #1e40af;
    }

    .profile-container {
        max-width: 600px;
        margin: 10px auto; /* reduced margin so it sits higher */
        background: grey;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    .profile-container img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }
    .profile-container h2 {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .profile-container p {
        color: white;
        margin-bottom: 20px;
    }
    .profile-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }
    .profile-container .btn {
        background: #2563eb;
        padding: 10px 18px;
        margin: 5px;
    }
    .profile-container .btn:hover {
        background: #1e40af;
    }
    .logout-btn {
        background: #dc2626;
    }
    .logout-btn:hover {
        background: #b91c1c;
    }

    .sub-header {
        display:flex;
        justify-content:center;
        align-items:center;
        margin-top:10px;   /* sits closer to nav */
        margin-bottom:15px;
    }
    .sub-header h1 {
        font-size: 2rem;
        font-weight: bold;
        margin:0;
    }
</style>

<div class="min-h-screen bg-gray-100 flex flex-col">

    <!-- Sub-header -->
    <div class="sub-header">
        <h1>Your Profile</h1>
    </div>

    <!-- Profile Card -->
    <div class="profile-container">
        <img src="{{ Auth::user()->profile_picture 
                     ? asset('storage/' . Auth::user()->profile_picture) 
                     : asset('images/default-avatar.png') }}" 
             alt="Profile Picture">

        <h2>{{ Auth::user()->name }}</h2>
        <p>{{ Auth::user()->email }}</p>

        <!-- Inline Edit Form -->
        <form method="POST" action="{{ route('dashboard.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <input type="text" name="name" value="{{ Auth::user()->name }}" placeholder="Your Name" required>
            <input type="email" name="email" value="{{ Auth::user()->email }}" placeholder="Your Email" required>
            <input type="file" name="profile_picture">

            <button type="submit" class="btn">Save Changes</button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn logout-btn">Log Out</button>
        </form>
    </div>

    <!-- Main Dashboard Content -->
    <main>
        <div class="max-w-6xl mx-auto p-8 space-y-10">
            
            <!-- Clubs Section -->
            <section>
                <h2 class="text-xl font-bold mb-4">Your Followed Clubs</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($followedClubs as $club)
                        <div class="club-card">
                            <h3 class="text-xl font-bold">{{ $club->name }}</h3>
                            <p class="text-gray-600">{{ $club->description }}</p>
                            <a href="{{ route('clubs.show', $club->id) }}" class="btn mt-4">View Club</a>
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
            <div class="event-sticker 
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
            <div class="event-sticker event-upcoming text-gray-600">
                <p>No events yet. Future events will appear here.</p>
            </div>
        @endforelse
    </div>
</section>

        </div>
    </main>
</div>
@endsection
