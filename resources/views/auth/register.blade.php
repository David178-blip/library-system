@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 text-success fw-bold">Create an Account ✨</h2>
                <p class="text-center text-muted mb-4">Join the Library System and start borrowing books today</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input id="name" type="text" 
                               class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input id="email" type="email" 
                               class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Register as</label>
                        <select id="role" name="role" class="form-select form-select-lg rounded-3" required>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input id="password" type="password" 
                               class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" 
                               name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
                        <input id="password-confirm" type="password" 
                               class="form-control form-control-lg rounded-3" 
                               name="password_confirmation" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg rounded-3">Register</button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center">Already have an account? 
                    <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
