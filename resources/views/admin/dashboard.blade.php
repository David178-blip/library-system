@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">📚 Admin Dashboard</h1>
        <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('books.create') }}" class="btn btn-success">
            ➕ Add New Book
        </a>
    </div>

    {{-- Top Stats --}}
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <p class="card-text display-6">{{ $books }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-6">{{ $users }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Books Borrowed</h5>
                    <p class="card-text display-6">{{ $borrows }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-dark mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">Overdue Books</h5>
                    <p class="card-text display-6">{{ $overdue }}</p>
                </div>
            </div>
        </div>
    </div>

    
    {{-- Recent Activity --}}
    <div class="card mt-4 shadow">
        <div class="card-header bg-primary text-white">
            Recent Borrow Activity
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Book</th>
                        <th>User</th>
                        <th>Borrowed At</th>
                        <th>Due At</th>
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
                                    <span class="badge bg-danger">Overdue</span>
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
                            <td colspan="5" class="text-center text-muted">No recent borrow activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
