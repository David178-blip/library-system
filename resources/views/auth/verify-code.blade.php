@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 480px;">
    <h2 class="mb-3">Verify your account</h2>
    <p>We sent a 6-digit verification code to <strong>{{ $user->email }}</strong>. Enter it below. The code expires in 10 minutes.</p>

    @if(session('status') === 'verification-code-sent')
        <div class="alert alert-success">A new verification code has been sent to your email.</div>
    @endif

    @if($errors->has('code'))
        <div class="alert alert-danger">{{ $errors->first('code') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.code.verify') }}">
        @csrf
        <div class="mb-3">
            <label for="code" class="form-label">Verification code</label>
            <input id="code" type="text" name="code" class="form-control" required autofocus maxlength="6">
        </div>
        <button type="submit" class="btn btn-primary w-100">Verify</button>
    </form>

    <form method="POST" action="{{ route('verification.code.resend') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-link p-0">Resend code</button>
    </form>
</div>
@endsection
