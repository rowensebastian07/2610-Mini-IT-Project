@extends('layouts.app')

@section('content')
<style>
    /* Embedded CSS for Edit Club Form */
    // WHY
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #create-club-h2 {
        text-align: center;
        font-size: 30px;
        color: #222;
        margin-top: 40px;
        letter-spacing: 1px;
    }

    p {
        text-align: center;
        color: #555;
        margin-bottom: 30px;
    }

    .create-club-form {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }

    .create-club-form form {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 30px 40px;
        width: 420px;
        text-align: left;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 6px;
    }

    .form-group input[type="text"],
    .form-group input[type="file"],
    .form-group select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        border-color: #1e40af;
        outline: none;
    }

    .form-group img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
        border: 2px solid #ddd;
    }

    .btn-submit {
        background-color: #1e40af;
        color: #fff;
        border: none;
        padding: 10px 0;
        width: 100%;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #1d4ed8;
    }
</style>

<div>
    <h2 id="create-club-h2">EDIT CLUB</h2>
    <p>Update the details below</p>

    <div class="create-club-form">
        <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $club->name) }}">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description" id="description" value="{{ old('description', $club->description) }}">
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture">
                @if($club->profile_picture)
                    <p>Current:</p>
                    <img src="{{ asset('storage/' . $club->profile_picture) }}" alt="Club Picture">
                @endif
            </div>

            <div class="form-group">
                <label for="category">Category</label>
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