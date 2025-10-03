@extends('layouts.app')

@section('content')
<h3>Edit Book</h3>
<form method="POST" action="{{ route('admin.books.update', $book->id) }}">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
    </div>

    <div class="mb-3">
        <label>Author</label>
        <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
    </div>

    <div class="mb-3">
        <label>ISBN</label>
        <input type="number" name="isbn" class="form-control" value="{{ $book->isbn }}">
    </div>

     <div class="mb-3">
        <label>Year</label>
        <input type="number" name="year" class="form-control" value="{{ $book->year }}">
    </div>

    <div class="mb-3">
        <label>Copies</label>
        <input type="number" name="copies" class="form-control" value="{{ $book->copies }}" required>
    </div>

    <button class="btn btn-success">Update</button>
</form>
@endsection
