@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 text-primary fw-bold">Welcome Back 👋</h2>
                <p class="text-center text-muted mb-4">Login to access the Library System</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input id="email" type="email" 
                               class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input id="password" type="password" 
                               class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror" 
                               name="password" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3">Login</button>
                    </div>

                   
                </form>

                <hr class="my-4">
                <p class="text-center">Don’t have an account? 
                    <a href="{{ route('register') }}" class="fw-semibold text-success text-decoration-none">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
