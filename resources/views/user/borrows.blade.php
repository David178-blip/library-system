@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">My Borrowed Books</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Book</th>
                <th>Borrowed At</th>
                <th>Due At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrows as $borrow)
            <tr>
                <td>{{ $borrow->book->title }}</td>
                <td>{{ $borrow->borrowed_at }}</td>
                <td>{{ $borrow->due_at }}</td>
                <td>
                    @if($borrow->status == 'borrowed')
                        <span class="badge bg-warning">Borrowed</span>
                    @else
                        <span class="badge bg-success">Returned</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
