@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Borrow Requests</h1>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Requested At</th>
                <th>Approval</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $r)
                <tr>
                    <td>{{ $r->user->name }}</td>
                    <td>{{ $r->book->title }}</td>
                    <td>{{ $r->created_at->format('M d, Y H:i') }}</td>
                    <td><span class="badge bg-warning">{{ ucfirst($r->approval) }}</span></td>
                    <td>
                        <form action="{{ route('admin.borrows.approve', $r->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>

                        <form action="{{ route('admin.borrows.reject', $r->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No borrow requests.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
