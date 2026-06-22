

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


<div class="settings-div" id="settings-div">

    
    <!-- Sub-header -->
        <h1>Your Profile</h1>


    <div class="profile-content" id='profile-content'>
    
    <div class="edit-div" id="edit-div">
    <nav>
        <a class="tab" onclick="tabs(0)">Personal Information</a>
        <a class="tab" onclick="tabs(1)">Liked Posts</a>
        <a class="tab" onclick="tabs(2)">Followed Clubs</a>
        <a class="tab" onclick="tabs(3)">Followed Events</a>
    </nav>
        <!-- Edit Form (hidden by default) -->
        <form id="profile-edit" method="POST" action="{{ route('dashboard.update') }}" enctype="multipart/form-data" style="width: 400%;" class="tabShow">
            <h2 class="settings-h2">Personal Information</h2>
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
                <button type="button" class="btn logout-btn" id="cancel-edit" onclick="closeSettings()">Cancel</button>
                
            </form>
            <form method="POST" action="{{ route('users.destroy', Auth::user()->id) }}" 
                  onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-icon">Delete Profile</button>
            </div>
            
            
        </form>
        <div class="tabShow" style="width: 400%; border-left: 2px rgb(134, 134, 134) solid;">
            
            <h2 class="settings-h2">Liked Posts</h2>
             @forelse($likedPosts as $post)
             
            <div class="post-card" style="position:relative; padding-bottom:40px;">
                <h3 class="post-title">{{ $post->title }}</h3>

                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="post-image">
                @endif

                <p class="post-content">{{ $post->content }}</p>
                <small class="post-meta">Posted in: {{ $post->club->name }}</small>

            </div>
        @empty
            <p>You haven't liked any posts yet.</p>
        @endforelse
        </div>
        
        <div class="tabShow" style="width: 400%; border-left: 2px rgb(134, 134, 134) solid;">
            
            <h2 class="settings-h2">Followed Clubs</h2>
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
            <p>You are not following any clubs yet</p>
        @endforelse
        </div>

     <div class="tabShow" style="width: 400%; border-left: 2px rgb(134, 134, 134) solid;">
            
            <h2 class="settings-h2">Your Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($events as $event)
                        <div class="event-card" style="width: 400px; margin:1rem;">
                            <img src="{{ asset($event->club->profile_picture) }}" class="event-profile-picture" alt="{{ $event->club->name }}">
                            <div class="event-card-text">
                            <h3 class="text-xl font-bold">{{ $event->title }}</h3>
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                @if($event->time) at {{ $event->time }} @endif
                            </p>
                            <p class="text-gray-500">Location: {{ $event->location ?? 'No location set' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class=" ">
                            <p>No events yet. Future events will appear here.</p>
                        </div>
                    @endforelse
                </div>
        </div>

</div>
    <!-- Profile Card -->
    <div class="profile-container" id="profile-container">


        <!-- Public View -->
        <div id="profile-view">
          <img src="{{ Auth::user()->profile_picture ?? asset('images/default_pp.png') }}" alt="Profile Picture">

            <h2>{{ Auth::user()->name }}</h2>
            <p>{{ Auth::user()->email }}</p>
        </div>

        

        <!-- Logout -->
        <button type="button" class="btn logout-btn" id="cancel-edit" onclick="openSettings()">Settings</button>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn logout-btn" style="background-color: red">Log Out</button>
        </form>
    </div>

    <!-- Main Dashboard Content -->
    <main>
        <div class="club-and-events" id="club-and-events">
            
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
                        <div class="event-card-white">
                            <img src="{{ asset($event->club->profile_picture) }}" class="event-profile-picture" alt="{{ $event->club->name }}">
                            <div class="event-card-text">
                            <h3 class="text-xl font-bold">{{ $event->title }}</h3>
                            <p class="text-gray-600">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                @if($event->time) at {{ $event->time }} @endif
                            </p>
                            <p class="text-gray-500">Location: {{ $event->location ?? 'No location set' }}</p>
                            </div>
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
const editDiv = document.getElementById('edit-div');
const profContainer = document.getElementById('profile-container');
const clubEvents = document.getElementById('club-and-events');
const profileContent = document.getElementById('profile-content');


// function to open / close settings menu and display normal dashboard menu
function closeSettings(){
    editDiv.style.display = 'none';
    profContainer.style.display = 'block';
    clubEvents.style.display = 'block';
    profileContent.style.padding = '3rem 0';
};

function openSettings(){
    editDiv.style.display = 'flex';
    profContainer.style.display = 'none';
    clubEvents.style.display = 'none';
    profileContent.style.padding = '0';
}


// Settings menu
    const tabBtn = document.querySelectorAll(".tab");
    const tab = document.querySelectorAll(".tabShow");

    function tabs(panelIndex) {
        tab.forEach(function(node){
            node.style.display = "none";
        });
        if (panelIndex == 0){
            tab[panelIndex].style.display = "flex";
        }
        else{
            tab[panelIndex].style.display = "block";
        }
        
    }

    tabs(0);

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

