@extends('layouts.app')

@section('content')
@php($borrows = $borrows ?? collect())
<div class="container mt-4">
    <h3 class="text-center text-danger mb-4">📅 Attendance Report</h3>

    <!-- Download PDF -->
    <a href="{{ route('admin.attendance.download', request()->query()) }}" class="btn btn-success mb-3">
        ⬇️ Download PDF Report
    </a>

    <!-- FILTERS -->
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            🔎 Filter Records
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.report') }}">
                <div class="row g-3">

                    <!-- User Name -->
                    <div class="col-md-4">
                        <label class="form-label">User Name</label>
                        <input type="text" name="user" value="{{ request('user') }}" class="form-control" placeholder="Search user...">
                    </div>

                    <!-- Role -->
                    <div class="col-md-4">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">All</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="faculty" {{ request('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <!-- Course -->
                    <div class="col-md-2">
                        <label class="form-label">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <option value="BSIT" {{ request('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM</option>
                            <option value="BSBA" {{ request('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                            <option value="BSED" {{ request('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                            <option value="BEED" {{ request('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                        </select>
                    </div>


                    <!-- Date From -->
                    <div class="col-md-4">
                        <label class="form-label">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                    </div>

                    <!-- Date To -->
                    <div class="col-md-4">
                        <label class="form-label">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                    </div>

                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- ATTENDANCE TABLE -->
    <div class="card shadow">
        <div class="card-body p-0">
            @if($records->isEmpty())
                <p class="text-center py-4 text-muted">No attendance records found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Course</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $index => $rec)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $rec->user->name }}</td>
                                    <td>{{ ucfirst($rec->user->role) }}</td>
                                    <td>{{ $rec->user->course ?? '-' }}</td>
                                    <td>{{ $rec->time_in }}</td>
                                    <td>{{ $rec->time_out ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3">
                    {{ $records->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection