@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Return Books</h3>

    @if($borrows->isEmpty())
        <p>No borrowed books for this user ✅</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Borrowed At</th>
                    <th>Due At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrows as $borrow)
                    <tr>
                        <td>{{ $borrow->book->title }}</td>
                        <td>{{ $borrow->borrowed_at }}</td>
                        <td>{{ $borrow->due_at }}</td>
                        <td>
                            <form action="{{ route('admin.return.book', $borrow->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm">Mark as Returned</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
