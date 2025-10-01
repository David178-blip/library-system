@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Assign Book to {{ $user->name }}</h2>
    <form method="POST" action="{{ route('admin.borrow.store', $user->id) }}">
        @csrf
        <div class="mb-3">
            <label for="book_id" class="form-label">Select Book</label>
            <select name="book_id" class="form-control" required>
                @foreach($books as $book)
                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Confirm Borrow</button>
    </form>
</div>
@endsection
