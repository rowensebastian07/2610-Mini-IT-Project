@extends('layouts.app')

@section('content')
<div class="event-form-container">
    <h1 class="event-form-title">➕ Add Event for {{ $club->name }}</h1>

    <form action="{{ route('events.store', ['club' => $club->id]) }}" method="POST" class="event-form">
        @csrf

        <div class="form-group">
            <label for="title">Event Title</label>
            <input type="text" id="title" name="title" placeholder="Chess Club AGM" required>
        </div>

        <div class="form-group"> 
            <label for="title">Description</label>
            <input type="description" id="description" name="description" nullable>
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
        </div>

        <div class="form-group">
            <label for="time">Time</label>
            <input type="time" id="time" name="time" required>
        </div>

        <div class="form-group">
            <label for="time">Location</label>
            <input type="location" id="location" name="location" nullable>
        </div>

        <button type="submit" class="btn-event">Save Event</button>
    </form>
</div>
@endsection