<form action="{{ route('clubs.faq.update', $club->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div id="faq-fields-container">
    <h4>Manage Club FAQs</h4>
    
        @if(!empty($club->faq) && is_array($club->faq))
            @foreach($club->faq as $index => $item)
                <div class="faq-field-group" style="margin-bottom: 15px; border-bottom: 1px dashed #ccc; padding-bottom: 10px;">
                    
                    <input type="text" 
                        name="faq[{{ $index }}][question]" 
                        value="{{ $item['question'] ?? '' }}" 
                        placeholder="Enter Question" 
                        required 
                        style="display:block; width:100%; margin-bottom:5px;">
                    
                    <textarea name="faq[{{ $index }}][answer]" 
                            placeholder="Enter Answer" 
                            required 
                            style="display:block; width:100%;">{{ $item['answer'] ?? '' }}</textarea>
                    
                    <button type="button" onclick="this.parentElement.remove()" style="margin-top:5px; color:red;">Remove Question</button>
                </div>
            @endforeach
        @endif
    </div>

    <button type="button" id="add-faq-btn">+ Add New Question</button>
    <button type="submit" style="background: green; color: white;">Save All FAQs</button>
    <a href="{{ route('clubs.faq.view', $club->id) }}" style="color: #666; margin-right: 15px; text-decoration: none;">
    Cancel
</a>
<button type="submit" style="background: green; color: white; padding: 8px 15px; border: none; border-radius: 5px;">
    Save All FAQs
</button>,
    @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif
</form>

<script>
    // JavaScript to dynamically append a new blank Q&A template block when clicked
    document.getElementById('add-faq-btn').addEventListener('click', function() {
        const container = document.getElementById('faq-fields-container');
        const index = container.children.length; // Create a unique index
        
        const newField = document.createElement('div');
        newField.className = 'faq-field-group';
        newField.style.marginBottom = '15px';
        newField.style.borderBottom = '1px dashed #ccc';
        newField.style.paddingBottom = '10px'; //  Fixed to camelCase
        
        newField.innerHTML = `
            <input type="text" name="faq[${index}][question]" placeholder="Enter Question" required style="display:block; width:100%; margin-bottom:5px;">
            <textarea name="faq[${index}][answer]" placeholder="Enter Answer" required style="display:block; width:100%;"></textarea>
            <button type="button" onclick="this.parentElement.remove()" style="margin-top:5px; color:red;">Remove Question</button>
        `;
        
        container.appendChild(newField);
    });
</script>