@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-book"></i> Student Dashboard</h1>
        <p class="text-muted">Track your borrowed and returned books.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-4">
                    <i class="bi bi-journal-bookmark text-primary fs-2 mb-2 d-block"></i>
                    <h6 class="text-secondary small text-uppercase mb-1">Active Borrows</h6>
                    <h3 class="fw-bold text-primary mb-0">{{ $borrowedCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-2 mb-2 d-block"></i>
                    <h6 class="text-secondary small text-uppercase mb-1">Overdue</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ $overdueCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body text-center py-4">
                    <i class="bi bi-clock-history text-success fs-2 mb-2 d-block"></i>
                    <h6 class="text-secondary small text-uppercase mb-1">Total History</h6>
                    <h3 class="fw-bold text-success mb-0">{{ $borrows->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Borrow History --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-primary text-white fw-semibold py-3">
            <i class="bi bi-clock-history me-2"></i>Borrow History
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-primary">
                        <tr>
                            <th>Book</th>
                            <th>Borrowed</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrows as $borrow)
                            <tr>
                                <td class="fw-semibold">{{ $borrow->book->title ?? 'N/A' }}</td>
                                <td>{{ $borrow->borrowed_at?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $borrow->due_at?->format('M d, Y') ?? '—' }}</td>
                                <td>
                                    @if($borrow->status === 'borrowed')
                                        <span class="badge bg-info">Borrowed</span>
                                    @elseif($borrow->status === 'returned')
                                        <span class="badge bg-success">Returned</span>
                                    @elseif($borrow->due_at && $borrow->due_at->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($borrow->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                    <p class="mb-0">You haven't borrowed any books yet.</p>
                                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm mt-2 rounded-3">Browse Books</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
