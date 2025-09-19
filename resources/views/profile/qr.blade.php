@extends('layouts.app')

@section('content')
<h3>Your QR Code</h3>
<div class="card text-center">
    <div class="card-body">
        {!! $qr !!}
        <p class="mt-2"><strong>{{ $user->name }}</strong></p>
        <p>{{ ucfirst($user->role) }}</p>
    </div>
</div>
@endsection
