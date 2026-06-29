@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2 class="form-title">Create Post</h2>
    <p class="form-subtitle">Fill in the details below</p>

    {{-- ✅ Corrected form action: pass club explicitly --}}
    <form action="{{ route('posts.store', ['club' => $club->id]) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="styled-form">
        @csrf

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" required></textarea>
        </div>

        <div class="form-group">
            <label for="media">Upload Images</label>
            {{-- ✅ Only images allowed --}}
            <input type="file" name="media[]" id="media" accept="image/*" multiple>
            <small class="form-text text-muted">You can add more images anytime — they’ll stack up below.</small>
            <p id="file-count" class="file-count">Total files selected: 0</p>
        </div>

        <div id="preview-container" class="preview-container" style="display:none;">
            <button type="button" id="prev-btn" class="nav-btn">←</button>
            <div id="preview-wrapper" class="preview-wrapper"></div>
            <button type="button" id="next-btn" class="nav-btn">→</button>
        </div>

        <button type="submit" class="btn-submit" style="margin-top: 2rem;">Save Post</button>
    </form>
</div>
@endsection

@push('styles')
<style>
.preview-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 15px;
}
.preview-wrapper {
  width: 320px;
  height: 220px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.preview-wrapper img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 8px;
}
.nav-btn {
  background: #e0245e;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.nav-btn:hover { background: #c81e4d; }
.file-count {
  margin-top: 5px;
  font-size: 14px;
  color: #555;
}


</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const input = document.getElementById('media');
    const previewContainer = document.getElementById('preview-container');
    const previewWrapper = document.getElementById('preview-wrapper');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const fileCount = document.getElementById('file-count');

    let files = [];
    let currentIndex = 0;

    input.addEventListener('change', function() {
        const dt = new DataTransfer();
        files = files.concat(Array.from(this.files));

        files.forEach(file => dt.items.add(file));
        input.files = dt.files;

        fileCount.textContent = `Total files selected: ${files.length}`;

        if (files.length > 0) {
            previewContainer.style.display = 'flex';
            showPreview(currentIndex);
        }
    });

    function showPreview(index) {
        const file = files[index];
        const reader = new FileReader();
        reader.onload = function(e) {
            previewWrapper.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }

    prevBtn.addEventListener('click', function() {
        if (files.length > 0) {
            currentIndex = (currentIndex - 1 + files.length) % files.length;
            showPreview(currentIndex);
        }
    });

    nextBtn.addEventListener('click', function() {
        if (files.length > 0) {
            currentIndex = (currentIndex + 1) % files.length;
            showPreview(currentIndex);
        }
    });
});
</script>
@endpush
