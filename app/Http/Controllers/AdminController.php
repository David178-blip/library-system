<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF; 
use App\Models\Notification;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */


public function dashboard()
{
    $books = Book::count();
    $users = User::count();
    $borrows = Borrow::count();
    $overdue = Borrow::where('status', 'borrowed')->where('due_at', '<', now())->count();
    $recentBorrows = Borrow::with('book', 'user')->latest()->take(5)->get();


    $notifications = Notification::with('user')
        ->latest()
        ->take(10)
        ->get();

    return view('admin.dashboard', compact(
        'books',
        'users',
        'borrows',
        'overdue',
        'recentBorrows',
        'notifications'
    ));
}


    /**
     * Show reports page.
     */
    public function reports()
    {
        $borrows = Borrow::with('user', 'book')->latest()->get();
        $overdue = Borrow::with('user', 'book')
            ->where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->get();

        return view('admin.reports', compact('borrows', 'overdue'));
    }

    /**
     * Download PDF report.
     */
    public function downloadReport()
    {
        $borrows = Borrow::with('user', 'book')->latest()->get();
        $overdue = Borrow::with('user', 'book')
            ->where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->get();

        $pdf = PDF::loadView('admin.report-pdf', compact('borrows', 'overdue'));

        return $pdf->download('library_report_' . now()->format('Y-m-d') . '.pdf');
    }


public function borrowRequests()
{
    $requests = Borrow::with('user','book')
                ->where('approval', 'pending')
                ->latest()
                ->get();

    return view('admin.borrows.requests', compact('requests'));
}

public function approveBorrow(Borrow $borrow)
{
    $book = $borrow->book;
    if ($book->copies < 1) {
        return back()->with('error','Cannot approve: no copies left.');
    }

    $borrow->update([
        'approval'    => 'approved',
        'status'      => 'borrowed',
        'borrowed_at' => now(),
        'due_at'      => now()->addDays(7),
    ]);

    $book->decrement('copies');

    // ✅ Send in-app notification
    Notification::create([
        'user_id' => $borrow->user_id,
        'title'   => 'Borrow Approved',
        'message' => 'Your request for "' . $book->title . '" has been approved.',
    ]);

    return back()->with('success','Borrow request approved.');
}


public function rejectBorrow(Borrow $borrow)
{
    $borrow->update([
        'approval' => 'rejected',
        'status'   => 'rejected',
    ]);

    // ✅ Notify user
    Notification::create([
        'user_id' => $borrow->user_id,
        'title'   => 'Borrow Rejected',
        'message' => 'Your request for "' . $borrow->book->title . '" has been rejected.',
    ]);

    return back()->with('error','Borrow request rejected.');
}

}
