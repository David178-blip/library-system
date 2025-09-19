@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Dashboard</h2>
    <a href="{{ route('books.create') }}" class="btn btn-primary">+ Add Book</a>
</div>

<div class="row">
    @foreach($books as $book)
    <div class="col-md-4">
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $book->title }}</h5>
                <p class="card-text"><strong>Author:</strong> {{ $book->author }}</p>
                <p class="card-text"><strong>Copies:</strong> {{ $book->copies }}</p>
                <a href="{{ route('books.borrow',$book->id) }}" class="btn btn-success btn-sm">Borrow</a>
                <a href="{{ route('books.edit',$book->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('books.destroy',$book->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<hr>
<h4>AI Chat Assistant 🤖</h4>
<div class="card">
    <div class="card-body">
        <textarea id="chat-message" class="form-control mb-2" placeholder="Ask the library assistant..."></textarea>
        <button class="btn btn-primary" onclick="sendMessage()">Send</button>
        <div id="chat-reply" class="mt-3"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function sendMessage(){
    let msg = document.getElementById('chat-message').value;
    fetch("{{ route('ai.chat') }}", {
        method: "POST",
        headers: {
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({message: msg})
    }).then(r=>r.json()).then(data=>{
        document.getElementById('chat-reply').innerHTML =
            "<div class='alert alert-info'><b>AI:</b> " + data.reply + "</div>";
    });
}
</script>
@endsection
