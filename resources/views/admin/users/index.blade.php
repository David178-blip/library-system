@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">👥 Manage Users</h1>

    <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">➕ Add User</a>

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
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection
