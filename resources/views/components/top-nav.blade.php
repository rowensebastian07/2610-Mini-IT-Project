<link rel="stylesheet" href="{{ asset('css/top-nav.css') }}">

<div class="top-nav">
    <!-- Hamburger icon -->
    <img src="{{ asset('images/drop down.png') }}" id="drop-down" alt="Menu">

    <!-- Dropdown list -->
    <ul class="drop-down-list">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
       

        @guest
            <li><a href="{{ route('login') }}">Log in</a></li>
            @if (Route::has('register'))
                <li><a href="{{ route('register') }}">Sign up</a></li>
            @endif
        @endguest

        @auth
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Log out</button>
                </form>
            </li>
        @endauth
    </ul>

    <!-- Right side links -->
    <ul class="right-side-nav">
        <li><a href="{{ route('clubs.index') }}">Clubs</a></li>
        <div class="underline"></div>
        <li><a href="{{ route('calendar.index') }}">Calendar</a></li>
        <div class="underline"></div>
    </ul>
</div>
