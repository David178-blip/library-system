@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Search Results for: "{{ $query }}"</h2>

    @if($books->count())
        <div class="row">
            @foreach($books as $book)
                <div class="col-md-3 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">{{ $book->title }}</h5>
                            <p class="card-text text-muted">{{ $book->author }}</p>
                            <a href="{{ route('books.show', $book->id) }}" class="btn btn-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No books found.</p>
    @endif
</div>
@endsection
