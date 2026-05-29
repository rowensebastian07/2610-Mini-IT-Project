<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }
    .cart-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }
    .cart-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fafafa;
    }
    .cart-card h5 {
        color: #ff5722;
        font-weight: 600;
    }
    .btn-remove {
        background-color: #f44336;
        color: #fff;
        border: none;
        padding: 8px 14px;
        border-radius: 6px;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-remove:hover {
        background-color: #d32f2f;
    }
    .btn-clear {
        background-color: #ff9800;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-clear:hover {
        background-color: #e68900;
    }
    .btn-checkout {
        background-color: #4caf50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-checkout:hover {
        background-color: #388e3c;
    }
</style>

<div class="cart-container">
    <h2>Your Cart</h2>

    @forelse($cart as $item)
        <div class="cart-card">
            <div>
                <h5>{{ $item['name'] }}</h5>
                <p>Price: RM {{ number_format($item['price'], 2) }}</p>
                <p>Quantity: {{ $item['quantity'] }}</p>
                <p>Subtotal: RM {{ number_format($item['price'] * $item['quantity'], 2) }}</p>
            </div>
            <form action="{{ route('cart.destroy', $item['id']) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-remove">Remove</button>
            </form>
        </div>
    @empty
        <p>Cart is empty</p>
    @endforelse

    <h4>Total: RM {{ number_format($total, 2) }}</h4>

    <form action="{{ route('cart.destroy', 'all') }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-clear">Clear Cart</button>
    </form>

    @if(!empty($cart))
        @php $firstProductId = $cart[array_key_first($cart)]['id']; @endphp
        <a href="{{ route('payment.create', $firstProductId) }}" class="btn-checkout">Checkout</a>
    @endif
</div>
@endsection
