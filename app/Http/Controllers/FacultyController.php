<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Borrow;
use App\Models\Book;
use Carbon\Carbon;

class FacultyController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Borrowed count (active borrows)
        $borrowedCount = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        // Books due in next 3 days
        $dueSoonCount = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->whereBetween('due_at', [Carbon::now(), Carbon::now()->addDays(3)])
            ->count();

        // Total books in library
        $booksCount = Book::count();

        // All borrows for faculty
        $borrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('faculty.dashboard', compact(
            'borrowedCount',
            'dueSoonCount',
            'booksCount',
            'borrows'
        ));
    }
}