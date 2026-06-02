<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')

@if(session('success'))
    <div id="popup-message">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const popup = document.getElementById('popup-message');
            if (popup) {
                popup.style.transition = 'opacity 0.5s ease';
                popup.style.opacity = '0';
                setTimeout(() => popup.remove(), 500);
            }
        }, 3000);
    </script>
@endif

<style>

    #popup-message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4CAF50;
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    z-index: 9999;
}

    body {
        background-color: #f8f4ff;
        font-family: 'Poppins', sans-serif;
    }
    .payment-container {
        max-width: 700px;
        margin: 50px auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 30px;
    }
    h2 {
        color: #7b3fe4;
        font-weight: 700;
        margin-bottom: 25px;
    }
    label {
        font-weight: 600;
        color: #4a2b8a;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #cbb8ff;
        padding: 10px;
        margin-bottom: 15px;
    }
    .form-control:focus {
        border-color: #7b3fe4;
        box-shadow: 0 0 5px rgba(123,63,228,0.3);
    }
    .btn-submit {
        background-color: #7b3fe4;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-submit:hover {
        background-color: #5e2ccf;
    }
    .treasurer-box {
        background: #f3e8ff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .treasurer-box h4 {
        color: #7b3fe4;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .treasurer-box img {
        width: 150px;
        border-radius: 10px;
        margin-top: 10px;
    }
</style>

<div class="payment-container">
    <h2>Submit Payment for {{ $product->title }}</h2>



    <!-- Treasurer Details Above Form -->
    <div class="treasurer-box">
    <h4>💰 Treasurer Details</h4>
    <p><strong>Name:</strong> {{ $treasurer->name }}</p>
    <p><strong>Bank Name:</strong> {{ $treasurer->bank_name }}</p>
    <p><strong>Account Number:</strong> {{ $treasurer->account_number }}</p>
    @if($treasurer->qr_payment)
        <img src="{{ asset('storage/'.$treasurer->qr_payment) }}" alt="QR Payment">
    @else
        <p class="text-muted">No QR payment image uploaded.</p>
    @endif
</div>


    <!-- Payment Form -->
    <form action="{{ route('payment.store', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Payer Name</label>
            <input type="text" name="payer_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Amount (RM)</label>
            <input type="number" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Payment Date</label>
            <input type="date" name="payment_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Upload Proof of Payment (Image)</label>
            <input type="file" name="proof_image" class="form-control" required>
        </div>

        <button type="submit" class="btn-submit">Submit Payment</button>
    </form>
</div>
@endsection
