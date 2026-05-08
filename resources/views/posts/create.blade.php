@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2 class="form-title">Create Post</h2>
    <p class="form-subtitle">Fill in the details below</p>

    <form action="{{ route('posts.store', $club->id) }}" method="POST" enctype="multipart/form-data" class="styled-form">
        @csrf

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title">
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" name="image" id="image">
        </div>

        <button type="submit" class="btn-submit">Save Post</button>
    </form>
</div>
@endsection