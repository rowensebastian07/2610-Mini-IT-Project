<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff8f0;
        font-family: 'Poppins', sans-serif;
    }
    .admin-container {
        max-width: 1200px;
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
    .btn-orange {
        background-color: #ff5722;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }
    .btn-orange:hover { background-color: #e64a19; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }
    th { background-color: #ffe0b2; color: #333; }
    .img-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
    .treasurer-box {
        background: #f3e8ff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 30px;
        position: relative;
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
    .edit-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        border: none;
        background: none;
        font-size: 20px;
        cursor: pointer;
    }
</style>

<div class="admin-container">
    <h2>Marketplace Admin Dashboard</h2>

    <a href="{{ route('products.create', $club->id) }}" class="btn-orange mb-3">+ Add Product</a>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price (RM)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="img-thumb">
                        @endif
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        <a href="{{ route('products.sales', $product->id) }}" class="btn btn-sm btn-info">Sales</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Treasurer Details Section -->
    <div class="treasurer-box">
        <button id="editTreasurerBtn" class="edit-btn">✏️</button>
        <h4>💰 Treasurer Details</h4>

        @if($treasurer)
            <!-- Display mode -->
            <div id="treasurerDisplay">
                <p><strong>Name:</strong> {{ $treasurer->name ?? 'Not set' }}</p>
                <p><strong>Bank Name:</strong> {{ $treasurer->bank_name ?? 'Not set' }}</p>
                <p><strong>Account Number:</strong> {{ $treasurer->account_number ?? 'Not set' }}</p>
                @if($treasurer->qr_payment)
                    <img src="{{ asset('storage/'.$treasurer->qr_payment) }}" alt="QR Payment">
                @else
                    <p class="text-muted">No QR payment image uploaded.</p>
                @endif
            </div>

            <!-- Edit mode -->
            <div id="treasurerEdit" style="display:none;">
                <div class="editable-field">
                    <label><strong>Name:</strong></label>
                    <input type="text" id="treasurer_name" value="{{ $treasurer->name }}" class="form-control" placeholder="Enter Treasurer Name">
                </div>

                <div class="editable-field mt-2">
                    <label><strong>Bank Name:</strong></label>
                    <input type="text" id="treasurer_bank" value="{{ $treasurer->bank_name }}" class="form-control" placeholder="Enter Bank Name">
                </div>

                <div class="editable-field mt-2">
                    <label><strong>Account Number:</strong></label>
                    <input type="text" id="treasurer_account" value="{{ $treasurer->account_number }}" class="form-control" placeholder="Enter Account Number">
                </div>

                <div class="editable-field mt-2">
                    <label><strong>QR Payment Image:</strong></label>
                    <input type="file" id="treasurer_qr" class="form-control">
                </div>

                <button id="saveTreasurer" class="btn btn-warning mt-3">Save Treasurer Info</button>
                <button id="cancelEdit" class="btn btn-secondary mt-2">Cancel</button>
            </div>
        @else
            <p class="text-muted">No treasurer record found for this club.</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if($treasurer)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('editTreasurerBtn');
    const displayDiv = document.getElementById('treasurerDisplay');
    const editDiv = document.getElementById('treasurerEdit');
    const cancelBtn = document.getElementById('cancelEdit');

    // Toggle edit mode
    editBtn.addEventListener('click', () => {
        displayDiv.style.display = 'none';
        editDiv.style.display = 'block';
    });

    cancelBtn.addEventListener('click', () => {
        editDiv.style.display = 'none';
        displayDiv.style.display = 'block';
    });

    // Save Treasurer Info
    document.getElementById('saveTreasurer').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('treasurer_name', document.getElementById('treasurer_name').value);
        formData.append('treasurer_bank', document.getElementById('treasurer_bank').value);
        formData.append('treasurer_account', document.getElementById('treasurer_account').value);
        if (document.getElementById('treasurer_qr').files[0]) {
            formData.append('treasurer_qr', document.getElementById('treasurer_qr').files[0]);
        }
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route('treasurer.update', $club->id) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert('Treasurer details updated successfully!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update treasurer details.');
        });
    });
});
</script>
@endif
@endsection
