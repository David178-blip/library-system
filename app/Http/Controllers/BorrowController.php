<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller {
    public function create(Book $book) {
        return view('borrows.create', compact('book'));
    }

    public function store(Request $request, Book $book) {
        if ($book->copies < 1) {
            return back()->with('error','No copies available.');
        }

        $borrow = Borrow::create([
            'user_id'=>Auth::id(),
            'book_id'=>$book->id,
            'borrowed_at'=>Carbon::today(),
            'due_at'=>Carbon::today()->addDays(7),
            'status'=>'borrowed'
        ]);

        $book->decrement('copies');
        return redirect()->route('books.index')->with('success','Book borrowed.');
    }

    public function return(Borrow $borrow) {
        if ($borrow->status !== 'returned') {
            $borrow->update([
                'returned_at'=>Carbon::today(),
                'status'=>'returned'
            ]);

            $borrow->book->increment('copies');
        }

        return redirect()->route('books.index')->with('success','Book returned.');
    }
}
