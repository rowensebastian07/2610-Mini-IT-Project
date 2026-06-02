<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }

    .edit-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }

    h2 {
        color: #ff5722;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
    }

    .form-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        gap: 10px;
        flex-wrap: wrap;
    }

    label {
        font-weight: 600;
        color: #333;
        min-width: 120px;
    }

    .form-control {
        flex: 1;
        border-radius: 6px;
        padding: 8px 10px;
        border: 1px solid #ccc;
    }

    textarea.form-control {
        resize: none;
        height: 80px;
    }

    .btn-update {
        background-color: #ff5722;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.3s;
        width: 100%;
    }

    .btn-update:hover {
        background-color: #e64a19;
    }

    small a {
        color: #1976d2;
        font-weight: 600;
        text-decoration: none;
    }

    small a:hover {
        text-decoration: underline;
    }

    /* ✅ Preview image styling */
    #preview-image {
        display: block;
        margin-top: 10px;
        max-width: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
</style>

<div class="edit-container">
    <h2>Edit Product</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $product->title) }}" class="form-control" required>
        </div>

        <div class="form-row">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-row">
            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="form-control" required>
        </div>

        <div class="form-row">
            <label>Stock</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control" required>
        </div>

        <div class="form-row">
            <label>Product Image</label>
            <input type="file" name="image" id="image-input" class="form-control" accept="image/*">
            @if($product->image)
                <small>Current: <a href="{{ asset('storage/'.$product->image) }}" target="_blank">View</a></small>
                <img id="preview-image" src="{{ asset('storage/'.$product->image) }}" alt="Current Image">
            @else
                <img id="preview-image" style="display:none;">
            @endif
        </div>

        <button type="submit" class="btn-update mt-3">Update Product</button>
    </form>
</div>

{{-- ✅ Live preview script --}}
<script>
    document.getElementById('image-input').addEventListener('change', function(event) {
        const preview = document.getElementById('preview-image');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
