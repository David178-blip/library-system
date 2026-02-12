@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('images/hccd_logo.png') }}" alt="Logo" class="mb-3" style="width: 70px;">
                    <h2 class="fw-bold text-primary mb-1">Reset Password 🔄</h2>
                    <p class="text-muted mb-0 small">Enter your new credentials below.</p>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope text-primary"></i>
                            </span>
                            <input id="email" type="email" 
                                   class="form-control border-start-0 rounded-end-3 @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $request->email) }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock-fill text-primary"></i>
                            </span>
                            <input id="password" type="password" 
                                   class="form-control border-start-0 rounded-end-3 @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock-check text-primary"></i>
                            </span>
                            <input id="password_confirmation" type="password" 
                                   class="form-control border-start-0 rounded-end-3"
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i> Reset Password
                        </button>
                    </div>
                </form>
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
