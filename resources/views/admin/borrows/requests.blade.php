@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Borrow Requests</h1>


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
                        <!-- Approve button triggers a modal where admin sets the due date -->
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $r->id }}">
                            Approve
                        </button>

                        <!-- Reject stays simple -->
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

    <!-- Approval Modals -->
    @foreach($requests as $r)
        <div class="modal fade" id="approveModal-{{ $r->id }}" tabindex="-1" aria-labelledby="approveModalLabel-{{ $r->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel-{{ $r->id }}">Approve Borrow Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.borrows.approve', $r->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p>
                                <strong>User:</strong> {{ $r->user->name }}<br>
                                <strong>Book:</strong> {{ $r->book->title }}
                            </p>

                            <div class="mb-3">
                                <label class="form-label">Select Copy (Accession Number)</label>
                                @php($availableCopies = $r->book->availableCopies ?? collect())
                                @if($availableCopies->count() > 0)
                                    <select name="accession_number" class="form-select" required>
                                        @foreach($availableCopies as $copy)
                                            <option value="{{ $copy->accession_number }}">
                                                {{ $copy->accession_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Choose which physical copy (by accession number) to lend.</small>
                                @else
                                    <p class="text-danger mb-0">No available copies for this book. Approval is disabled.</p>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Due date</label>
                                <input type="date" name="due_at" class="form-control" required min="{{ now()->toDateString() }}">
                                <small class="text-muted">Select the date when this book should be returned.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" @if($availableCopies->count() === 0) disabled @endif>Confirm Approval</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
