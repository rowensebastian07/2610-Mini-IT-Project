<link rel="stylesheet" href="{{ asset('css/top-nav.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<img src="{{ asset('images/drop down.png') }}" id="drop-down" onclick="toggleMenu()" class="drop-down">
 <aside class="sidebar" id="sidebar">
            
            <div class="drop-down-list">
                <a href="{{ url('/') }}"><p>Home</p></a>
                <a href="{{ url('/clubs') }}"><p>Clubs</p></a>
            @if (Route::has('login'))
                @auth
                <a href="{{ url('/calendar') }}"><p>Calendar</p></a>
                <a href="{{ url('/create-clubs') }}"><p>Create Clubs</p></a>

                
                @endauth
            @endif
            </div>
        </aside>

<div class="top-nav">
   
    <div class="search-bar">
        <form action="{{ route('clubs.search') }}" method="GET">
            <input type="text" name="query" id="query" placeholder="Search clubs or events..." value="{{ request('query') }}">
            <button type="submit" id="search-submit">
                <img src="{{ asset('images/search-icon.png') }}" style="width:30px; height:30px; transform:scale(1.7);">
            </button>
        </form>
    </div>

    <ul class="right-side-nav">
        @if (Route::has('login'))
            @auth
                <li class="nav-item-wrapper notification-wrapper">
                    <a href="{{ url('/notifications') }}" class="bell-link">
                        <i class="fa-regular fa-bell"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="notification-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item-wrapper profile-dropdown-wrapper" id="profileMenuTrigger">
                    <div class="profile-trigger">
                        <span>Profile</span>
                        <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                    </div>
                    
                    <ul class="profile-submenu" id="profileSubmenu">
                        <li>
                            <a href="{{ url('/dashboard') }}">
                                <i class="fa-regular fa-user"></i> My Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </li>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <li class="nav-item-wrapper guest-link">
                    <a href="{{ route('login') }}" >Log in</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item-wrapper guest-link">
                        <a href="{{ route('register') }}">Register</a>
                    </li>
                @endif
            @endauth
        @endif
    </ul>
</div>

<script>
    

    function toggleMenu(){

        const burgerMenu = document.getElementById('drop-down')
        const sidebar = document.getElementById('sidebar');

        sidebar.classList.toggle('active');
        burgerMenu.classList.toggle('active');


    }
    

</script>