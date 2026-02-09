@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Notifications</h2>
    @forelse($notifications as $note)
        @php
            $cardClass = '';
            $badgeClass = null;
            $badgeText = null;

            if ($note->title === 'Book Overdue') {
                $cardClass  = 'border-danger';
                $badgeClass = 'bg-danger';
                $badgeText  = 'Overdue';
            } elseif ($note->title === 'Book Due Today') {
                $cardClass  = 'border-warning';
                $badgeClass = 'bg-warning text-dark';
                $badgeText  = 'Due Today';
            } elseif ($note->title === 'Book Due Soon') {
                $cardClass  = 'border-info';
                $badgeClass = 'bg-info text-dark';
                $badgeText  = 'Due Soon';
            } elseif (! $note->is_read) {
                $cardClass = 'border-primary';
            }
        @endphp

        <div class="card mb-2 {{ $cardClass }}">
            <div class="card-body d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center mb-1">
                        @if($badgeText)
                            <span class="badge {{ $badgeClass }} me-2">{{ $badgeText }}</span>
                        @endif
                        <h5 class="mb-0">{{ $note->title }}</h5>
                    </div>
                    <p class="mb-1 mt-1">{{ $note->message }}</p>
                    <small class="text-muted">{{ $note->created_at->format('M d, Y h:i A') }}</small>
                </div>
                <form action="{{ route('notifications.destroy', $note) }}" method="POST" class="ms-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete notification">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <p>No notifications.</p>
    @endforelse
</div>
@endsection
