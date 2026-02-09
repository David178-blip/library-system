@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="fw-bold text-primary"><i class="bi bi-book me-2"></i>Books</h1>
        <p class="text-muted mb-0">Search and filter the library catalog.</p>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-dark text-white py-3">
            <i class="bi bi-funnel me-2"></i>Filter Books
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('books.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Title</label>
                        <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Search by title...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Author</label>
                        <input type="text" name="author" value="{{ request('author') }}" class="form-control" placeholder="Search by author...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Year</label>
                        <input type="number" name="year" value="{{ request('year') }}" class="form-control" placeholder="e.g. 2025">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Course</label>
                        <select name="course" class="form-select">
                            <option value="">All Courses</option>
                            <option value="BSIT" {{ request('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                            <option value="BSCRIM" {{ request('course') == 'BSCRIM' ? 'selected' : '' }}>BSCRIM</option>
                            <option value="BSBA" {{ request('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                            <option value="BSED" {{ request('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                            <option value="BEED" {{ request('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Apply</button>
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($topBooks) && $topBooks->count())
        <div class="mb-4">
            <h3 class="fw-bold text-danger mb-3"><i class="bi bi-star me-2"></i>Recommended</h3>
            <div class="row g-3">
                @foreach($topBooks as $top)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm border-start border-4 border-danger rounded-3 h-100">
                            <div class="card-body">
                                <h6 class="card-title fw-bold">{{ Str::limit($top->title, 35) }}</h6>
                                <p class="card-text small text-muted mb-2">{{ $top->author }}</p>
                                <span class="badge bg-danger">Borrowed {{ $top->borrows_count }} times</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Books Table --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Available</th>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td><a href="{{ route('books.show', $book) }}" class="text-decoration-none fw-semibold">{{ Str::limit($book->title, 40) }}</a></td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->year ?? '—' }}</td>
                                <td>{{ $book->available_copies_count ?? $book->copies ?? 0 }}</td>
                                <td><span class="badge bg-secondary">{{ $book->course ?? '—' }}</span></td>
                                <td>
                                    @if(auth()->user()->role !== 'admin')
                                        @if(($book->available_copies_count ?? $book->copies ?? 0) > 0)
                                            <a href="{{ route('books.borrow', $book) }}" class="btn btn-primary btn-sm rounded-3"><i class="bi bi-journal-plus me-1"></i>Borrow</a>
                                        @else
                                            <span class="badge bg-danger">Unavailable</span>
                                        @endif
                                    @else
                                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-outline-warning btn-sm rounded-3"><i class="bi bi-pencil me-1"></i>Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>No books match your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($books->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $books->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
