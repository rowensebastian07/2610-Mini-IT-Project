@extends('layouts.app')

@section('content')
<h2>{{ $event->title }} - Uploaded Photos</h2>

@if($files)
<div class="gallery-grid">
    @foreach($files as $index => $file)
        <div class="gallery-item">
            {{-- Thumbnail that opens lightbox --}}
            <img src="{{ asset('storage/' . $file) }}" 
                 alt="Event Photo" 
                 onclick="openLightbox({{ $index }})">

            {{-- Delete button (visible to all for now) --}}
            <form action="{{ route('events.deletePhoto', ['event' => $event->id]) }}" 
                  method="POST" 
                  onsubmit="return confirm('Delete this photo?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="file_path" value="{{ $file }}">
                <button type="submit" class="btn-red">Delete</button>
            </form>
        </div>
    @endforeach
</div>

{{-- Lightbox modal --}}
<div id="lightbox" class="lightbox">
    <span class="close" onclick="closeLightbox()">&times;</span>
    <img id="lightbox-img" src="">
    <a class="prev" onclick="changePhoto(-1)">&#10094;</a>
    <a class="next" onclick="changePhoto(1)">&#10095;</a>
</div>

<style>
.gallery-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.gallery-item {
    width: 180px;
    text-align: center;
}

.gallery-item img {
    width: 100%;
    border-radius: 6px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.gallery-item img:hover {
    transform: scale(1.05);
}

.btn-red {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 6px;
    font-size: 13px;
}

.btn-red:hover {
    background-color: #c0392b;
}

.lightbox {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 60px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.9);
    text-align: center;
}

.lightbox img {
    max-width: 80%;
    max-height: 80%;
}

.close {
    position: absolute;
    top: 20px; right: 35px;
    color: #fff;
    font-size: 40px;
    cursor: pointer;
}

.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    font-size: 40px;
    color: #fff;
    padding: 16px;
    user-select: none;
}

.prev { left: 0; }
.next { right: 0; }
</style>

{{--  JavaScript for lightbox + keyboard arrows --}}
<script>
    let photos = @json($files);
    let currentIndex = 0;

    function openLightbox(index) {
        currentIndex = index;
        document.getElementById('lightbox').style.display = 'block';
        document.getElementById('lightbox-img').src = '/storage/' + photos[index];
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }

    function changePhoto(direction) {
        currentIndex += direction;
        if (currentIndex < 0) currentIndex = photos.length - 1;
        if (currentIndex >= photos.length) currentIndex = 0;
        document.getElementById('lightbox-img').src = '/storage/' + photos[currentIndex];
    }

    // Keyboard arrow + Esc support
    document.addEventListener('keydown', function(event) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'block') {
            if (event.key === 'ArrowLeft') {
                changePhoto(-1);
            } else if (event.key === 'ArrowRight') {
                changePhoto(1);
            } else if (event.key === 'Escape') {
                closeLightbox();
            }
        }
    });
</script>
@endif
@endsection
