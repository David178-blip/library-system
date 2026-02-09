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
                    <label for="year" class="form-label">Year</label>
                    <input type="number" name="year" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="accession_numbers" class="form-label">Accession Numbers</label>
                    <textarea name="accession_numbers" class="form-control" rows="3" required>{{ old('accession_numbers') }}</textarea>
                    <small class="form-text text-muted">
                        Enter one accession number per copy. You can separate them with spaces, commas, or new lines.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="course" class="form-label">Course</label>
                    <select name="course" class="form-select" required>
                        <option value="">Select Course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCRIM">BSCRIM</option>
                        <option value="BSBA">BSBA</option>
                        <option value="BSED">BSED</option>
                        <option value="BEED">BEED</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Save Book</button>
            </form>
        </div>
    </div>
</div>
@endsection
