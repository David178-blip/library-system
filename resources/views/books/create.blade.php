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
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="author" class="form-label">Author</label>
        <input type="text" name="author" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="number" name="isbn" class="form-control">
    </div>

     <div class="mb-3">
        <label for="year" class="form-label">Year</label>
        <input type="number" name="year" class="form-control">
    </div>

    <div class="mb-3">
        <label for="copies" class="form-label">Copies</label>
        <input type="number" name="copies" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Save Book</button>
</form>

        </div>
    </div>
</div>
@endsection
