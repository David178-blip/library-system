<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $books   = Book::count();
        $users   = User::count();
        $borrows = Borrow::where('status', 'borrowed')->count();

        // 👇 Add overdue count
        $overdue = Borrow::where('status', 'borrowed')
            ->whereDate('due_at', '<', Carbon::now())
            ->count();

        // 👇 Add list of recent borrow records (with relations for table)
        $recentBorrows = Borrow::with(['book', 'user'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'books',
            'users',
            'borrows',
            'overdue',
            'recentBorrows'
        ));
    }
}
