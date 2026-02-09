@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Hero Section --}}
    <div class="text-center mb-5 p-4 p-lg-5 rounded-4 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #c82333 100%); color: white;">
        <h1 class="display-5 fw-bold mb-3"><i class="bi bi-book me-2"></i>Welcome to HCCD Library</h1>
        <p class="lead mb-4 opacity-90">Discover, borrow, and manage books in one place.</p>
        @guest
            <a href="{{ route('login') }}" class="btn btn-light btn-lg me-2 me-md-3 fw-semibold shadow-sm rounded-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg fw-semibold rounded-3">
                <i class="bi bi-person-plus me-2"></i>Register
            </a>
        @else
            <a href="{{ route('books.index') }}" class="btn btn-light btn-lg fw-semibold shadow-sm rounded-3">
                <i class="bi bi-book me-2"></i>Browse Books
            </a>
        @endguest
    </div>

    {{-- Available Books --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
            <h4 class="mb-0"><i class="bi bi-book-half me-2"></i>Available Books</h4>
            <span class="badge bg-danger rounded-pill">{{ $books->count() }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @forelse($books as $book)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm book-card rounded-3">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-primary mb-2">{{ Str::limit($book->title, 40) }}</h5>
                                <p class="card-text text-muted small mb-2">by {{ $book->author }}</p>
                                <p class="mb-3"><span class="badge bg-secondary">Copies: {{ $book->copies ?? 0 }}</span></p>
                                <div class="mt-auto">
                                    @auth
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm w-100 rounded-3">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm w-100 rounded-3">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Log in to borrow
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-journal-x display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No books available yet</h5>
                        <p class="text-secondary mb-0">Check back later or contact the librarian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.book-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.book-card:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important; }
</style>
@endpush
@endsection
