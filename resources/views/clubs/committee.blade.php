@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/committee.css') }}">
@endpush

@section('content')

@if(session('success'))
    <div id="popup-message" class="popup">
        {{ session('success') }}
    </div>
@endif


<div class="committee-page" 
     style="background-image: url('{{ asset('storage/' . $club->committee_background) }}'); 
            background-size: cover; background-position: center; min-height: 100vh;">


<div class="committee-form-wrapper">
    <div class="committee-form-container">
        <h3 style="text-align:center;">Assign Committee Member</h3>

        <form action="{{ route('clubs.terms.assign', $club->id) }}" method="POST">
            @csrf
            
            <label for="term">Academic Term Year</label>
            <input type="text" name="term" id="term" placeholder="e.g., 2026/2027" required class="form-control" style="margin-bottom: 15px;">

            <label for="member-search">Select Member</label>
            <select id="member-search" name="user_id" class="form-control" required>
                <option value="">Search by name or email</option>
            </select>
            
            <div id="attempts-left" class="text-muted mt-2" style="margin-bottom: 15px;">
                Attempts left today: {{ $remaining }}
            </div>

            <label for="role">Role Hierarchy</label>
            <select name="role" id="role" class="form-control" required style="margin-bottom: 20px;">
                <option value="">-- Select Role Hierarchy --</option>
                <option value="president">President</option>
                <option value="hicom">High Committee</option>
                <option value="sub_committee">Sub Committee</option>
            </select>

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

<div style="position:fixed; bottom:20px; right:20px;">
    <form action="{{ route('clubs.committee.background', $club->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        <label for="background-upload" class="btn btn-secondary" 
               style="cursor:pointer; background-color:#075e54; color:#fff; border:none; border-radius:6px; padding:10px 16px;">
            Upload Background
        </label>
        <input type="file" id="background-upload" name="background" 
               accept="image/*" style="display:none;" onchange="this.form.submit()">
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
$('#member-search').select2({
    placeholder: 'Search by name or email',
    allowClear: true,
    ajax: {
        url: '{{ route("users.search") }}',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => {
            $('#attempts-left').text("Attempts left today: " + data.remaining);
            return { results: data.results };
        },
        error: xhr => {
            if (xhr.status === 429) {
                const data = xhr.responseJSON;
                alert(data.error || "Daily search limit reached.");
                $('#attempts-left').text("Attempts left today: " + (data.remaining || 0));
                $('#member-search').prop('disabled', true);
            }
        },
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

// Drag and Drop Card
Sortable.create(document.getElementById('committee-list'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    chosenClass: 'sortable-chosen',
    filter: '.edit-icon, .delete-icon',
    onFilter: function (evt) {
        evt.preventDefault();
    }
});
</script>
@endsection