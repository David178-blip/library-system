@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📊 Library Reports</h1>

    <a href="{{ route('admin.reports.download') }}" class="btn btn-success mb-3">
        ⬇️ Download PDF Report
    </a>

    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">All Borrow Records</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Book</th>
                        <th>Borrowed At</th>
                        <th>Due At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($borrows as $borrow)
                        <tr>
                            <td>{{ $borrow->user->name }}</td>
                            <td>{{ $borrow->book->title }}</td>
                            <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                            <td>{{ $borrow->due_at->format('M d, Y') }}</td>
                            <td>
                                @if($borrow->status === 'borrowed')
                                    <span class="badge bg-info">Borrowed</span>
                                @elseif($borrow->status === 'returned')
                                    <span class="badge bg-success">Returned</span>
                                @else
                                    <span class="badge bg-danger">Overdue</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-danger text-white">⏳ Overdue Books</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Book</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overdue as $borrow)
                        <tr>
                            <td>{{ $borrow->user->name }}</td>
                            <td>{{ $borrow->book->title }}</td>
                            <td>{{ $borrow->due_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No overdue books 🎉</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
