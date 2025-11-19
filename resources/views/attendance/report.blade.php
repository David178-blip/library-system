@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-center text-danger mb-4">📅 Attendance Report</h3>

    <table class="table table-bordered table-striped shadow-sm">
        <thead class="table-danger">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Role</th>
                <th>Time In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $rec)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rec->name }}</td>
                    <td>{{ ucfirst($rec->role) }}</td>
                    <td>{{ $rec->time_in }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
