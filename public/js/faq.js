/**
 * Modern FAQ Management Script
 * Handles: Drag-and-drop sorting, dynamic addition/deletion, index updating, non-blocking toast popups, and live filtering.
 */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('faq-fields-container');
    const hasDragHandles = document.querySelector('.drag-handle') !== null;
    
    //initial database count baseline
    window.initialFaqCount = container ? container.querySelectorAll('.faq-item').length : 0;

    // 1. Initialize SortableJS
    if (container && hasDragHandles && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            animation: 150,
            handle: '.faq-question', 
            draggable: '.faq-item',
            filter: 'button, input, textarea', 
            preventOnFilter: false,
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateFormIndexes();
                markAsUnsaved(); // Reordering always triggers an unsaved change status
            }
        });
    }

    // 2. Track real-time typing changes across all inputs
    if (container) {
        container.addEventListener('input', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                markAsUnsaved(); // Typing instantly activates the save indicator button
            }
        });
    }

    // 3. Catch and fire backend status success messages 
    if (container) {
        const successMessage = container.getAttribute('data-success');
        if (successMessage && successMessage.trim() !== '') {
            showToast(successMessage);
        }
    }

    // Mark application setup complete to enable save-change checking flags
    window.faqSetupComplete = true;
});

/**
 * Dynamically injects a fresh FAQ item card into the container area
 */
function addNewFaqRow() {
    const container = document.getElementById('faq-fields-container');
    if (!container) return;

    const fallbackBox = container.querySelector('.fallback-box');
    if (fallbackBox) {
        fallbackBox.remove();
    }

    const index = container.querySelectorAll('.faq-item').length;
    const newRow = document.createElement('details');
    
    newRow.className = 'faq-item';
    newRow.setAttribute('open', 'true'); 
    newRow.style.cssText = 'border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; overflow: hidden; background: white; position: relative;';

    newRow.innerHTML = `
        <summary class="faq-question" 
                 onclick="if(window.isDraggingFAQ) { event.preventDefault(); event.stopPropagation(); }"
                 onmousedown="window.isDraggingFAQ = false;"
                 onmousemove="window.isDraggingFAQ = true;"
                 style="padding: 16px 20px; padding-left: 45px; font-weight: 600; color: #334155; cursor: grab; background: #f8fafc; user-select: none; position: relative; display: flex; justify-content: space-between; align-items: center;">
            <span class="drag-handle" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 18px; padding: 4px; z-index: 10;">☰</span>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="faq-arrow">▶</span>
                <span>New Frequently Asked Question</span>
            </div>
            
            <button type="button" 
                    onclick="event.stopPropagation(); this.closest('.faq-item').remove(); updateFormIndexes(); handleCardDeletion();" 
                    style="background: transparent; color: #ef4444; border: none; padding: 6px; border-radius: 6px; cursor: pointer; font-size: 16px; display: inline-flex; align-items: center; justify-content: center; z-index: 11; transition: background 0.2s;"
                    title="Delete FAQ Item">
                🗑️
            </button>
        </summary>
        <div class="faq-answer-edit-panel" style="padding: 20px; background: #ffffff; border-top: 1px solid #e2e8f0;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">QUESTION</label>
                <input type="text" name="faq[${index}][question]" placeholder="Type your question here..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;" required>
            </div>
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px;">ANSWER</label>
                <textarea name="faq[${index}][answer]" rows="3" placeholder="Type the answer here..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; resize: vertical;" required></textarea>
            </div>
        </div>
    `;

    container.appendChild(newRow);
    updateFormIndexes(); // Updates array field name structures silently
}

/**
 * Loops through all active FAQ cards on page and forces sequential indexing indices 0, 1, 2...
 */
function updateFormIndexes() {
    const container = document.getElementById('faq-fields-container');
    if (!container) return;

    const items = container.querySelectorAll('.faq-item');
    
    // 1. Re-index form input field names sequentially
    items.forEach((item, idx) => {
        const questionInput = item.querySelector('input[name*="[question]"]');
        const answerTextarea = item.querySelector('textarea[name*="[answer]"]');
        
        if (questionInput) questionInput.setAttribute('name', `faq[${idx}][question]`);
        if (answerTextarea) answerTextarea.setAttribute('name', `faq[${idx}][answer]`);
    });

    // 2. Fallback State Management placeholder box injection check
    if (items.length === 0) {
        if (!container.querySelector('.fallback-box')) {
            const fallbackHTML = `
                <div class="fallback-box" style="text-align: center; padding: 40px 20px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px;">
                    <h3 style="margin: 0 0 8px 0; color: #1e293b;">No FAQs listed yet</h3>
                    <p style="margin: 0; color: #64748b;">Check back later or contact club management for more information.</p>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', fallbackHTML);
        }
    }
}

/**
 * Explicit change tracking trigger for item deletion cleanup loops
 */
function handleCardDeletion() {
    const container = document.getElementById('faq-fields-container');
    if (!container || !window.faqSetupComplete) return;

    const currentCount = container.querySelectorAll('.faq-item').length;

    // Turn button amber ONLY if the deletion left us with a different count than our database starting point
    if (currentCount !== window.initialFaqCount) {
        markAsUnsaved();
    }
}

/**
 * Real-time filter engine to search through questions and answers
 */
function filterFaqs() {
    const input = document.getElementById('faqSearchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase().trim();
    const items = document.querySelectorAll('.faq-item');

    items.forEach(item => {
        const questionText = item.querySelector('.faq-question div span:last-child')?.textContent || '';
        const inputVal = item.querySelector('input[type="text"]')?.value || '';
        const textVal = item.querySelector('textarea')?.value || '';
        const staticAnswer = item.querySelector('.faq-answer p')?.textContent || '';

        const dynamicCombinedSearchText = `${questionText} ${inputVal} ${textVal} ${staticAnswer}`.toLowerCase();

        if (dynamicCombinedSearchText.includes(filter)) {
            item.style.display = ''; 
        } else {
            item.style.display = 'none'; 
        }
    });
}

/**
 * Visual indicator highlighting the save button when changes are pending
 */
function markAsUnsaved() {
    const saveBtn = document.getElementById('faqSaveBtn');
    if (saveBtn && !saveBtn.classList.contains('btn-unsaved-changes')) {
        saveBtn.classList.add('btn-unsaved-changes');
        saveBtn.innerHTML = '⚠️ Save Pending Changes';
    }
}

/**
 * Glides a sleek toast message into the corner without blocking layout control elements
 */
function showToast(message) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; background: #0f172a; color: #ffffff;
        padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; z-index: 9999;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 8px;
        transform: translateY(-20px); opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    `;
    
    toast.innerHTML = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    }, 10);

    setTimeout(() => {
        toast.style.transform = 'translateY(-20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}