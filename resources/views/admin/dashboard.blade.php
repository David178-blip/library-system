@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ===== Dashboard Header ===== --}}
    <div class="mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
        <p class="text-muted">Manage books, users, and monitor library activity.</p>
    </div>

 
    {{-- ===== Summary Cards ===== --}}
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Total Books</h6>
                    <h3 class="fw-bold text-primary">{{ $books }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Total Users</h6>
                    <h3 class="fw-bold text-primary">{{ $users }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Books Borrowed</h6>
                    <h3 class="fw-bold text-success">{{ $borrows }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-secondary">Overdue Books</h6>
                    <h3 class="fw-bold text-danger">{{ $overdue }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Recent Borrow Activity ===== --}}
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-clock-history"></i> Recent Borrow Activity
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-primary">
                    <tr>
                        <th>Book</th>
                        <th>User</th>
                        <th>Borrowed</th>
                        <th>Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBorrows as $borrow)
                        <tr>
                            <td>{{ $borrow->book->title ?? 'N/A' }}</td>
                            <td>{{ $borrow->user->name ?? 'N/A' }}</td>
                            <td>{{ $borrow->borrowed_at?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                {{ $borrow->due_at?->format('M d, Y') ?? '—' }}
                                @if($borrow->status === 'borrowed' && $borrow->due_at && $borrow->due_at->isPast())
                                    <span class="badge bg-danger ms-1">Overdue</span>
                                @endif
                            </td>
                            <td>
                                @if($borrow->status === 'borrowed')
                                    <span class="badge bg-info">Borrowed</span>
                                @elseif($borrow->status === 'returned')
                                    <span class="badge bg-success">Returned</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($borrow->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No recent borrow activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== Notifications ===== --}}
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-danger text-white fw-semibold">
            <i class="bi bi-envelope"></i> Emails Sent Today
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-danger">
                    <tr>
                        <th>User</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $note)
                        <tr>
                            <td>{{ $note->user?->name ?? 'N/A' }}</td>
                            <td>{{ $note->title }}</td>
                            <td>{{ $note->message }}</td>
                            <td>{{ $note->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No emails sent yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
