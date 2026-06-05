<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }

    h2 {
        color: #ff5722;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
    }

    .btn-danger {
        background-color: #ff3b30;
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }
    .btn-danger:hover { background-color: #e02a20; }

    .sub-header {
        color: #ff5722;
        font-weight: 700;
        text-align: center;
        margin-top: 20px;
        border-bottom: 2px solid #ffcc80;
        display: inline-block;
        padding-bottom: 5px;
    }

    .table {
        width: 95%;
        margin: 25px auto;
        border-collapse: collapse;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

   .table th, .table td {
    padding: 12px 16px;
    text-align: center;
    border: 1px solid #ffcc80; 
}


    .table th {
        background-color: #ffe0b2;
        color: #ff5722;
        font-weight: 700;
    }

    .table td {
        background-color: #fffaf3;
        color: #333;
    }

    .badge.bg-success {
        background-color: #4CAF50;
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
    }

    .badge.bg-warning {
        background-color: #FFC107;
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
    }

    .btn-sm.btn-success {
        background-color: #ff5722;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        color: #fff;
        transition: 0.3s;
    }
    .btn-sm.btn-success:hover { background-color: #e64a19; }

    .form-control {
        border: 1px solid #ffcc80;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.9rem;
    }
</style>

{{-- 🔻 Sold Out Button --}}
<div style="text-align:center; margin-top:25px;">
    <form action="{{ route('products.soldout', $product->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger btn-lg">Mark as Sold Out</button>
    </form>
</div>

{{-- 🔸 Orders Table --}}
<h3 class="sub-header">Your Orders</h3>

<table class="table mt-4">
    <thead>
        <tr>
            <th>Date</th>
            <th>Payer Name</th>
            <th>Amount (RM)</th>
            <th>Proof</th>
            <th>Status</th>
            <th>Message / Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $order)
            <tr>
                <td>{{ $order->payment_date }}</td>
                <td>{{ $order->payer_name }}</td>
                <td>{{ number_format($order->amount, 2) }}</td>
                <td>
                    @if($order->proof_image)
                        <a href="{{ asset('storage/'.$order->proof_image) }}" target="_blank">View</a>
                    @else
                        <span class="text-muted">No proof</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $order->verification_status == 'verified' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($order->verification_status) }}
                    </span>
                </td>
                <td>
                    @if($order->verification_status == 'pending')
                        <form action="{{ route('orders.verify', $order->id) }}" method="POST" style="display:flex; gap:10px; align-items:center; justify-content:center;">
                            @csrf
                            <input type="text" name="message" placeholder="Enter collection message..." class="form-control" style="width:250px;" required>
                            <button type="submit" class="btn btn-sm btn-success">Verify</button>
                        </form>
                    @else
                        <span class="text-muted">{{ $order->message ?? 'No message' }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-muted text-center">No payments yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
