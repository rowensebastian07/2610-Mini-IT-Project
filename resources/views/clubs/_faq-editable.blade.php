<details class="faq-item" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; overflow: hidden; background: white; position: relative;">
    
    <summary class="faq-question" style="padding: 16px 20px; padding-left: {{ $isCommittee ? '45px' : '20px' }}; font-weight: 600; color: #334155; cursor: pointer; background: #f8fafc; user-select: none; position: relative;">
        @if($isCommittee)
            <span class="drag-handle" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; cursor: grab; font-size: 18px;">☰</span>
        @endif
        {{ $item['question'] }}
    </summary>

    <div class="faq-answer-edit-panel" style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">QUESTION</label>
            <input type="text" name="faq[{{ $loop->index }}][question]" value="{{ $item['question'] }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">ANSWER</label>
            <textarea name="faq[{{ $loop->index }}][answer]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; resize: vertical; box-sizing: border-box;">{{ $item['answer'] }}</textarea>
        </div>
        
        <div style="text-align: right; margin-top: 10px;">
            <button type="button" class="btn-delete" onclick="this.closest('.faq-item').remove(); if(typeof updateFormIndexes === 'function') { updateFormIndexes(); }" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 500;">
                🗑️ Delete Question
            </button>
        </div>
    </div>
</details>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

<script src="{{ asset('js/faq.js') }}?v={{ time() }}"></script>