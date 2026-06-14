

@extends('layouts.app')

@section('content')

<style>
    /*Live preview Image*/
    #pic_label{
     background: url("{{  $user->profile_picture }}") no-repeat center;
    border: solid black 3px;
    background-size: cover;
    display: inline-block;
    width: 9rem;
    height: 10rem;
    text-align: center;
    border-radius: 50%;
    }

    #pic_label:hover{
        cursor: pointer;
    }

    #pic_label input[ type = "file" ]{
        display: none;
    }
</style>

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">


<div class="settings-div">

    
    <!-- Sub-header -->
        <h1>Your Profile</h1>


    <div class="profile-content">
    
    <div class="edit-div">
    <nav>
        <a>Personal Information</a>
        <a>Liked Posts</a>
        <a >Followed Clubs</a>
        <a>Followed Events</a>
    </nav>
        <!-- Edit Form (hidden by default) -->
        <form id="profile-edit" method="POST" action="{{ route('dashboard.update') }}" enctype="multipart/form-data" style="width: 400%">
            @csrf
            @method('PATCH')
            <label for="name" id="name-lbl">Display Name</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" placeholder="Your Name" required>
            <label for="email" id="email-lbl">Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" placeholder="Your Email" required>
            <label for="profile_picture">Profile Picture</label><br>
            <label id="pic_label" style="margin: 0 1rem;">
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                value="{{ old('profile_picture', $user->profile_picture) }}">
            </label>
            <div class="button-edit" style="margin-left: 1rem;">
                <button type="submit" class="btn">Save Changes</button>
                <button type="button" class="btn logout-btn" id="cancel-edit">Cancel</button>
                <form method="POST" action="{{ route('users.destroy', Auth::user()->id) }}" 
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-icon">Delete Profile</button>
            </form>
            </div>
            
            
        </form>
        </div>
    <!-- Profile Card -->
    <div class="profile-container">
        <div class="icon-bar">
            <button class="edit-icon" id="edit-profile">✏️</button>
            
        </div>

        <!-- Public View -->
        <div id="profile-view">
          <img src="{{ Auth::user()->profile_picture ?? asset('images/default_pp.png') }}" alt="Profile Picture">

            <h2>{{ Auth::user()->name }}</h2>
            <p>{{ Auth::user()->email }}</p>
        </div>

        

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

 // Declaring variables for profile pic
            let input_file = document.getElementById('profile_picture');
            let picDisplay = document.getElementById('pic_label'); 
        

            // Live preview for profile pic
            input_file.onchange = (e) => {

            let file = e.target.files[0];


            let url = URL.createObjectURL(file);

            picDisplay.style.background = `url(${url}) center / cover no-repeat`;

            // Free up memory space (better perfomance)
            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 100)
            
        }

</script>
@endsection

