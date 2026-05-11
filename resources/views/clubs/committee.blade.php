@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/committee-page.css') }}">
@endpush

@section('content')
<div class="committee-page">
    <!-- Add Member Form -->
    <div class="add-form">
        <h2 style="text-align:center; font-weight:600; margin-bottom:25px;">
            Committee Members - {{ $club->name }}
        </h2>

        <div class="form-box">
            <form action="{{ route('clubs.committee.add', $club->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="name" placeholder="Member Name" required>
                <input type="text" name="role" placeholder="Role" required>
                <textarea name="description" placeholder="Description (optional)"></textarea>
                <input type="file" name="profile_picture">
                <button type="submit">Add Member</button>
            </form>
        </div>
    </div>

    <!-- Committee Cards -->
    <div class="committee-list">
        @forelse($committee as $member)
            <div class="committee-card">
                <div class="committee-left">
                    @if($member->profile_picture)
                        <img src="{{ asset('storage/' . $member->profile_picture) }}" alt="{{ $member->name }}">
                    @endif
                    <h4>{{ strtoupper($member->name) }}</h4>
                    <p>{{ strtoupper($member->role) }}</p>
                    <form action="{{ route('clubs.committee.remove', [$club->id, $member->id]) }}" method="POST" onsubmit="return confirm('Delete this member?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="remove-btn">Remove</button>
                    </form>
                </div>

                <div class="committee-right">
                    @if($member->description)
                        {{ $member->description }}
                    @else
                        <em>No description provided.</em>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center">No committee members added yet.</p>
        @endforelse
    </div>
</div>
@endsection
