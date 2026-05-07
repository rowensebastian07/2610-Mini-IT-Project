@extends('layouts.app')

@section('content')
<x-top-nav />
<style>
    .profile-container {
        max-width: 600px;
        margin: 120px auto 40px; /* push down below fixed header */
        background: grey;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    .profile-container img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }
    .profile-container h2 {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .profile-container p {
        color: white;
        margin-bottom: 20px;
    }
    .profile-container .btn {
        display: inline-block;
        background: #2563eb;
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        text-decoration: none;
        margin: 5px;
    }
    .profile-container .btn:hover {
        background: #1e40af;
    }
    .logout-btn {
        background: #dc2626;
    }
    .logout-btn:hover {
        background: #b91c1c;
    }
</style>

<div class="profile-container">
    <!-- Profile Picture -->
    <img src="{{ Auth::user()->profile_picture 
                 ? asset('storage/' . Auth::user()->profile_picture) 
                 : asset('images/default-avatar.png') }}" 
         alt="Profile Picture">

    <!-- Name + Email -->
    <h2>{{ Auth::user()->name }}</h2>
    <p>{{ Auth::user()->email }}</p>

    <!-- Actions -->
    <a href="{{ route('profile.edit') }}" class="btn">Edit Profile</a>

    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn logout-btn">Log Out</button>
    </form>
</div>
@endsection
