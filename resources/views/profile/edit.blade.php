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
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #374151;
    }

    .form-group input {
        width: 100%;
        padding: 0.6rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1rem;
    }

    .error-msg {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .profile-picture-preview img {
        margin-top: 0.75rem;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn.save {
        background: #2563eb;
        color: #fff;
    }

    .btn.save:hover {
        background: #1e40af;
    }

    .btn.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .btn.cancel:hover {
        background: #e5e7eb;
    }
</style>

<div class="edit-profile-container">
    <div class="edit-profile-card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; font-weight: bold;">Edit Profile</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" 
                       value="{{ old('name', Auth::user()->name) }}" required>
                @error('name')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" 
                       value="{{ old('email', Auth::user()->email) }}" required>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture</label>
                <input id="profile_picture" type="file" name="profile_picture" accept="image/*">
                
                <div class="profile-picture-preview">
                    @php
                        $imagePath = Auth::user()->profile_picture 
                                     ? asset('storage/' . Auth::user()->profile_picture) 
                                     : asset('images/default.png');
                    @endphp
                    <img id="preview-image" src="{{ $imagePath }}" alt="Profile Preview">
                </div>
                @error('profile_picture')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn cancel">Cancel</a>
                <button type="submit" class="btn save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Real-time image preview logic
    document.getElementById('profile_picture').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('preview-image');
            preview.src = URL.createObjectURL(file);
        }
    });
</script>
@endsection