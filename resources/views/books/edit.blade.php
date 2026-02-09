@extends('layouts.app')

@section('content')
<h3>Edit Book</h3>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="book-edit-form" method="POST" action="{{ route('admin.books.update', $book->id) }}">
    @csrf 
    @method('PUT')

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
    </div>

    <div class="mb-3">
        <label>Author</label>
        <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
    </div>

    <div class="mb-3">
        <label>Year</label>
        <input type="number" name="year" class="form-control" value="{{ $book->year }}">
    </div>

    <div class="mb-3">
        <label>Course</label>
        <select name="course" class="form-select" required>
            <option value="">Select Course</option>
            <option value="BSIT" {{ $book->course === 'BSIT' ? 'selected' : '' }}>BSIT</option>
            <option value="BSCRIM" {{ $book->course === 'BSCRIM' ? 'selected' : '' }}>BSCRIM</option>
            <option value="BSBA" {{ $book->course === 'BSBA' ? 'selected' : '' }}>BSBA</option>
            <option value="BSED" {{ $book->course === 'BSED' ? 'selected' : '' }}>BSED</option>
            <option value="BEED" {{ $book->course === 'BEED' ? 'selected' : '' }}>BEED</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Accession Numbers</label>
        <select id="accession_list" class="form-select mb-2" size="5">
            @foreach ($book->copies()->get() as $copy)
                <option value="{{ $copy->accession_number }}" data-status="{{ $copy->status }}">
                    {{ $copy->accession_number }} ({{ ucfirst($copy->status) }})
                </option>
            @endforeach
        </select>

        <div class="mt-3">
            <label for="new_accession_number" class="form-label">Add New Copy (Accession Number)</label>
            <div class="input-group">
                <input type="text" id="new_accession_number" class="form-control" placeholder="Enter new accession number">
                <button type="button" class="btn btn-primary" id="add_new_copy">Add Copy</button>
            </div>
            <small class="form-text text-muted">
                This will add a new physical copy for this book.
            </small>
        </div>

        {{-- Hidden field that will be populated with the remaining accession numbers before submit --}}
        <input type="hidden" name="accession_numbers" id="accession_numbers_input">
    </div>

    <button class="btn btn-success">Update</button>
</form>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('accession_list');
        const hiddenInput = document.getElementById('accession_numbers_input');
        const form = document.getElementById('book-edit-form');
        const addBtn = document.getElementById('add_new_copy');
        const newAccessionInput = document.getElementById('new_accession_number');

        function refreshHidden() {
            const values = Array.from(selectEl.options).map(o => o.value);
            hiddenInput.value = values.join(' ');
        }

        addBtn.addEventListener('click', function () {
            const value = newAccessionInput.value.trim();
            if (!value) {
                alert('Please enter an accession number.');
                return;
            }

            const exists = Array.from(selectEl.options).some(o => o.value === value);
            if (exists) {
                alert('That accession number is already in the list.');
                return;
            }

            const option = document.createElement('option');
            option.value = value;
            option.textContent = value + ' (Available)';
            option.setAttribute('data-status', 'available');
            selectEl.appendChild(option);

            newAccessionInput.value = '';
            refreshHidden();
        });

        form.addEventListener('submit', function () {
            refreshHidden();
        });

        // Initialize hidden input on page load
        refreshHidden();
    });
</script>
@endsection
@endsection
