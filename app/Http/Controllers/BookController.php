<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller {
    
public function dashboard() {
    $user = auth()->user();

    if ($user->role === 'admin') {
        // Admin sees all borrow records
        $borrows = \App\Models\Borrow::with('book', 'user')->get();
        return view('admin.dashboard', compact('borrows'));
    } elseif ($user->role === 'faculty') {
        // Faculty sees only their borrow records
        $borrows = \App\Models\Borrow::with('book')
            ->where('user_id', $user->id)
            ->get();
        return view('faculty.dashboard', compact('borrows'));
    } else {
        // Student sees only their borrow records
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

    public function store(Request $request) {
        $request->validate([
            'title'=>'required',
            'author'=>'required',
            'isbn'=>'required|unique:books',
            'copies'=>'required|integer|min:1'
        ]);

        Book::create($request->all());
        return redirect()->route('books.index')->with('success','Book added successfully.');
    }

    public function edit(Book $book) {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book) {
        $request->validate([
            'title'=>'required',
            'author'=>'required',
            'isbn'=>'required|unique:books,isbn,'.$book->id,
            'copies'=>'required|integer|min:1'
        ]);

        $book->update($request->all());
        return redirect()->route('books.index')->with('success','Book updated.');
    }

    public function destroy(Book $book) {
        $book->delete();
        return redirect()->route('books.index')->with('success','Book deleted.');
    }
}
