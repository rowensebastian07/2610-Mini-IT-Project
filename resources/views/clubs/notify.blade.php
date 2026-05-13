@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Your Notifications</h2>

    @if($notifications->isEmpty())
        <div class="alert alert-secondary text-center">
            No notifications yet.
        </div>
    @else
        <div class="list-group">
            @foreach($notifications as $notification)
                <div class="list-group-item d-flex justify-content-between align-items-center 
                    {{ $notification->read_at ? 'bg-light' : 'bg-white' }}" 
                    style="border-left: 4px solid {{ $notification->read_at ? '#ccc' : '#007bff' }};">
                    
                    <div>
                        <h5 class="mb-1">{{ $notification->data['club_name'] ?? 'Club Update' }}</h5>
                        <p class="mb-1 text-muted">{{ $notification->data['message'] ?? 'No message content' }}</p>
                        <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="text-end">
                        @if(!$notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success">Mark as Read</button>
                            </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3 text-center">
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Mark All as Read</button>
            </form>
        </div>
    @endif
</div>
@endsection
