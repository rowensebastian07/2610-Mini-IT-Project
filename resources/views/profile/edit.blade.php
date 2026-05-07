@extends('layouts.app')

@section('content')

<style>
    .edit-profile-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        background: #f5f5f5;
        padding-top: 100px;
    }

    .edit-profile-card {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .profile-picture-preview img {
        margin-top: 0.5rem;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn.save {
        background: #2563eb;
        color: #fff;
    }

    .btn.save:hover {
        background: #1e40af;
    }

    .btn.cancel {
        background: #999;
        color: #fff;
    }

    .btn.cancel:hover {
        background: #666;
    }
</style>

<div class="edit-profile-container">
    <div class="edit-profile-card">
        <h2>Edit Profile</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Name -->
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" type="text" name="name"
                       value="{{ old('name', Auth::user()->name) }}">
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email', Auth::user()->email) }}">
            </div>

            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input id="profile_picture" type="file" name="profile_picture">

                <div class="profile-picture-preview">
                    @if(Auth::user()->profile_picture)
                        <img id="preview-image"
                             src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                             alt="Current Profile Picture">
                    @else
                        <img id="preview-image"
                             src="{{ asset('images/default.png') }}"
                             alt="Default Profile Picture">
                    @endif
                </div>
            </div>

            <!-- Buttons -->
            <div class="form-actions">
                <a href="{{ route('profile.show') }}" class="btn cancel">Cancel</a>
                <button type="submit" class="btn save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('preview-image');
            preview.src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
