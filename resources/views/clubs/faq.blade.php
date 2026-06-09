@extends('layouts.app')

@section('content')
<div class="faq-page-container">
    <div class="container">
        
        <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <a href="{{ route('clubs.show', $club->id) }}" style="text-decoration: none; color: #64748b; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; margin-bottom: 8px; transition: color 0.2s;">
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

        @if($isCommittee)
            <form action="{{ route('clubs.faq.update', $club->id) }}" method="POST">
                @csrf
                @method('PUT')
        @endif

        <div style="margin-top: 20px; margin-bottom: 20px; position: relative;">
            <input type="text" id="faqSearchInput" onkeyup="filterFaqs()" placeholder="🔍 Search through questions and answers..." 
                style="width: 100%; padding: 12px 16px; padding-left: 40px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; font-family: inherit; box-sizing: border-box; outline: none; transition: border 0.15s;"
                onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">
            <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; pointer-events: none;"></span>
        </div>

        <div class="faq-wrapper" id="faq-fields-container" data-success="{{ session('success') ? session('success') : '' }}" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0;">
            
            @forelse($club->faq ?? [] as $item)
                
                <details class="faq-item" style="border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px; overflow: hidden; background: white;">
                    <summary class="faq-question" style="padding: 16px 20px; font-weight: 600; color: #334155; cursor: pointer; background: #f8fafc; user-select: none;">
                        {{ $item['question'] }}
                    </summary>

                    @if($isCommittee)
                        <div class="faq-answer-edit-panel" style="padding: 20px; background: #ffffff; border-top: 1px solid #e2e8f0;">
                            <div style="margin-bottom: 15px;">
                                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px; letter-spacing: 0.05em;">QUESTION</label>
                                <input type="text" name="faq[{{ $loop->index }}][question]" value="{{ $item['question'] }}" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                            </div>

                            <div>
                                <label style="display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:5px; letter-spacing: 0.05em;">ANSWER</label>
                                <textarea name="faq[{{ $loop->index }}][answer]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; resize: vertical;">{{ $item['answer'] }}</textarea>
                            </div>
                            
                            <div style="text-align: right; margin-top: 12px;">
                                <button type="button" onclick="this.closest('.faq-item').remove()" style="background: #ef4444; color: white; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 500;">
                                    🗑️ Delete Row
                                </button>
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
                    <a href="{{ route('clubs.show', $club->id) }}" style="text-decoration: none; background: #e2e8f0; color: #475569; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 14px; transition: background 0.2s;">
                        Cancel Changes
                    </a>
                    
                    <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        💾 Save All Changes
                    </button>
                </div>
            </div>
            </form> 
        @endif
    </div>
</div>

<script src="{{ asset('js/faq.js') }}?v={{ time() }}"></script>
@endsection