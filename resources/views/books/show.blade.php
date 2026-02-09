@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $book->title }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Author: {{ $book->author }}</h5>
            <p><strong>Publisher:</strong> {{ $book->publisher }}</p>
            <p><strong>Year:</strong> {{ $book->year }}</p>
            <p><strong>Copies Available:</strong> {{ $book->available_copies_count ?? $book->copies }}</p>
        </div>
    </div>

    @auth
        <a href="{{ route('books.borrow', $book) }}" class="btn btn-primary">
            📖 Borrow this book
        </a>
    @endauth

    <a href="{{ route('books.index') }}" class="btn btn-secondary">⬅ Back to all books</a>
</div>
@endsection
