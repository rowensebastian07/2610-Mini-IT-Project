// FEATURE 1: DYNAMIC FAQ ROW GENERATION
function addNewFaqRow() {
    const container = document.getElementById('faq-fields-container');
    const index = container.querySelectorAll('.faq-item').length;
    
    const fallback = container.querySelector('.fallback-box');
    if (fallback) fallback.remove();

    const newRow = document.createElement('details');
    newRow.className = 'faq-item';
    newRow.setAttribute('open', 'true');
    newRow.style.cssText = "border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; overflow: hidden; background: white;";
    
    newRow.innerHTML = `
        <summary class="faq-question" style="padding: 16px 20px; font-weight: 600; color: #334155; cursor: pointer; background: #f8fafc; user-select: none;">
            New Frequently Asked Question
        </summary>
        <div class="faq-answer-edit-panel" style="padding: 20px; background: #ffffff; border-top: 1px solid #e2e8f0;">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px; letter-spacing: 0.05em;">QUESTION</label>
                <input type="text" name="faq[` + index + `][question]" placeholder="Type question line text here..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px; letter-spacing: 0.05em;">ANSWER</label>
                <textarea name="faq[` + index + `][answer]" rows="3" placeholder="Type data statement details here..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; resize: vertical;"></textarea>
            </div>
            <div style="text-align: right; margin-top: 12px;">
                <button type="button" onclick="this.closest('.faq-item').remove()" style="background: #ef4444; color: white; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                    🗑️ Delete Row
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
}

// FEATURE 2: REAL-TIME SEARCH FILTER
function filterFaqs() {
    const query = document.getElementById('faqSearchInput').value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    let visibleCount = 0;

    faqItems.forEach(item => {
        const summaryText = item.querySelector('.faq-question').textContent.toLowerCase();
        
        const inputField = item.querySelector('input[type="text"]');
        const textField = item.querySelector('textarea');
        const answerText = item.querySelector('.faq-answer');
        
        let contentValue = "";
        if (inputField && textField) {
            contentValue = inputField.value.toLowerCase() + " " + textField.value.toLowerCase();
        } else if (answerText) {
            contentValue = answerText.textContent.toLowerCase();
        }

        if (summaryText.includes(query) || contentValue.includes(query)) {
            item.style.display = "";
            visibleCount++;
            
            if (query.trim() !== "") {
                item.setAttribute('open', 'true');
            } else {
                item.removeAttribute('open');
            }
        } else {
            item.style.display = "none";
            item.removeAttribute('open');
        }
    });

    const existingMsg = document.getElementById('no-search-results');
    if (visibleCount === 0 && query.trim() !== "") {
        if (!existingMsg) {
            const noResults = document.createElement('div');
            noResults.id = 'no-search-results';
            noResults.style.cssText = "text-align: center; padding: 30px; color: #64748b; font-size: 15px;";
            noResults.innerHTML = "❌ No matches found for your search query.";
            document.getElementById('faq-fields-container').appendChild(noResults);
        }
    } else if (existingMsg) {
        existingMsg.remove();
    }
}

// FEATURE 3: SECURE SUCCESS POPUP WITH HISTORY SHIELD
document.addEventListener('DOMContentLoaded', function() {
    const faqContainer = document.getElementById('faq-fields-container');
    if (!faqContainer) return;

    const successMessage = faqContainer.getAttribute('data-success');

    // If a success message exists in the HTML attribute, show it immediately!
    if (successMessage && successMessage.trim() !== "") {
        alert("🎉 " + successMessage);
        
        // Clean up the browser history timeline state
        if (window.history.replaceState) {
            window.history.replaceState(null, document.title, window.location.href);
        }
    }
});