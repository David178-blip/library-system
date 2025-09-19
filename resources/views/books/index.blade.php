@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Books</h1>

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
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
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
