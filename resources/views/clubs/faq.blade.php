@extends('layouts.app')

@section('content')
<div class="faq-page-container">
    <div class="container">
        
        <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <a href="{{ route('clubs.show', $club->id) }}" style="text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; margin-bottom: 8px;">
                    ← Back to Club Profile
                </a>
                <h1 class="club-title" style="margin: 0;">{{ $club->name }}</h1>
                <p class="club-subtitle" style="color: #64748b; margin: 4px 0 0 0;">Frequently Asked Questions</p>
            </div>
    
            @if($isCommittee)
                <span style="background: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                    🛠️ Committee Editing Mode Active
                </span>
            @endif
        </div>

        <div style="text-align: right; margin-bottom: 10px;">
            <button type="button" id="faqGlobalToggleBtn" style="background: transparent; color: #2563eb; border: 1px solid #2563eb; padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                ↕️ Expand All
            </button>
        </div>

        <div style="margin-top: 20px; margin-bottom: 20px;">
            <input type="text" id="faqSearchInput" onkeyup="filterFaqs()" placeholder="🔍 Search through questions and answers..." 
                style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box;">
        </div>

        @if($isCommittee)
            <form action="{{ route('clubs.faq.update', $club->id) }}" method="POST" id="faqFormDashboard">
                @csrf
                @method('PUT')
        @endif

            <div class="faq-wrapper" id="faq-fields-container" data-success="{{ session('success') ? session('success') : '' }}" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
                
                @forelse($club->faq ?? [] as $item)
                    <details class="faq-item" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; overflow: hidden; background: white; position: relative;">
                        <summary class="faq-question" 
                            onclick="if(window.isDraggingFAQ) { event.preventDefault(); event.stopPropagation(); } "
                            onmousedown="window.isDraggingFAQ = false;"
                            onmousemove="window.isDraggingFAQ = true;"
                            style="padding: 16px 20px; padding-left: {{ $isCommittee ? '45px' : '20px' }}; font-weight: 600; color: #334155; cursor: {{ $isCommittee ? 'grab' : 'pointer' }}; background: #f8fafc; user-select: none; position: relative; display: flex; justify-content: space-between; align-items: center;">
                                            
                            @if($isCommittee)
                                <span class="drag-handle" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; cursor: grab; font-size: 18px; padding: 4px; z-index: 10;">☰</span>
                            @endif

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="faq-arrow">▶</span>
                                <span>{{ $item['question'] }}</span>
                            </div>

                            @if($isCommittee)
                                <button type="button" 
                                        onclick="event.stopPropagation(); this.closest('.faq-item').remove(); if(typeof updateFormIndexes === 'function') { updateFormIndexes(); }" 
                                        style="background: transparent; color: #ef4444; border: none; padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; display: inline-flex; align-items: center; justify-content: center; z-index: 11; transition: background 0.2s;"
                                        onmouseover="this.style.background='#fee2e2'"
                                        onmouseout="this.style.background='transparent'"
                                        title="Delete FAQ Item">
                                    🗑️
                                </button>
                            @endif
                        </summary>      

                        @if($isCommittee)
                            <div class="faq-answer-edit-panel" style="padding: 20px; background: #ffffff; border-top: 1px solid #e2e8f0;">
                                <div style="margin-bottom: 15px;">
                                    <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">QUESTION</label>
                                    <input type="text" name="faq[{{ $loop->index }}][question]" value="{{ $item['question'] }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                                </div>

                                <div>
                                    <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">ANSWER</label>
                                    <textarea name="faq[{{ $loop->index }}][answer]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; resize: vertical;" required>{{ $item['answer'] }}</textarea>
                                </div>
                            </div>
                        @else
                            <div class="faq-answer" style="padding: 20px; background: #ffffff; border-top: 1px solid #e2e8f0; color: #475569; line-height: 1.6;">
                                <p style="margin: 0;">{{ $item['answer'] }}</p>
                            </div>
                        @endif
                    </details>
                @empty
                    <div class="fallback-box" style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px;">
                        <h3 style="margin: 0 0 8px 0; color: #1e293b;">No FAQs listed yet</h3>
                        <p style="margin: 0; color: #64748b;">Check back later or contact club management for more information.</p>
                    </div>
                @endforelse
            </div>

            @if($isCommittee)
                <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <button type="button" onclick="addNewFaqRow()" style="background: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        ➕ Add New Question
                    </button>

                    <div style="display: flex; gap: 12px; align-items: center;">
                        <button type="reset" onclick="window.location.reload();" style="background: #e2e8f0; color: #475569; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">
                            Cancel Changes
                        </button>

                        <button type="submit" id="faqSaveBtn" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            💾 Save All Changes
                        </button>
                    </div>
                </div>
            </form> 
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('faqGlobalToggleBtn');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const faqItems = document.querySelectorAll('.faq-item');
            if (faqItems.length === 0) return;

            const isAnyOpen = Array.from(faqItems).some(item => item.hasAttribute('open'));

            faqItems.forEach(item => {
                if (isAnyOpen) {
                    item.removeAttribute('open');
                } else {
                    item.setAttribute('open', 'true');
                }
            });

            this.innerHTML = isAnyOpen ? "↕️ Expand All" : "↕️ Collapse All";
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="{{ asset('js/faq.js') }}?v={{ time() }}"></script>
@endsection