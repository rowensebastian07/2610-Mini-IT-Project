@extends('layouts.app')

@section('content')
<div class="calendar-container">


    <!-- Calendar header -->
    <div class="calendar-header">
        <button id="prevMonth" class="calendar-arrow">◀</button>
        <h2 id="calendar-title" class="calendar-title"></h2>
        <button id="nextMonth" class="calendar-arrow">▶</button>
    </div>

    <!-- Calendar grid -->
    <table class="calendar-table">
        <thead>
            <tr>
                <th>Sun</th><th>Mon</th><th>Tue</th>
                <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
            </tr>
        </thead>
        <tbody id="calendar-body">
            <!-- JS will fill this -->
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    // Pass events from Laravel to JS
    window.calendarEvents = @json($events);
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endpush

