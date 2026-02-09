@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4 fw-bold text-danger">📘 Library Attendance</h2>

    <div class="card p-4 shadow">

        <h4><strong>Name:</strong> {{ $user->name }}</h4>
        <h5><strong>Role:</strong> {{ ucfirst($user->role) }}</h5>
        <h5><strong>Course:</strong> {{ $user->course ?? '-' }}</h5>

        <h3 class="mt-3">
            Current Time: <span id="clock" class="text-primary fw-bold"></span>
        </h3>

        <hr>

        @if(!$activeAttendance)
            <form action="{{ route('attendance.timein') }}" method="POST">
                @csrf
                <button class="btn btn-success btn-lg w-100">🟢 Time In</button>
            </form>
        @else
            <p><strong>Time In:</strong> {{ $activeAttendance->time_in }}</p>

            <form action="{{ route('attendance.timeout') }}" method="POST">
                @csrf
                <button class="btn btn-danger btn-lg w-100">🔴 Time Out</button>
            </form>
        @endif

    </div>

</div>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString();
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
