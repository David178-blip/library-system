@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4">
        <h3 class="text-center text-danger mb-4">📘 Library Attendance</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Role</label>
                <select class="form-select" name="role" required>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>

            <button type="submit" class="btn btn-danger w-100">Submit Attendance</button>
        </form>
    </div>
</div>
@endsection
