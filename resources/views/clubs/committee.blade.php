<x-top-nav></x-top-nav>

@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #fff;
        font-family: 'Roboto', sans-serif;
    }

    .committee-form-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        margin-top: 40px;
    }

    .committee-form-container {
        background-color: #5f6368;
        color: #fff;
        border-radius: 10px;
        padding: 25px 30px;
        width: 85%;
        max-width: 1100px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        margin: 0 auto;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #3c4043;
        background-color: #292a2d;
        color: #fff;
        margin-bottom: 10px;
    }

    .btn-primary {
        background-color: #1a73e8;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }
    .btn-primary:hover { opacity: 0.9; }

    .btn-secondary {
        background-color: #5f6368;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: opacity 0.2s ease;
    }
    .btn-secondary:hover { opacity: 0.9; }

    .popup {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #1a73e8;
        color: #fff;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        animation: fadeInOut 3s forwards;
        z-index: 9999;
    }
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-10px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; }
        100% { opacity: 0; transform: translateY(-10px); }
    }

    .profile-card {
        position: relative;
        display: flex;
        background-color: #202124;
        color: #fff;
        border-radius: 12px;
        padding: 25px;
        margin: 25px auto;
        width: 90%;
        max-width: 1200px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.5);
        transition: transform 0.2s ease;
    }
    .profile-card:hover { transform: scale(1.02); }

    .profile-left {
        flex: 0 0 180px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .profile-img {
        width: 160px;
        height: 160px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #5f6368;
    }

    .profile-right { flex: 1; padding-left: 25px; }

    .icon-bar {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 10px;
    }

    .edit-icon, .delete-icon {
        background-color: transparent;
        border: none;
        font-size: 22px;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    .edit-icon { color: #1a73e8; }
    .edit-icon:hover { color: #4dabf7; }

    .delete-icon { color: #e63946; }
    .delete-icon:hover { color: #ff6b6b; }

    .form-control-inline {
        background-color: #292a2d;
        color: #fff;
        border: 1px solid #3c4043;
        border-radius: 6px;
        padding: 8px;
        width: 100%;
        margin-bottom: 10px;
    }

    /* Grip handle for drag */
    .drag-handle {
        position: absolute;
        left: 15px;
        top: 15px;
        font-size: 20px;
        color: #888;
        cursor: grab;
    }
    .drag-handle:hover { color: #ccc; }
</style>

@if(session('success'))
    <div id="popup-message" class="popup">
        {{ session('success') }}
    </div>
@endif

<div class="committee-form-wrapper">
    <div class="committee-form-container">
        <h3 style="text-align:center;">Assign Committee Member</h3>

        <form action="{{ route('clubs.committee.add', $club->id) }}" method="POST">
            @csrf
            <label>Select Member</label>
            <select id="member-search" name="user_id" class="form-control">
                <option value="">Search by name or email</option>
            </select>

            <label>Role</label>
            <input type="text" name="role" class="form-control" placeholder="Enter role">

            <button type="submit" class="btn-primary">Assign Member</button>
        </form>
    </div>
</div>

<div id="committee-list">
    {{-- Default card for president --}}
    <div class="profile-card" id="card-president" data-id="president">
        <span class="drag-handle">⋮⋮</span>
        <div class="icon-bar">
            <button class="edit-icon" data-id="president">✏️</button>
            <form action="{{ route('clubs.committee.remove', [$club->id, 'president']) }}" 
                  method="POST" onsubmit="return confirm('Are you sure you want to remove the president?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-icon">🗑️</button>
            </form>
        </div>
        <div class="profile-left">
            <img src="{{ asset('images/mmu.png') }}" alt="Default Profile" class="profile-img">
        </div>
        <div class="profile-right">
            <div class="profile-view" id="view-president">
                <h3>{{ $club->owner->name ?? 'N/A' }}</h3>
                <p><strong>Role:</strong> President</p>
                <p>N/A</p>
            </div>
            <form action="{{ route('clubs.committee.update', [$club->id, 'president']) }}" 
                  method="POST" enctype="multipart/form-data" 
                  class="profile-edit" id="edit-president" style="display:none;">
                @csrf
                @method('PUT')
                <input type="text" name="role" value="President" class="form-control-inline">
                <textarea name="description" class="form-control-inline" rows="3">N/A</textarea>
                <input type="file" name="profile_picture" class="form-control-inline">
                <button type="submit" class="btn-primary">Save Changes</button>
                <button type="button" class="btn-secondary cancel-btn" data-id="president">Cancel</button>
            </form>
        </div>
    </div>

    {{-- Cards for accepted committee members --}}
    @foreach($committee as $member)
        @if($member->status === 'accepted')
            <div class="profile-card" id="card-{{ $member->id }}" data-id="{{ $member->id }}">
                <span class="drag-handle">⋮⋮</span>
                <div class="icon-bar">
                    <button class="edit-icon" data-id="{{ $member->id }}">✏️</button>
                    <form action="{{ route('clubs.committee.remove', [$club->id, $member->id]) }}" 
                          method="POST" onsubmit="return confirm('Are you sure you want to remove this member?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-icon">🗑️</button>
                    </form>
                </div>
                <div class="profile-left">
                    <img src="{{ asset($member->profile_picture ?? 'images/mmu.png') }}" 
                         alt="Profile Picture" class="profile-img">
                </div>
                <div class="profile-right">
                    <div class="profile-view" id="view-{{ $member->id }}">
                        <h3>{{ $member->name }}</h3>
                        <p><strong>Role:</strong> {{ $member->role }}</p>
                        <p>{{ $member->description ?? 'No description provided.' }}</p>
                    </div>
                    <form action="{{ route('clubs.committee.update', [$club->id, $member->id]) }}" 
                          method="POST" enctype="multipart/form-data" 
                          class="profile-edit" id="edit-{{ $member->id }}" style="display:none;">
                        @csrf
                        @method('PUT')
                        <input type="text" name="role" value="{{ $member->role }}" class="form-control-inline">
                        <textarea name="description" class="form-control-inline" rows="3">{{ $member->description ?? '' }}</textarea>
                        <input type="file" name="profile_picture" class="form-control-inline">
                        <button type="submit" class="btn-primary" style="margin-top:10px;">Save Changes</button>
                        <button type="button" class="btn-secondary cancel-btn" data-id="{{ $member->id }}">Cancel</button>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
$(document).ready(function() {
    // Select2 search
    $('#member-search').select2({
        placeholder: 'Search by name or email',
        allowClear: true,
        ajax: {
            url: '{{ route("users.search") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(user => ({
                    id: user.id,
                    text: user.name + " (" + user.email + ")"
                }))
            }),
            cache: true
        },
        minimumInputLength: 2
    });

    // Edit toggle
    $('.edit-icon').on('click', function() {
        const id = $(this).data('id');
        $('#view-' + id).hide();
        $('#edit-' + id).show();
    });

    $('.cancel-btn').on('click', function() {
        const id = $(this).data('id');
        $('#edit-' + id).hide();
        $('#view-' + id).show();
    });

    //Drag and Drop Card
   Sortable.create(document.getElementById('committee-list'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    filter: '.edit-icon, .delete-icon',   // prevent drag when clicking edit/delete
    onFilter: function (evt) {
        evt.preventDefault();
    }
});

});
</script>
@endsection