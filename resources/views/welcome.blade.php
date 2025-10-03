@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Welcome Message --}}
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary">📚 Welcome to Our Library</h1>
        <p class="lead text-muted">Explore our collection and manage your library activities online.</p>
    </div>

    {{-- Auth Buttons --}}
    <div class="text-center mb-4">
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-success btn-lg">Register</a>
     
        @endguest
    </div>

    {{-- Book Listing --}}
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0">Available Books</h4>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse($books as $book)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $book->title }}</h5>
                                <p class="card-text text-muted">by {{ $book->author }}</p>
                
                                <p><strong>Available Copies:</strong> {{ $book->copies }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">
                        No books available right now.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
