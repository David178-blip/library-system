@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm">
            <h1 class="text-center mb-4">{{ $user->name }}'s Profile</h1>

            <div class="text-center mb-4">
                <strong>Email:</strong> {{ $user->email }} <br>
                <strong>Role:</strong> {{ ucfirst($user->role) }}
            </div>

            {{-- QR Code --}}
            <div class="d-flex justify-content-center mb-3">
                <div class="qr-container">
                    {!!QrCode::size(200)->generate(route('profile.show', $user->id))!!}
                </div>
            </div>
            <p class="text-muted text-center mb-4">Scan to access this profile</p>

            {{-- Borrowed Books --}}
{{-- Borrowed Books --}}
@if(isset($borrows) && $borrows->count())
    <h4 class="mb-3">Borrowed Books</h4>
    <ul class="list-group mb-4">
 @foreach($borrows as $borrow)
    <tr>
        <td>{{ $borrow->book->title }}</td>
        <td>{{ $borrow->user->name }}</td>
        <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
        <td>
            @php
                $today = \Carbon\Carbon::today();
                $due = \Carbon\Carbon::parse($borrow->due_at);
            @endphp

            @if($borrow->status === 'returned')
                <span class="badge bg-success">Returned</span>
            @elseif($due->isPast())
                <span class="badge bg-danger">Overdue ({{ $due->diffForHumans() }})</span>
            @elseif($due->diffInDays($today) <= 2)
                <span class="badge bg-warning text-dark">Due Soon ({{ $due->diffForHumans() }})</span>
            @else
                <span class="badge bg-secondary">Due {{ $due->diffForHumans() }}</span>
            @endif
        </td>
        <td>
            @if($borrow->status !== 'returned' && auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('borrows.return', $borrow->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">
                        🔄 Return
                    </button>
                </form>
            @endif
        </td>
    </tr>
@endforeach

    </ul>
@else
    <p class="text-center text-muted">No borrowed books.</p>
@endif
            {{-- Admin-only: Borrow New Book --}}
            @if(auth()->user()->role === 'admin')
                <div class="text-center mb-4">
                    <!-- Button trigger modal -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowBookModal">
                        📚 Borrow New Book for {{ $user->name }}
                    </button>
                </div>
            @endif

            {{-- Back to Dashboard --}}
            @php
                $role = auth()->user()->role ?? null;
                $backRoute = match($role) {
                    'admin' => route('admin.dashboard'),
                    'faculty' => route('faculty.dashboard'),
                    'student' => route('student.dashboard'),
                    default => url('/'),
                };
            @endphp

            <div class="text-center">
                <a href="{{ $backRoute }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

{{-- Borrow Book Modal (Admin Only) --}}
@if(auth()->user()->role === 'admin')
<div class="modal fade" id="borrowBookModal" tabindex="-1" aria-labelledby="borrowBookLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="borrowBookLabel">Borrow Book for {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('borrows.assign', $user->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="book_id" class="form-label">Select Book</label>
                        <select name="book_id" id="book_id" class="form-control" required>
                            @foreach(\App\Models\Book::where('copies', '>', 0)->get() as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->copies }} copies left)</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Confirm Borrow</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- QR responsive styling --}}
<style>
    .qr-container svg {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection
