<?php

namespace App\Http\Controllers;

use App\Models\Book;
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

    public function index() {
        $books = Book::all();
        return view('books.index', compact('books'));
    }

    public function create() {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'year'   => 'nullable|integer',
            'isbn'   => 'nullable|string',
            'copies' => 'required|integer|min:1',
        ]);

        Book::create([
            'title'  => $request->title,
            'author' => $request->author,
            'year'   => $request->year,
            'isbn'   => $request->isbn,
            'copies' => $request->copies,
        ]);

        return redirect()->route('admin.books.index')->with('success', 'Book added successfully!');
    }

    public function edit(Book $book) {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'year'   => 'nullable|integer',
            'isbn'   => 'nullable|string',
            'copies' => 'required|integer|min:1',
        ]);

        $book->update([
            'title'  => $request->title,
            'author' => $request->author,
            'year'   => $request->year,
            'isbn'   => $request->isbn,
            'copies' => $request->copies,
        ]);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book) {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted.');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('year', 'LIKE', "%{$query}%")
            ->get();

        return view('books.search-results', compact('books', 'query'));
    }
}
