<x-top-nav></x-top-nav>
@extends('layouts.app')

@section('content')
<!-- Sub-header -->
<div class="sub-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
<!-- Left spacer -->
<div style="flex:1;"></div>

<!-- Page title centered -->
<h1 style="flex:1; text-align:center; margin:0;">Events Calendar</h1>

<!-- Right section (optional button) -->
<div style="flex:1; text-align:right;">

</div>
</div>

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
   // Debug: log events to console
   console.log("Calendar Events:", @json($events));

   // Pass events to JS
   window.calendarEvents = @json($events);
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endpush

