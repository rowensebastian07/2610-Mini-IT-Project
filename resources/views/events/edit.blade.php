@extends('layouts.app')

@section('content')
<div class="edit-event-container">
    <h2 class="form-title">Edit Event</h2>

    <form action="{{ route('events.update', ['club' => $club->id, 'event' => $event->id]) }}" method="POST" class="event-form">
        @csrf
        @method('PUT')

        <!-- Event Title -->
        <div class="form-group">
            <label for="title">Event Title</label>
            <input type="text" name="title" id="title"
                   value="{{ old('title', $event->title) }}" required>
        </div>

        <!-- Event Date -->
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" name="date" id="date"
                   value="{{ old('date', $event->date) }}" required>
        </div>

        <!-- Event Time -->
        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" name="time" id="time"
                   value="{{ old('time', $event->time) }}" required>
        </div>

        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-update">Update Event</button>
            <a href="{{ route('clubs.show', $club->id) }}" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection