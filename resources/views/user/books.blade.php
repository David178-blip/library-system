@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Available Books</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Year</th>
                <th>ISBN</th>
                <th>Copies</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->year }}</td>
                <td>{{ $book->isbn }}</td>
                <td>{{ $book->copies }}</td>
                <td>
                    @if($book->copies > 0)
                        <form action="{{ route('borrows.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                            <button type="submit" class="btn btn-sm btn-primary">Borrow</button>
                        </form>
                    @else
                        <span class="badge bg-danger">Not Available</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
