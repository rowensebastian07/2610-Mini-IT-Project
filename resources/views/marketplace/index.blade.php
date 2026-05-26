<x-top-nav></x-top-nav>
@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Poppins', sans-serif;
    }
    .marketplace-topnav {
        background-color: #007bff;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 40px;
        margin-top: 70px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .marketplace-title { font-weight: 700; font-size: 1.3rem; }
    .marketplace-search { flex: 1; display: flex; justify-content: center; }
    .marketplace-search input {
        width: 50%; padding: 10px; border: none;
        border-radius: 6px 0 0 6px; outline: none;
    }
    .marketplace-search button {
        background-color: #ff5722; color: white; border: none;
        padding: 10px 20px; border-radius: 0 6px 6px 0; cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .marketplace-search button:hover { background-color: #e64a19; }
    .cart-icon { font-size: 1.5rem; cursor: pointer; transition: transform 0.2s ease; }
    .cart-icon:hover { transform: scale(1.1); }
    .marketplace-container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
    .product-card {
        background-color: #fff; border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden;
        transition: transform 0.3s ease;
    }
    .product-card:hover { transform: translateY(-5px); }
    .product-card img { width: 100%; height: 220px; object-fit: cover; }
    .product-card .card-body { padding: 15px; text-align: center; }
    .product-card h5 { font-weight: 600; margin-bottom: 10px; }
    .product-card p { color: #555; margin-bottom: 8px; }
    .badge { font-size: 0.8rem; padding: 5px 10px; border-radius: 5px; }
    .btn-view {
        background-color: #007bff; color: white; border: none;
        padding: 8px 15px; border-radius: 6px; font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }
    .btn-view:hover { background-color: #0056b3; }
    .alert-info {
        background-color: #e9f5ff; border: 1px solid #b8daff;
        color: #004085; font-weight: 500; border-radius: 8px;
        padding: 20px; text-align: center;
    }
</style>

<!-- Blue Top Nav -->
<div class="marketplace-topnav">
    <div class="marketplace-title">
        {{ $club->name }} Marketplace
    </div>

    <form method="GET" action="{{ route('clubs.marketplace', $club->id) }}" class="marketplace-search">
        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}">
        <button type="submit">Search</button>
    </form>

   <!-- Right: Cart + Add Product -->
<div style="display:flex; gap:15px; align-items:center;">
    <a href="{{ route('cart.index') }}" style="color:white;">
        <i class="fa-solid fa-cart-shopping cart-icon"></i>
    </a>

    @if(auth()->user()->role === \App\Enums\ClubRole::COMMITTEE || auth()->user()->is_admin)
    <a href="{{ route('marketplace.admin', $club->id) }}" class="btn btn-light btn-sm">
        ⚙️ Manage Marketplace
    </a>
@endif

</div>

</div>

<!-- Product Grid -->
<div class="marketplace-container">
    @if($products->count())
        <div class="product-grid">
            @foreach($products as $product)
                <div class="product-card">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->title }}">
                    @endif
                    <div class="card-body">
                        <h5>{{ $product->title }}</h5>
                        <p>RM {{ number_format($product->price, 2) }}</p>

                        <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </span>

                        <p class="small text-muted mt-2 mb-0">
                            Sold by: <strong>{{ $product->club->name }}</strong>
                        </p>

                        <!-- Correct product detail link -->
                        <a href="{{ route('products.show', $product->id) }}" class="btn-view mt-2">View</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No products found. Try adjusting your filters or check back later!
        </div>
    @endif
</div>
@endsection
