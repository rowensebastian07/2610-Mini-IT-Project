<div class="faq-display-container" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>{{ $club->name }} - Frequently Asked Questions</h2>
        
        @if($isCommittee)
            <a href="{{ route('clubs.faq.edit', $club->id) }}" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">
                ⚙️ Edit FAQs
            </a>
        @endif
    </div>

    <hr>

    @if(!empty($club->faq) && is_array($club->faq))
        @foreach($club->faq as $item)
            <details style="background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; cursor: pointer;">
                <summary style="font-weight: bold; font-size: 1.1em; color: #333;">
                    {{ $item['question'] }}
                </summary>
                <p style="margin-top: 10px; color: #666; line-height: 1.6; padding-left: 15px;">
                    {{ $item['answer'] }}
                </p>
            </details>
        @endforeach
    @else
        <p style="text-align: center; color: #999; margin-top: 30px;">No FAQs have been added by this club yet.</p>
    @endif

</div>