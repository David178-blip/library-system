@extends('layouts.app')

@section('content')
  {{-- 🔙 Back to Welcome Button --}}
        <div class="mb-4 text-start">
            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-3">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
<div class="row justify-content-center align-items-center min-vh-100">
    
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="text-center mb-4">
                    
                    <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo" class="mb-3" style="width: 70px;">
                    <h2 class="fw-bold text-primary mb-1">Welcome Back 👋</h2>
                    <p class="text-muted mb-0">Login to access the Library System</p>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Login Form --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-primary"></i>
                            </span>
                            <input id="email" type="email" 
                                   class="form-control border-start-0 rounded-end-3 @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock text-primary"></i>
                            </span>
                            <input id="password" type="password" 
                                   class="form-control border-start-0 rounded-end-3 @error('password') is-invalid @enderror"
                                   name="password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted" for="remember">Remember Me</label>
                        </div>
                       
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login
                        </button>
                    </div>
                </form>

                <hr class="my-4">
                <p class="text-center mb-0">
                    Don’t have an account?
                    <a href="{{ route('register') }}" class="fw-semibold text-danger text-decoration-none">
                        Register here
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Subtle Animation --}}
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
