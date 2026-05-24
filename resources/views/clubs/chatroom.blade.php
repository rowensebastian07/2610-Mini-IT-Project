<x-top-nav></x-top-nav>

@extends('layouts.app')

@section('content')
<style>
    .chatroom-wrapper {
        background-color: #e5ddd5;
        border-radius: 10px;
        width: 90%;
        max-width: 900px;
        margin: 30px auto;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
        height: 80vh;
    }

    .chatroom-header {
        background-color: #075e54;
        color: #fff;
        padding: 15px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        font-weight: 600;
    }

    .chat-window {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: url('{{ asset('images/chat-wallpaper.jpg') }}') repeat center center;
        background-size: cover;
        display: flex;
        flex-direction: column;
    }

    .message-wrapper {
        display: flex;
        align-items: flex-end;
        margin-bottom: 20px; /* adds gap between messages */
        position: relative;
    }

    .message-wrapper.sent { flex-direction: row-reverse; }

    .profile-pic {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin: 0 8px;
        object-fit: cover;
    }

    .message {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 0.9rem;
        word-wrap: break-word;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .message.sent {
        background-color: #dcf8c6;
        align-self: flex-end;
    }

    .message.received {
        background-color: #fff;
        align-self: flex-start;
    }

    .timestamp {
        font-size: 0.7rem;
        color: #555;
        position: absolute;
        bottom: -15px;
        right: 10px;
    }

    .chat-input-area {
        display: flex;
        background-color: #f0f0f0;
        padding: 10px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    #chat-input {
        flex: 1;
        border: none;
        border-radius: 20px;
        padding: 10px 15px;
        outline: none;
    }

    .send-btn {
        background-color: #075e54;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-size: 1.2rem;
    }
</style>

<div class="chatroom-wrapper">
    <div class="chatroom-header">
        <h3>{{ $club->name }} Chatroom 💬</h3>
    </div>

    <div id="chat-window" class="chat-window">
        @foreach($messages as $message)
            <div class="message-wrapper {{ $message->user_id == auth()->id() ? 'sent' : 'received' }}" data-id="{{ $message->id }}">
                <img src="{{ $message->user->profile_picture }}" class="profile-pic" alt="{{ $message->user->name }}">
                <div class="message {{ $message->user_id == auth()->id() ? 'sent' : 'received' }}">
                    <strong>{{ $message->user->name }}:</strong> {{ $message->body }}
                    <span class="timestamp">{{ $message->created_at->format('g:i a') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <form id="chat-form" class="chat-input-area">
        @csrf
        <input type="text" id="chat-input" name="message" placeholder="Type a message…" autocomplete="off">
        <button type="submit" class="send-btn">➤</button>
    </form>
</div>

<script>
    // Send message
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        let body = document.getElementById('chat-input').value;

        fetch("{{ route('clubs.messages.store', $club->id) }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
            body: JSON.stringify({ body: body })
        })
        .then(res => res.json())
        .then(data => {
            let msgDiv = `<div class="message-wrapper sent" data-id="${data.id}">
                <img src="${data.user.profile_picture}" class="profile-pic">
                <div class="message sent">
                    ${data.body}
                    <span class="timestamp">${new Date(data.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</span>
                </div>
            </div>`;
            document.getElementById('chat-window').innerHTML += msgDiv;
            document.getElementById('chat-input').value = "";
        });
    });

    // Real-time listener
    window.Echo.channel('club.{{ $club->id }}')
        .listen('MessageSent', (e) => {
            let msgDiv = `<div class="message-wrapper received" data-id="${e.message.id}">
                <img src="${e.message.user.profile_picture}" class="profile-pic">
                <div class="message received">
                    <strong>${e.message.user.name}:</strong> ${e.message.body}
                    <span class="timestamp">${new Date(e.message.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</span>
                </div>
            </div>`;
            document.getElementById('chat-window').innerHTML += msgDiv;
        });

    // Right-click delete
    document.addEventListener('contextmenu', function(e) {
        const wrapper = e.target.closest('.message-wrapper.sent');
        if (wrapper) {
            e.preventDefault();
            const id = wrapper.dataset.id;
            if (confirm('Delete this message?')) {
                fetch(`/messages/${id}`, {
                    method: 'DELETE',
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                }).then(res => {
                    if (res.ok) wrapper.remove();
                });
            }
        }
    });
</script>
@endsection
