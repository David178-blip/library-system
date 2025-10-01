@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg text-center" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            <h3 class="mb-3">Your QR Code</h3>
            <p class="text-muted">Scan this QR to identify yourself in the library system.</p>
            <div class="d-flex justify-content-center">
                {!! $qr !!}
            </div>
            <p class="mt-3"><strong>{{ $user->name }}</strong></p>
            <p class="text-muted">{{ $user->email }}</p>
        </div>
    </div>
</div>
@endsection
