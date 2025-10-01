@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">➕ Add New Book</h1>

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.books.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Book Title</label>
                    <input type="text" name="title" id="title" 
                           class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" name="author" id="author" 
                           class="form-control" value="{{ old('author') }}" required>
                </div>

                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" name="isbn" id="isbn" 
                           class="form-control" value="{{ old('isbn') }}">
                </div>

                <div class="mb-3">
                    <label for="publisher" class="form-label">Publisher</label>
                    <input type="text" name="publisher" id="publisher" 
                           class="form-control" value="{{ old('publisher') }}">
                </div>

                <div class="mb-3">
                    <label for="year" class="form-label">Publication Year</label>
                    <input type="number" name="year" id="year" 
                           class="form-control" value="{{ old('year') }}">
                </div>

                <div class="mb-3">
                    <label for="copies" class="form-label">Number of Copies</label>
                    <input type="number" name="copies" id="copies" 
                           class="form-control" value="{{ old('copies', 1) }}" min="1" required>
                </div>

                <button type="submit" class="btn btn-success">📚 Save Book</button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">⬅ Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
