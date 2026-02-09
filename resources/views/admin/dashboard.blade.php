@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ===== Dashboard Header ===== --}}
    <div class="mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
        <p class="text-muted">Manage books, users, and monitor library activity.</p>
    </div>

 
    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.books.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100 clickable-card rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-journal-bookmark-fill text-primary fs-2 mb-2 d-block"></i>
                        <h6 class="text-secondary small text-uppercase mb-1">Total Books</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $books }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100 clickable-card rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-people-fill text-primary fs-2 mb-2 d-block"></i>
                        <h6 class="text-secondary small text-uppercase mb-1">Total Users</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $users }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.reports') }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100 clickable-card rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-journal-check text-success fs-2 mb-2 d-block"></i>
                        <h6 class="text-secondary small text-uppercase mb-1">Borrowed</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $borrows }}</h3>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.reports', ['status' => 'overdue']) }}" class="text-decoration-none text-reset">
                <div class="card border-0 shadow-sm h-100 clickable-card rounded-3">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-2 mb-2 d-block"></i>
                        <h6 class="text-secondary small text-uppercase mb-1">Overdue</h6>
                        <h3 class="fw-bold text-danger mb-0">{{ $overdue }}</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Borrow Activity --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-primary text-white fw-semibold py-3">
            <i class="bi bi-clock-history me-2"></i>Recent Borrow Activity
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

    {{-- Emails Sent Today --}}
    <div class="card mt-4 border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-danger text-white fw-semibold py-3">
            <i class="bi bi-envelope me-2"></i>Emails Sent Today
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
