@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">👥 Manage Users</h1>

    <!-- FILTERS -->
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            🔎 Filter Users
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search by name...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Search by email...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="faculty" {{ request('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <option value="BSIT" {{ request('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSBA" {{ request('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                            <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM</option>
                            <option value="BEED" {{ request('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                            <option value="BSED" {{ request('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-striped table-hover shadow">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Course</th> <!-- Added Course Column -->
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge 
                            @if($user->role === 'admin') bg-danger 
                            @elseif($user->role === 'faculty') bg-primary 
                            @else bg-success @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        {{ $user->role === 'student' ? $user->course : '-' }}
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
@endsection
