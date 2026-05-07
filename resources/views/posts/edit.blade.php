@extends('layouts.app')

@section('content')
<div class="form-container edit-form">
    <h2 class="form-title">Edit Post</h2>
    <p class="form-subtitle">Update the details below</p>

    <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="styled-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}">
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content">{{ old('content', $post->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Upload New Image</label>
            <input type="file" name="image" id="image">
        </div>

        <button type="submit" class="btn-update">Update Post</button>
    </form>
</div>
@endsection
