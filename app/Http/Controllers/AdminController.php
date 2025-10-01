<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF; // from barryvdh/laravel-dompdf

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
        $overdue = Borrow::where('status', 'overdue')->count();
        $recentBorrows = Borrow::latest()->take(10)->get();

        // Get today’s sent emails
        $emailLogs = EmailLog::whereDate('sent_at', now())->latest()->take(10)->get();

        return view('admin.dashboard', compact('books', 'users', 'borrows', 'overdue', 'recentBorrows', 'emailLogs'));
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
    // safety: ensure still available
    $book = $borrow->book;
    if ($book->copies < 1) {
        return back()->with('error','Cannot approve: no copies left.');
    }

    // set approval and status, set borrowed/due dates
    $borrow->update([
        'approval'    => 'approved',
        'status'      => 'borrowed',
        'borrowed_at' => Carbon::today(),
        'due_at'      => Carbon::today()->addDays(7),
    ]);

    // decrement copies
    $book->decrement('copies');


    return back()->with('success','Borrow request approved.');
}

public function rejectBorrow(Borrow $borrow)
{
    $borrow->update([
        'approval' => 'rejected',
        'status'   => 'rejected', // optional
    ]);

    /* Optionally notify user
    if ($borrow->user && $borrow->user->email) {
        Mail::to($borrow->user->email)->send(new \App\Mail\BorrowRejectedMail($borrow));
    }*/

    return back()->with('error','Borrow request rejected.');
}

}
