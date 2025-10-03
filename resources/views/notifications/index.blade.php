@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Emails</h2>
    @forelse($notifications as $note)
        <div class="card mb-2 {{ $note->is_read ? '' : 'border-primary' }}">
            <div class="card-body">
                <h5>{{ $note->title }}</h5>
                <p>{{ $note->message }}</p>
                <small>{{ $note->created_at->format('M d, Y h:i A') }}</small>
            </div>
        </div>
    @empty
        <p>No Emails.</p>
    @endforelse
</div>
@endsection
