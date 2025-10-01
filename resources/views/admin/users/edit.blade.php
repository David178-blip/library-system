@extends('layouts.app')

@section('content')
<div class="container">
    <h1>✏️ Edit User</h1>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="student" @if($user->role === 'student') selected @endif>Student</option>
                <option value="faculty" @if($user->role === 'faculty') selected @endif>Faculty</option>
        
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
