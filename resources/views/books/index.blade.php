@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Books</h1>

 @if(isset($searchQuery))
    <div class="alert alert-info">
        Showing results for: <strong>{{ $searchQuery }}</strong>
    </div>
@endif

@if($books->isEmpty())
    <p class="text-center text-muted">No books found.</p>
@else
    <div class="row">
        @foreach($books as $book)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text">Author: {{ $book->author }}</p>
                        <p class="card-text"><small>ISBN: {{ $book->isbn }}</small></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif


    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Copies</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->copies }}</td>
                            <td>
                                {{-- Show "Borrow" button for students/faculty --}}
                                @if(auth()->user()->role !== 'admin')
                                    @if($book->copies > 0)
                                        <a href="{{ route('books.borrow', $book) }}" class="btn btn-primary btn-sm">Borrow</a>
                                    @else
                                        <span class="badge bg-danger">Unavailable</span>
                                    @endif
                                @endif

                                {{-- Show Admin-only buttons --}}
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this book?')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No books available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
