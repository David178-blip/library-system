@extends('layouts.app')

@section('content')
<h3>Add New Book</h3>
<form method="POST" action="{{ route('books.store') }}">
    @csrf
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Author</label>
        <input type="text" name="author" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>ISBN</label>
        <input type="text" name="isbn" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Copies</label>
        <input type="number" name="copies" class="form-control" value="1" required>
    </div>
    <button class="btn btn-success">Save</button>
</form>
@endsection
