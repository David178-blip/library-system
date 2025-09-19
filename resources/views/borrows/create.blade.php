@extends('layouts.app')

@section('content')
<h3>Borrow Book</h3>
<div class="card">
    <div class="card-body">
        <h5>{{ $book->title }}</h5>
        <p><strong>Author:</strong> {{ $book->author }}</p>
        <p><strong>Available:</strong> {{ $book->copies }}</p>
        <form method="POST" action="{{ route('books.borrow.store',$book->id) }}">
            @csrf
            <button class="btn btn-primary">Confirm Borrow</button>
        </form>
    </div>
</div>
@endsection
