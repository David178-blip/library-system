<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    // =============================
    // Student/Faculty Borrow (self)
    // =============================
    public function create(Book $book)
    {
        return view('borrows.create', compact('book'));
    }
public function store(Request $request, Book $book)
{
    if ($book->copies < 1) {
        return back()->with('error','No copies available.');
    }

    // create borrow request (pending)
    $borrow = Borrow::create([
        'user_id'     => Auth::id(),
        'book_id'     => $book->id,
        'borrowed_at' => null,                // only set when approved
        'due_at'      => null,                // only set when approved
        'returned_at' => null,
        'status'      => 'pending',           // or keep previous status logic
        'approval'    => 'pending',
    ]);

    return redirect()->route('books.index')->with('success','Borrow request submitted. Waiting for admin approval.');
}



    public function return(Borrow $borrow)
    {
        if ($borrow->status !== 'returned') {
            $borrow->update([
                'returned_at' => Carbon::today(),
                'status'      => 'returned'
            ]);

            $borrow->book->increment('copies');
        }

        return redirect()->route('books.index')->with('success', 'Book returned.');
    }

    // =============================
    // Admin Assign Borrow (via QR)
    // =============================
    public function assign($userId)
    {
        $user  = User::findOrFail($userId);
        $books = Book::all();

        return view('admin.assign-borrow', compact('user', 'books'));
    }

    public function storeForUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $book = Book::findOrFail($request->book_id);

        if ($book->copies < 1) {
            return back()->with('error', 'No copies available.');
        }

        Borrow::create([
            'user_id'     => $user->id,
            'book_id'     => $book->id,
            'borrowed_at' => Carbon::today(),
            'due_at'      => Carbon::today()->addDays(7),
            'status'      => 'borrowed'
        ]);

        $book->decrement('copies');

        return redirect()->route('admin.dashboard')->with('success', 'Book assigned to ' . $user->name);
    }
    public function showReturnForm($userId)
{
    $borrows = \App\Models\Borrow::with('book')
        ->where('user_id', $userId)
        ->whereNull('returned_at')
        ->get();

    return view('admin.return-books', compact('borrows', 'userId'));
}

public function markReturned($borrowId)
{
    $borrow = \App\Models\Borrow::findOrFail($borrowId);
    
    $borrow->returned_at = now();
    $borrow->status = 'returned';
    $borrow->save();

    // ✅ Optionally, increase book copies again
    $borrow->book->increment('copies');

    return back()->with('success', 'Book marked as returned.');
}

}
