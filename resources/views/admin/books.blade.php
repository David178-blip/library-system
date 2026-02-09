@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="fw-bold text-primary mb-1"><i class="bi bi-journal-bookmark me-2"></i>Manage Books</h1>
            <p class="text-muted mb-0">Add, edit, and remove books from the catalog.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary rounded-3">
            <i class="bi bi-plus-circle me-2"></i>Add New Book
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Copies</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td class="fw-semibold">{{ Str::limit($book->title, 45) }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->year ?? '—' }}</td>
                                <td>{{ $book->copies ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-outline-warning btn-sm rounded-3 me-1"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>No books yet. Add your first book above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
