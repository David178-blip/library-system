@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-danger">Faculty Dashboard</h1>

    <div class="row">
        <!-- Borrowed Books -->
        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Books Borrowed</h5>
                    <p class="card-text fs-4">{{ $borrowedCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Books Due Soon -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Books Due Soon</h5>
                    <p class="card-text fs-4">{{ $dueSoonCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Total Books -->
        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Books in Library</h5>
                    <p class="card-text fs-4">{{ $booksCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Borrow History -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-primary text-white">My Borrowed Books</div>
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead class="table-danger">
                    <tr>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Borrowed At</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrows as $borrow)
                        <tr>
                            <td>{{ $borrow->book->title }}</td>
                            <td><span class="badge bg-primary">{{ ucfirst($borrow->status) }}</span></td>
<td>{{ $borrow->borrowed_at ? $borrow->borrowed_at->format('M d, Y') : 'N/A' }}</td>
        <td>{{ $borrow->returned_at ? $borrow->returned_at->format('M d, Y') : 'Not returned' }}</td>                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
