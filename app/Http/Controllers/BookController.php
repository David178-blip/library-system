<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LostCopy;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function dashboard() {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $borrows = \App\Models\Borrow::with('book', 'user')->get();
            return view('admin.dashboard', compact('borrows'));
        } elseif ($user->role === 'faculty') {
            $borrows = \App\Models\Borrow::with('book')
                ->where('user_id', $user->id)
                ->get();
            return view('faculty.dashboard', compact('borrows'));
        } else {
            $borrows = \App\Models\Borrow::with('book')
                ->where('user_id', $user->id)
                ->get();
            return view('student.dashboard', compact('borrows'));
        }
    }

public function index(Request $request)
{
    // Include count of available physical copies per book
    $query = Book::withCount('availableCopies');

    if ($request->filled('title')) {
        $query->where('title', 'like', '%'.$request->title.'%');
    }

    if ($request->filled('author')) {
        $query->where('author', 'like', '%'.$request->author.'%');
    }

    if ($request->filled('year')) {
        $query->where('year', $request->year);
    }

    if ($request->filled('course')) {
        $query->where('course', $request->course);
    }

    $books = $query->paginate(10);

    $topBooks = Book::withCount('borrows')
        ->orderBy('borrows_count', 'desc')
        ->take(3)
        ->get();

    return view('books.index', compact('books', 'topBooks'))
           ->with('searchQuery', $request->query());
}




    public function create() {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'author'            => 'required|string|max:255',
            'year'              => 'nullable|integer',
            'course'            => 'nullable|string|max:50',
            'accession_numbers' => 'required|string',
        ]);

        // Normalize accession numbers from textarea (newline/comma/space separated)
        $raw = $request->input('accession_numbers', '');
        $accessionNumbers = collect(preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        if ($accessionNumbers->isEmpty()) {
            return back()
                ->withErrors(['accession_numbers' => 'Please provide at least one accession number.'])
                ->withInput();
        }

        // Ensure accession numbers are not already used by other copies (existing or historically lost)
        $existsInCopies = BookCopy::whereIn('accession_number', $accessionNumbers)->exists();
        $existsInLost   = LostCopy::whereIn('accession_number', $accessionNumbers)->exists();

        if ($existsInCopies || $existsInLost) {
            return back()
                ->withErrors(['accession_numbers' => 'One or more accession numbers have already been used and cannot be reused.'])
                ->withInput();
        }

        $copiesCount = $accessionNumbers->count();

        // Create the book first
        $book = Book::create([
            'title'  => $request->title,
            'author' => $request->author,
            'year'   => $request->year,
            'course' => $request->course,
            'copies' => $copiesCount, // keep this as the total physical copies
        ]);

        // Create individual physical copies with their accession numbers
        foreach ($accessionNumbers as $number) {
            $book->copies()->create([
                'accession_number' => $number,
                'status'           => 'available',
            ]);
        }

        return redirect()->route('admin.books.index')->with('success', 'Book added successfully!');
    }

    public function edit(Book $book) {
        $book->load('copies');

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'             => 'required|string|max:255',
            'author'            => 'required|string|max:255',
            'year'              => 'nullable|integer',
            'course'            => 'nullable|string|max:50',
            'accession_numbers' => 'required|string',
        ]);

        $raw = $request->input('accession_numbers', '');
        $accessionNumbers = collect(preg_split('/[\s,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        if ($accessionNumbers->isEmpty()) {
            return back()
                ->withErrors(['accession_numbers' => 'Please provide at least one accession number.'])
                ->withInput();
        }

        // Ensure accession numbers are not already used by other books (current copies)
        $existsInOtherBooks = BookCopy::whereIn('accession_number', $accessionNumbers)
            ->where('book_id', '!=', $book->id)
            ->exists();

        // Ensure accession numbers were never used before (lost history)
        $existsInLost = LostCopy::whereIn('accession_number', $accessionNumbers)->exists();

        if ($existsInOtherBooks || $existsInLost) {
            return back()
                ->withErrors(['accession_numbers' => 'One or more accession numbers have already been used and cannot be reused.'])
                ->withInput();
        }

        $currentAccessionNumbers = $book->copies()->pluck('accession_number')->all();
        $newAccessionNumbers     = $accessionNumbers->all();

        // Only add new accession numbers from the form; do not remove existing copies here.
        $toAdd = array_diff($newAccessionNumbers, $currentAccessionNumbers);

        foreach ($toAdd as $number) {
            $book->copies()->create([
                'accession_number' => $number,
                'status'           => 'available',
            ]);
        }

        // Update basic book info and keep copies count in sync
        $book->update([
            'title'  => $request->title,
            'author' => $request->author,
            'year'   => $request->year,
            'course' => $request->course,
            'copies' => $book->copies()->count(),
        ]);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book) {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted.');
    }

    public function show(Book $book)
    {
        // Include real-time available copies for detail view
        $book->loadCount('availableCopies');
        return view('books.show', compact('book'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
    
            ->orWhere('year', 'LIKE', "%{$query}%")
            ->get();

        return view('books.search-results', compact('books', 'query'));
    }
}
