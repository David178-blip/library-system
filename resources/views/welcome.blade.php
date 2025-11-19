@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- ===== Hero Section ===== --}}
    <div class="text-center mb-5 p-5 rounded-4 shadow-sm" 
         style="background: linear-gradient(135deg, #0d6efd, #dc3545); color: white;">
        <h1 class="display-4 fw-bold mb-3">📚 Welcome to the HCCD Library</h1>
        <p class="lead mb-4">Discover, borrow, and manage your favorite books all in one place.</p>

        @guest
            <a href="{{ route('login') }}" class="btn btn-light btn-lg me-3 fw-semibold shadow-sm">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg fw-semibold shadow-sm">
                <i class="bi bi-person-plus"></i> Register
            </a>
        @endguest
    </div>

    {{-- ===== Book Listing ===== --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
            <h4 class="mb-0"><i class="bi bi-book-half me-2"></i> Available Books</h4>
            <span class="badge bg-danger rounded-pill">{{ $books->count() }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row">
                @forelse($books as $book)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm book-card">
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-primary">{{ $book->title }}</h5>
                                <p class="card-text text-muted mb-2">by {{ $book->author }}</p>
                                <p><strong>Available Copies:</strong> {{ $book->copies }}</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-end">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-emoji-frown display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No books available right now.</h5>
                        <p class="text-secondary">Please check back later or contact the librarian.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===== Custom Styles ===== --}}
@push('styles')
<style>
    .book-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush
@endsection
