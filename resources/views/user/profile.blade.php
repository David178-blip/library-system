@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1 class="mb-4">My Profile</h1>

    <div class="card p-4">
        <h3>{{ $user->name }}</h3>
        <p>Email: {{ $user->email }}</p>
        <p>Role: {{ ucfirst($user->role) }}</p>

        <h4 class="mt-4">My QR Code</h4>
        <div class="p-3 border rounded bg-light">
            {!! $qrCode !!}
        </div>
    </div>
</div>
@endsection
