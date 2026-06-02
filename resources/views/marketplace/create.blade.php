<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    /* ====== Orange Theme Styling ====== */
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }

    .create-container {
        max-width: 700px;
        margin: 50px auto;
        background: #ffffff;
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

    label {
        font-weight: 600;
        color: #333;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        margin-bottom: 18px;
        border: 1px solid #ddd;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.3s ease;
    }

    input:focus, textarea:focus {
        border-color: #ff5722;
    }

    .btn-submit {
        background-color: #ff5722;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        width: 100%;
    }

    .btn-submit:hover {
        background-color: #e64a19;
    }

    #image-preview {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 10px;
        display: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
</style>

<div class="create-container">
    <h2>Add New Product</h2>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Product Form -->
    <form action="{{ route('products.store', $club->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Product Name -->
        <label for="title">Product Name</label>
        <input type="text" name="title" id="title" required>

        <!-- Description -->
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <!-- Price -->
        <label for="price">Price (RM)</label>
        <input type="number" step="0.01" name="price" id="price" required>

        <!-- Stock -->
        <label for="stock">Stock Quantity</label>
        <input type="number" name="stock" id="stock" required>

        <!-- Image Upload -->
        <label for="image">Upload Image</label>
        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
        <img id="image-preview" alt="Preview">

        <!-- Submit -->
        <button type="submit" class="btn-submit">Save Product</button>
    </form>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }
</script>
@endsection
