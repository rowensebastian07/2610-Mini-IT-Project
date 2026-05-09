<h1>Search Results for "{{ $query }}"</h1>

@forelse($clubs as $club)
    <div class="club">
        <h3>{{ $club->name }}</h3>
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

{{ $clubs->links() }}