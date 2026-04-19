@extends('layouts.admin')

{{-- تحديد العناوين الديناميكية --}}
@section('title', 'تقويم الإجازات')
@section('content_header', 'تقويم الإجازات')

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="calendar" style="min-height: 500px;"></div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ar',
                direction: 'rtl',
                events: "{{ route('admin.leaves.events') }}"
            });
            calendar.render();
        });
    </script>
@endsection