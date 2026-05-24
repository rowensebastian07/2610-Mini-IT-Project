<link rel="stylesheet" href="{{ asset('css/top-nav.css') }}">

<div class="top-nav">
    <img src="{{ asset('images/drop down.png') }}" id="drop-down">
    <ul class="drop-down-list">
        <a href="{{ url('/') }}"><li>Home</li></a>
        <a href="{{ url('/clubs') }}"><li>Clubs</li></a>
     @if (Route::has('login'))
        @auth
        <a href="{{ url('/calendar') }}"><li>Calendar</li></a>
       <a href="{{ url('/notifications') }}"><li>Notifications</li></a>
        <a href="{{ url('/create-clubs') }}"><li>Create Clubs</li></a>

        
        @endauth
    @endif
    
    </ul>

    <!-- 🔍 Search Bar -->
   <div class="search-bar">
       <form action="{{ route('clubs.search') }}" method="GET">
           <input type="text" name="query" id="query" placeholder="Search clubs or events... "value="{{ request('query') }}">
           <button type="submit" id="search-submit" 
           style=" position: absolute; background-color:rgb(82, 82, 82);
            z-index:999; border-color:white; align-items:center; margin:6px 3px; 
            border-radius:7em; border-style:solid;">
            <img src="{{asset('images/search-icon.png')}}" style="width:30px; height:30px; transform:scale(1.7); ">
        </button>
       </form>
   </div>

    
    <ul class="right-side-nav">
        <div class="underline"></div>
        @if (Route::has('login'))
        @auth
        <a href="{{ url('/dashboard') }}"><li>Profile</li></a>
            <a href="{{ route('logout') }}"
            onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><li>Log Out</li>
                
            </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        @else
            <a href="{{ route('login') }}"><li>Log in</li></a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"><li>Register</li></a>
            @endif
            
        @endauth
        @endif
        
        <div class="underline"></div>
    </ul>
</div>