<div class="faq-answer-edit-panel" style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
    <div style="margin-bottom: 15px;">
        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">QUESTION</label>
        <input type="text" name="faq[{{ $loop->index }}][question]" value="{{ $item['question'] }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
    </div>

    <div>
        <label style="display:block; font-size:12px; font-weight:600; color:#64748b; margin-bottom:5px;">ANSWER</label>
        <textarea name="faq[{{ $loop->index }}][answer]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; resize: vertical;">{{ $item['answer'] }}</textarea>
    </div>
    
    <div style="text-align: right; margin-top: 10px;">
        <button type="button" class="btn-delete" onclick="this.closest('.faq-item').remove()" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
            🗑️ Delete Question
        </button>
    </div>
</div>