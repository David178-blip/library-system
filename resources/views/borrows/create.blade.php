@extends('layouts.app')

@section('content')
<h3>Borrow Book</h3>
<div class="card">
    <div class="card-body">
        <h5>{{ $book->title }}</h5>
        <p><strong>Author:</strong> {{ $book->author }}</p>
        <p><strong>Available:</strong> {{ $book->available_copies_count ?? $book->copies }}</p>

        @php
            $borrowPeriod = 7; // Borrow period in days
            $estimatedDueDate = \Carbon\Carbon::today()->addDays($borrowPeriod)->format('F j, Y');
        @endphp

        <p><strong>Estimated Due Date:</strong> {{ $estimatedDueDate }}</p>

        <form method="POST" action="{{ route('books.borrow.store', $book->id) }}">
            @csrf
            <button class="btn btn-primary" @if(($book->available_copies_count ?? $book->copies) < 1) disabled @endif>
                Confirm Borrow
            </button>
        </form>
    </div>
</div>
@endsection
