<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }
    .product-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }
    .product-image {
        width: 100%;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .product-info h2 {
        color: #ff5722;
        font-weight: 700;
    }
    .btn-payment {
        background-color: #ff5722;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-payment:hover {
        background-color: #e64a19;
    }
    .btn-cart {
        background-color: #607d8b;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-cart:hover {
        background-color: #455a64;
    }
</style>

<div class="product-container">
    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->title }}" class="product-image">

    <div class="product-info">
        <h2>{{ $product->title }}</h2>
        <p><strong>Price:</strong> RM {{ number_format($product->price, 2) }}</p>
        <p><strong>Stock:</strong> {{ $product->stock }}</p>
        <p><strong>Description:</strong> {{ $product->description }}</p>
        <p><strong>Sold by:</strong> {{ $product->club->name }}</p>

        <!-- Submit Payment -->
        <form action="{{ route('payment.create', $product->id) }}" method="GET" class="mt-3 d-inline">
            <label for="quantity"><strong>Quantity:</strong></label>
            <input type="number" name="quantity" id="quantity" min="1" max="{{ $product->stock }}" value="1" class="form-control w-25 d-inline-block">
            <button type="submit" class="btn btn-payment ms-2">Submit Payment</button>
        </form>

        <!-- Add to Cart -->
        <form action="{{ route('cart.store') }}" method="POST" class="mt-3 d-inline">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="btn btn-cart ms-2">Add to Cart</button>
        </form>
    </div>
</div>
@endsection
