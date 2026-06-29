<x-top-nav></x-top-nav>

@extends('layouts.app')

@section('content')
<style>
.chatroom-wrapper {
  background-color: #e5ddd5;
  border-radius: 10px;
  width: 90%;
  max-width: 900px;
  margin: 100px auto 0; 
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
  margin-bottom: 20px;
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

.message.sent { background-color: #dcf8c6; align-self: flex-end; }
.message.received { background-color: #fff; align-self: flex-start; }
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

.message-menu {
  position: absolute;
  display: none;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.25);
  z-index: 1000;
  padding: 6px 0;
  min-width: 120px;
}

.message-menu button {
  display: block;
  width: 100%;
  padding: 8px 14px;
  border: none;
  background: none;
  text-align: left;
  font-size: 0.9rem;
  cursor: pointer;
}

.message-menu button:hover { background-color: #f5f5f5; }
</style>

<!--Chatroom Body -->

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
    <input type="text" id="chat-input" name="body" placeholder="Type a message…" autocomplete="off">
    <button type="submit" class="send-btn">➤</button>
</form>

</div>

<!-- Floating menu -->
<div id="message-menu" class="message-menu">
  <button id="edit-message">✏️ Edit</button>
  <button id="delete-message">🗑️ Delete</button>
</div>

<!-- Edit modal -->
<div id="edit-modal" style="
  display:none; position:fixed; top:0; left:0; width:100%; height:100%;
  background:rgba(0,0,0,0.4); z-index:2000; justify-content:center; align-items:center;">
  <div style="
    background:#fff; border-radius:10px; width:400px; padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.3);">
    <h4 style="margin-bottom:10px;">Edit message</h4>
    <div id="edit-preview" style="
      background:#dcf8c6; border-radius:8px; padding:10px; margin-bottom:10px;
      font-size:0.9rem; box-shadow:0 1px 2px rgba(0,0,0,0.2);">
      <span id="edit-preview-text"></span>
      <span id="edit-preview-time" style="display:block; font-size:0.8rem; color:#555; margin-top:4px;"></span>
    </div>
    <input type="text" id="edit-input" style="
      width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;" placeholder="Edit your message only">
    <div style="text-align:right; margin-top:10px;">
      <button id="save-edit" style="
        background:#075e54; color:#fff; border:none; border-radius:50%;
        width:40px; height:40px; font-size:1.2rem; cursor:pointer;">✔</button>
    </div>
  </div>
</div>

<script>
let selectedMessageId = null;

//  Send message
document.addEventListener('DOMContentLoaded', () => {
  const chatForm = document.getElementById('chat-form');
  const chatInput = document.getElementById('chat-input');
  const chatWindow = document.getElementById('chat-window');

  chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = chatInput.value.trim();
    if (!body) return;

    try {
      const res = await fetch("{{ route('clubs.messages.store', $club->id) }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ body })
      });

      const data = await res.json();

      chatWindow.insertAdjacentHTML('beforeend', `
        <div class="message-wrapper sent" data-id="${data.id}">
          <img src="${data.user.profile_picture}" class="profile-pic">
          <div class="message sent">
            <strong>You:</strong> ${data.body}
            <span class="timestamp">${new Date(data.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</span>
          </div>
        </div>
      `);

      chatInput.value = "";
      chatWindow.scrollTop = chatWindow.scrollHeight;
    } catch (err) {
      console.error('Send failed:', err);
    }
  });
});

// Right-click menu
document.addEventListener('contextmenu', function(e) {
  const wrapper = e.target.closest('.message-wrapper.sent');
  if (wrapper) {
    e.preventDefault();
    selectedMessageId = wrapper.dataset.id;
    const menu = document.getElementById('message-menu');
    const rect = wrapper.getBoundingClientRect();

    // Position menu near the message bubble itself
    menu.style.top = (rect.top + window.scrollY + rect.height / 2) + 'px';
    menu.style.left = (rect.right + window.scrollX - menu.offsetWidth - 10) + 'px';
    menu.style.display = 'block';
  } else {
    document.getElementById('message-menu').style.display = 'none';
  }
});

//  Show modal when Edit clicked
document.getElementById('edit-message').addEventListener('click', function() {
  document.getElementById('message-menu').style.display = 'none';

  const msgDiv = document.querySelector(`.message-wrapper[data-id="${selectedMessageId}"] .message`);
   // Extract only message text (exclude timestamp)
  const timestampEl = msgDiv.querySelector('.timestamp');
  const oldText = timestampEl
    ? msgDiv.innerText.replace(timestampEl.innerText, '').replace(/^\s*[^:]+:\s*/, '').trim()
    : msgDiv.innerText.replace(/^\s*[^:]+:\s*/, '').trim();
  const oldTime = timestampEl ? timestampEl.innerText : '';

  // Fill modal preview
  document.getElementById('edit-preview-text').innerText = oldText;
  document.getElementById('edit-preview-time').innerText = oldTime;
  document.getElementById('edit-input').value = oldText;
  document.getElementById('edit-modal').style.display = 'flex';
  document.getElementById('edit-input').focus();
});

// Save edit (calls MessageController@update)
document.getElementById('save-edit').addEventListener('click', function() {
  const newText = document.getElementById('edit-input').value.trim();
  if (!newText) return;

  fetch(`/messages/${selectedMessageId}`, {
    method: 'PUT',
    headers: {
      "X-CSRF-TOKEN": "{{ csrf_token() }}",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ body: newText })
  })
  .then(res => res.json())
  .then(data => {
    const msgDiv = document.querySelector(`.message-wrapper[data-id="${selectedMessageId}"] .message`);
    msgDiv.innerHTML = `<strong>${data.user.name}:</strong> ${data.body}
      <span class="timestamp">${new Date(data.updated_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</span>`;
    document.getElementById('edit-modal').style.display = 'none';
  })
  .catch(err => console.error('Edit failed:', err));
});

// Delete message (calls MessageController@destroy)
document.getElementById('delete-message').addEventListener('click', function() {
  document.getElementById('message-menu').style.display = 'none';

  if (confirm('Delete this message?')) {
    fetch(`/messages/${selectedMessageId}`, {
      method: 'DELETE',
      headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    })
    .then(res => {
      if (res.ok) {
        document.querySelector(`.message-wrapper[data-id="${selectedMessageId}"]`).remove();
      } else {
        console.error('Delete failed:', res.status);
      }
    })
    .catch(err => console.error('Delete error:', err));
  }
});


// Close modal when clicking outside
document.getElementById('edit-modal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});


// Hide menu when clicking elsewhere
document.addEventListener('click', () => {
  document.getElementById('message-menu').style.display = 'none';
});
</script>


<script>
let selectedMessageId = null;

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

  