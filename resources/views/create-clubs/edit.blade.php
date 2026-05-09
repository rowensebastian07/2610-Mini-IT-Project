@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/create-clubs.css') }}">

@section('content')
<div>
    <h2 id="create-club-h2">EDIT CLUB</h2>
    <p>Update the details below</p>

    <div class="create-club-form">
        <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label><br>
                <input type="text" name="name" id="name" value="{{ old('name', $club->name) }}">
            </div>

            <div class="form-group">
                <label for="description">Description</label><br>
                <input type="text" name="description" id="description" value="{{ old('description', $club->description) }}">
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label><br>
                <input type="file" name="profile_picture" id="profile_picture">
                @if($club->profile_picture)
                    <p>Current:</p>
                    <img src="{{ asset('storage/' . $club->profile_picture) }}" alt="Club Picture" width="100">
                @endif
            </div>

            <div class="form-group">
                <label for="category">Category</label><br>
                <select name="category" id="category">
                    <option value="Arts Clubs" {{ $club->category == 'Arts Clubs' ? 'selected' : '' }}>Art Clubs</option>
                    <option value="Community Clubs" {{ $club->category == 'Community Clubs' ? 'selected' : '' }}>Community Clubs</option>
                    <option value="Religious Clubs" {{ $club->category == 'Religious Clubs' ? 'selected' : '' }}>Religious Clubs</option>
                    <option value="Games / Entertainment Clubs" {{ $club->category == 'Games / Entertainment Clubs' ? 'selected' : '' }}>Games / Entertainment Clubs</option>
                    <option value="Cultural Clubs" {{ $club->category == 'Cultural Clubs' ? 'selected' : '' }}>Cultural Clubs</option>
                    <option value="Tech Clubs" {{ $club->category == 'Tech Clubs' ? 'selected' : '' }}>Tech Clubs</option>
                    <option value="Recreational / Physical Activities Clubs" {{ $club->category == 'Recreational / Physical Activities Clubs' ? 'selected' : '' }}>Recreational / Physical Activities Clubs</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Update Club</button>
        </form>
    </div>
</div>
@endsection
