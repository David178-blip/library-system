@extends('layouts.app')

@section('content')
<h3>Profile</h3>
<div class="card">
    <div class="card-body">
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <a href="{{ route('profile.qr') }}" class="btn btn-dark">View QR Code</a>
    </div>
</div>
@endsection
