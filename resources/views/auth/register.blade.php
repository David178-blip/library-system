@extends('layouts.app')

@section('content')
 {{-- 🔙 Back to Welcome Button --}}
        <div class="mb-4 text-start">
            <a href="{{ url('/') }}" class="btn btn-outline-danger rounded-3">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo" class="mb-3" style="width: 70px;">
                    <h2 class="fw-bold text-danger mb-1">Create an Account ✨</h2>
                    <p class="text-muted mb-0">Join the Library System and start borrowing books today</p>
                </div>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Register Form --}}
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-danger">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person text-danger"></i>
                            </span>
                            <input id="name" type="text" 
                                   class="form-control border-start-0 rounded-end-3 @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-danger">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-danger"></i>
                            </span>
                            <input id="email" type="email" 
                                   class="form-control border-start-0 rounded-end-3 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold text-danger">Register as</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-badge text-danger"></i>
                            </span>
                            <select id="role" name="role" class="form-select border-start-0 rounded-end-3" required>
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-danger">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-danger"></i>
                            </span>
                            <input id="password" type="password" 
                                   class="form-control border-start-0 rounded-end-3 @error('password') is-invalid @enderror" 
                                   name="password" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label fw-semibold text-danger">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-shield-check text-danger"></i>
                            </span>
                            <input id="password-confirm" type="password" 
                                   class="form-control border-start-0 rounded-end-3" 
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg rounded-3 fw-semibold shadow-sm">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">
                        Login here
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Fade-in animation --}}
@push('styles')
<style>
    .card {
        animation: fadeInUp 0.6s ease;
    }
    @keyframes fadeInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
@endpush
@endsection
