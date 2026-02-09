@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Return Books</h3>

    @isset($user)
        <div class="mb-3">
            <p><strong>User:</strong> {{ $user->name }} ({{ $user->email }})</p>
        </div>
    @endisset

    @if($borrows->isEmpty())
        <p>No borrowed books for this user ✅</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Borrowed At</th>
                    <th>Due At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrows as $borrow)
                    @php
                        $isOverdue = ($borrow->status === 'overdue') || ($borrow->due_at && $borrow->due_at->isPast());
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                        <td>{{ $borrow->book->title }}</td>
                        <td>{{ optional($borrow->borrowed_at)->format('M d, Y') }}</td>
                        <td>{{ optional($borrow->due_at)->format('M d, Y') }}</td>
                        <td>
                            @if($isOverdue)
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-success">On Time</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.return.book', $borrow->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Mark as Returned</button>
                                </form>
                                <form action="{{ route('admin.return.book.missing', $borrow->id) }}" method="POST" onsubmit="return confirm('Mark this book as missing? This will remove the copy from the catalog and record it as lost.');">
                                    @csrf
                                    <button class="btn btn-danger btn-sm">Mark as Missing</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
