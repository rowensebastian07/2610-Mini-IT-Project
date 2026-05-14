@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: white;
        color: white;
        font-family: 'Roboto', sans-serif;
    }

    h2 {
        color: black;
        margin-bottom: 20px;
        font-weight: 600;
        text-align: center;
    }

    .notification-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 85vh;
    }

    .notification-container {
        display: flex;
        width: 90%;
        max-width: 1200px;
        height: 80vh;
        border-radius: 10px;
        overflow: hidden;
        background-color: #202124;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    .notification-list {
        width: 40%;
        border-right: 1px solid #3c4043;
        overflow-y: auto;
        background-color: #2d2f31;
    }

    .notification-item {
        padding: 18px 22px;
        border-bottom: 1px solid #3c4043;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .notification-item:hover { background-color: #3c4043; }
    .notification-item.active { background-color: #1a73e8; color: #fff; }

    .notification-detail {
        flex: 1;
        padding: 30px;
        overflow-y: auto;
        background-color: #292a2d;
        color: #e8eaed;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        margin-left: 8px;
    }
    .badge-event { background-color: #1a73e8; color: #fff; }
    .badge-post  { background-color: #34a853; color: #fff; }
    .badge-club  { background-color: #fbbc05; color: #000; } /* ✅ new club badge */

    .btn {
        border: none;
        border-radius: 4px;
        padding: 8px 14px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: opacity 0.2s ease;
        margin-right: 10px;
    }
/* Mark as Read (blue) */
.btn-markread {
    background-color: #1a73e8;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: opacity 0.2s ease;
}
.btn-markread:hover { opacity: 0.9; }

/* Accept (green) */
.btn-success {
    background-color: #34a853;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: opacity 0.2s ease;
}
.btn-success:hover { opacity: 0.9; }

/* Decline (red) */
.btn-danger {
    background-color: #d93025;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: opacity 0.2s ease;
}
.btn-danger:hover { opacity: 0.9; }

    .notification-actions { margin-top: 20px; }
</style>

<div class="container mt-4">
    <h2>Your Notifications</h2>

    <div class="notification-wrapper">
        <div class="notification-container">
            {{-- Left panel --}}
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <div class="notification-item" 
                         onclick="showNotification('{{ $notification->id }}')"
                         id="notif-{{ $notification->id }}">
                        <strong>{{ $notification->data['club_name'] ?? 'Club Update' }}</strong>
                        @if(isset($notification->data['type']))
                            <span class="badge badge-{{ $notification->data['type'] }}">
                                {{ ucfirst($notification->data['type']) }}
                            </span>
                        @endif
                        <br>
                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>

            {{-- Right panel --}}
            <div class="notification-detail" id="notification-detail">
                <p>Select a notification to view its details.</p>
            </div>
        </div>
    </div>
</div>

<script>
    const notifications = @json($notifications);
function showNotification(id) {
    // Reset active state
    document.querySelectorAll('.notification-item').forEach(el => el.classList.remove('active'));
    document.getElementById('notif-' + id).classList.add('active');

    const notif = notifications.find(n => n.id == id);
    const detail = document.getElementById('notification-detail');

    // ✅ Default actions (Mark as Read + Delete)
    let actionsHtml = `
        <div class="notification-actions">
            <form action="/notifications/${id}/read" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-markread">Mark as Read</button>
            </form>
            <form action="/notifications/${id}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    `;

    // ✅ Add Accept/Decline buttons for committee invites
    if (notif.data.type === 'committee') {
        actionsHtml += `
            <div class="notification-actions" style="margin-top:15px;">
                <form action="/clubs/${notif.data.club_id}/invite/respond" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="btn btn-success">Accept</button>
                </form>
                <form action="/clubs/${notif.data.club_id}/invite/respond" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="action" value="decline">
                    <button type="submit" class="btn btn-danger">Decline</button>
                </form>
            </div>
        `;
    }

    // ✅ Render detail panel
    detail.innerHTML = `
        <h4 style="color:#fff;">${notif.data.club_name ?? 'Club Update'}
            ${notif.data.type ? `<span class="badge badge-${notif.data.type}">${notif.data.type}</span>` : ''}
        </h4>
        <p style="color:#ccc;">${notif.data.message ?? 'No message content'}</p>
        <small style="color:#999;">${new Date(notif.created_at).toLocaleString()}</small>
        ${actionsHtml}
    `;
}


</script>
@endsection 