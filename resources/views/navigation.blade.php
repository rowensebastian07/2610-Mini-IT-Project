<x-top-nav>
  
</x-top-nav>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/navigation-page.css') }}">
    <title>Navigation</title>
</head>

<header>
    <img src="images/csrw-placeholder-2.jpeg">
    <h1>Clubs and Societies in MMU</h1>
</header>
<body>
        <h2>Art Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Arts Clubs")
        
          <a href="clubs/{{ $club->name}}">
            <p>{{ $club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>
        
        <h2>Community Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category== "Community Clubs")
        
          <a href="clubs/{{ $club->name}}">
            <p>{{$club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>

        <h2>Religious Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Religious Clubs")
        
          <a href="clubs/{{ $club->name}}">
            <p>{{$club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name}}">
        </a>  
        
        @endif
        @endforeach
        </div>

        <h2>Games / Entertainment Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Games / Entertainment Clubs")
        
          <a href="clubs/{{ $club->name}}">
            <p>{{ $club->name }}</p>
            <img src="{{$club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>

        <h2>Cultural Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Cultural Clubs")
        
          <a href="clubs/{{$club->name}}">
            <p>{{$club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>

        <h2>Tech Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Tech Clubs")
        
          <a href="clubs/{{$club->name}}">
            <p>{{ $club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>
        <h2>Recreational / Physical Activities Clubs</h2>
        <div class="container">
        @foreach ($clubs as $club)
        @if ($club->category == "Recreational / Physical Activities Clubs")
        
          <a href="clubs/{{ $club->name}}">
            <p>{{ $club->name }}</p>
            <img src="{{ $club->profile_picture }}" alt="{{ $club->name }}">
        </a>  
        
        @endif
        @endforeach
        </div>
        
        
        


    
</body>


</html>

