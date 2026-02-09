@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ Edit User</h1>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" id="role" class="form-select" required>
                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student</option>
                <option value="faculty" {{ old('role', $user->role) === 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="mb-3" id="course-group" style="display: {{ old('role', $user->role) === 'student' ? 'block' : 'none' }};">
            <label class="form-label">Course</label>
            <select name="course" class="form-select">
                <option value="">Select Course</option>
                @foreach(['BSIT','BSBA','BSCRIM','BEED', 'BSED'] as $course)
                    <option value="{{ $course }}" {{ old('course', $user->course) === $course ? 'selected' : '' }}>{{ $course }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

@push('scripts')
<script>
const roleSelect = document.getElementById('role');
const courseGroup = document.getElementById('course-group');

roleSelect.addEventListener('change', function() {
    courseGroup.style.display = this.value === 'student' ? 'block' : 'none';
});
</script>
@endpush
@endsection
