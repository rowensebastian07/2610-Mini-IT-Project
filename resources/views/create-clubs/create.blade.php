@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/create-clubs.css') }}">

@section('content')
<div >
    <h2 id = "create-club-h2">Create Club</h2>
    <p>Fill in the details below</p>

<div class="create-club-form">
    <form action="{{ route('create-clubs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Name</label><br>
            <input type="text" name="name" id="name">
        </div>

        <div class="form-group">
            <label for="description">Description</label><br>
            <input name="description" id="description"></input>
        </div>

        <div class="form-group">
            <label for="profile_picture">Profile_Picture</label><br>
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <div class="form-group">
            <label for="category">Category</label><br>
            <select name="category" id="category">
                <option value="Arts Clubs">Art Clubs</option>
                <option value="Community Clubs">Community Clubs</option>
                <option value="Religious Clubs">Religious Clubs</option>
                <option value="Games / Entertainment Clubs">Games / Entertainment Clubs</option>
                <option value="Cultural Clubs">Cultural Clubs</option>
                <option value="Tech Clubs">Tech Clubs</option>
                <option value="Recreational / Physical Activities Clubs">Recreational / Physical Activities Clubs</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Create Club</button>
    </form>

</div>
</div>
@endsection




