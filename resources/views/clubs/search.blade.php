<x-top-nav></x-top-nav>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $query }}</title>
    <link rel="stylesheet" href="{{ asset('css/search-page.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

    
</head>

<section >

<h1 class="search-header">Search Results for "{{ $query }}"</h1>

@forelse($clubs as $club)
    <div class="club">
        <a href="{{ route('clubs.show', $club->id) }}" class="club-lbl"><h3 class="club-lbl">{{ $club->name }}</h3></a>
        <p>{{ $club->description }}</p>
        @if($club->events->count())
            <h4>Matching Events:</h4>
            <ul>
                @foreach($club->events as $event)
                    <li>{{ $event->title }} - {{ $event->description }}</li>
                @endforeach
            </ul>
        @else
            <p>No matching events.</p>
        @endif
    </div>
@empty
    <p>No clubs found.</p>
@endforelse

</section>

<div class="pagination-div">
{{ $clubs->links() }}
</div>
