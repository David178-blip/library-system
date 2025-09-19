@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Book</h1>

    <form action="{{ route('books.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Copies</label>
            <input type="number" name="copies" class="form-control" required min="1">
        </div>

        <button type="submit" class="btn btn-success">Save Book</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
