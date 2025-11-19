@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-book"></i> Student Dashboard</h1>
        <p class="text-muted">Track your borrowed and returned books.</p>
    </div>

    {{-- ===== Summary Cards ===== --}}
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Books Borrowed</h6>
                    <h3 class="fw-bold text-primary">{{ $borrowedCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Books Returned</h6>
                    <h3 class="fw-bold text-success">{{ $returnedCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-secondary">Overdue Books</h6>
                    <h3 class="fw-bold text-danger">{{ $overdueCount ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Borrow History ===== --}}
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-clock-history"></i> Borrow History
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-primary">
                    <tr>
                        <th>Book</th>
                        <th>Borrowed At</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrows as $borrow)
                        <tr>
                            <td>{{ $borrow->book->title ?? 'N/A' }}</td>
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
                            <td colspan="4" class="text-center text-muted py-3">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
