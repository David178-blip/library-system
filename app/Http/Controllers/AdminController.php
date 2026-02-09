<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrow;
use App\Models\BookCopy;
use App\Models\LostCopy;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use PDF;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $books   = Book::count();
        $users   = User::count();
        $borrows = Borrow::count();
        $overdue = Borrow::where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->count();

        $recentBorrows = Borrow::with('book', 'user')
            ->latest()
            ->take(5)
            ->get();

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
    public function reports(Request $request)
    {
        $query = Borrow::with(['user', 'book']);

        // Filter: User Name
        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }

        // Filter: Book Title
        if ($request->filled('book')) {
            $query->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }

        // Filter: Accession Number
        if ($request->filled('accession_number')) {
            $query->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }

        // Filter: Course (based on user's course field)
        if ($request->filled('course')) {
            $query->whereHas('user', fn($q) =>
                $q->where('course', $request->course)
            );
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Borrowed From Date
        if ($request->filled('from')) {
            $query->whereDate('borrowed_at', '>=', $request->from);
        }

        // Filter: Borrowed To Date
        if ($request->filled('to')) {
            $query->whereDate('borrowed_at', '<=', $request->to);
        }

        $borrows = $query->latest()->paginate(10)->withQueryString();

        // Overdue books filtered using same criteria
        $overdueQuery = Borrow::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->where('due_at', '<', now());

        if ($request->filled('user')) {
            $overdueQuery->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }

        if ($request->filled('book')) {
            $overdueQuery->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }

        if ($request->filled('accession_number')) {
            $overdueQuery->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }

        if ($request->filled('course')) {
            $overdueQuery->whereHas('user', fn($q) =>
                $q->where('course', $request->course)
            );
        }

        if ($request->filled('from')) {
            $overdueQuery->whereDate('borrowed_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $overdueQuery->whereDate('borrowed_at', '<=', $request->to);
        }

        $overdue = $overdueQuery->get();

        // Lost / missing copies (removed from catalog)
        $lostQuery = LostCopy::with(['book', 'user']);

        if ($request->filled('user')) {
            $lostQuery->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }

        if ($request->filled('book')) {
            $lostQuery->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }

        if ($request->filled('accession_number')) {
            $lostQuery->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }

        if ($request->filled('course')) {
            $lostQuery->whereHas('book', fn($q) =>
                $q->where('course', $request->course)
            );
        }

        if ($request->filled('from')) {
            $lostQuery->whereDate('removed_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $lostQuery->whereDate('removed_at', '<=', $request->to);
        }

        $lost = $lostQuery->latest('removed_at')->get();

        // ============================================
        // Chart Data for Analytics
        // ============================================
        
        // Get base query for charts (respects filters)
        $chartQuery = Borrow::with(['user', 'book']);
        
        // Apply same filters to chart data
        if ($request->filled('user')) {
            $chartQuery->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->user . '%'));
        }
        if ($request->filled('book')) {
            $chartQuery->whereHas('book', fn($q) => $q->where('title', 'like', '%' . $request->book . '%'));
        }
        if ($request->filled('accession_number')) {
            $chartQuery->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }
        if ($request->filled('course')) {
            $chartQuery->whereHas('user', fn($q) => $q->where('course', $request->course));
        }
        if ($request->filled('status')) {
            $chartQuery->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $chartQuery->whereDate('borrowed_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $chartQuery->whereDate('borrowed_at', '<=', $request->to);
        }

        // 1. Borrows Over Time (Last 30 days)
        $days = 30;
        $borrowsOverTime = [];
        $borrowsOverTimeLabels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = (clone $chartQuery)->whereDate('borrowed_at', $date->format('Y-m-d'))->count();
            $borrowsOverTimeLabels[] = $date->format('M d');
            $borrowsOverTime[] = $count;
        }

        // 2. Status Distribution
        $statusData = [
            'borrowed' => (clone $chartQuery)->where('status', 'borrowed')->count(),
            'returned' => (clone $chartQuery)->where('status', 'returned')->count(),
            'overdue' => (clone $chartQuery)->where('status', 'overdue')->count(),
            'rejected' => (clone $chartQuery)->where('status', 'rejected')->count(),
        ];

        // 3. Course Distribution (for students)
        $courseData = [];
        $courseLabels = [];
        $courses = ['BSIT', 'BSBA', 'BSCRIM', 'BEED', 'BSED'];
        foreach ($courses as $course) {
            $count = (clone $chartQuery)->whereHas('user', fn($q) => $q->where('course', $course))->count();
            if ($count > 0) {
                $courseLabels[] = $course;
                $courseData[] = $count;
            }
        }

        // 4. Top Borrowed Books (Top 10)
        $topBooks = (clone $chartQuery)
            ->selectRaw('book_id, count(*) as borrow_count')
            ->groupBy('book_id')
            ->orderByDesc('borrow_count')
            ->limit(10)
            ->with('book')
            ->get()
            ->map(function ($item) {
                return [
                    'title' => $item->book->title ?? 'Unknown',
                    'count' => (int) $item->borrow_count
                ];
            })
            ->values(); // Ensure it's a proper collection

        // 5. Monthly Trends (Last 12 months)
        $monthlyData = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = (clone $chartQuery)
                ->whereYear('borrowed_at', $date->year)
                ->whereMonth('borrowed_at', $date->month)
                ->count();
            $monthlyLabels[] = $date->format('M Y');
            $monthlyData[] = $count;
        }

        // 6. Summary Statistics
        $totalBorrows = (clone $chartQuery)->count();
        $totalReturned = (clone $chartQuery)->where('status', 'returned')->count();
        $totalOverdue = (clone $chartQuery)->where('status', 'overdue')->orWhere(function($q) {
            $q->where('status', 'borrowed')->where('due_at', '<', now());
        })->count();
        $totalLost = $lost->count();
        $returnRate = $totalBorrows > 0 ? round(($totalReturned / $totalBorrows) * 100, 1) : 0;

        return view('admin.reports', compact(
            'borrows', 
            'overdue', 
            'lost',
            'borrowsOverTime',
            'borrowsOverTimeLabels',
            'statusData',
            'courseLabels',
            'courseData',
            'topBooks',
            'monthlyLabels',
            'monthlyData',
            'totalBorrows',
            'totalReturned',
            'totalOverdue',
            'totalLost',
            'returnRate'
        ));
    }

    /**
     * Download PDF report.
     */
    public function downloadReport(Request $request)
    {
        $query = Borrow::with(['user', 'book']);

        // Apply same filters as the reports page
        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }
        if ($request->filled('book')) {
            $query->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }
        if ($request->filled('accession_number')) {
            $query->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }
        if ($request->filled('course')) {
            $query->whereHas('user', fn($q) =>
                $q->where('course', $request->course)
            );
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('borrowed_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('borrowed_at', '<=', $request->to);
        }

        $borrows = $query->latest()->get();

        // Overdue table filtered with same criteria
        $overdueQuery = Borrow::with(['user', 'book'])
            ->where('status', 'borrowed')
            ->where('due_at', '<', now());

        if ($request->filled('user')) {
            $overdueQuery->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }
        if ($request->filled('book')) {
            $overdueQuery->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }
        if ($request->filled('accession_number')) {
            $overdueQuery->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }
        if ($request->filled('course')) {
            $overdueQuery->whereHas('user', fn($q) =>
                $q->where('course', $request->course)
            );
        }
        if ($request->filled('from')) {
            $overdueQuery->whereDate('borrowed_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $overdueQuery->whereDate('borrowed_at', '<=', $request->to);
        }

        $overdue = $overdueQuery->get();

        // Lost / missing copies (removed from catalog)
        $lostQuery = LostCopy::with(['book', 'user']);

        if ($request->filled('user')) {
            $lostQuery->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->user . '%')
            );
        }

        if ($request->filled('book')) {
            $lostQuery->whereHas('book', fn($q) =>
                $q->where('title', 'like', '%' . $request->book . '%')
            );
        }

        if ($request->filled('accession_number')) {
            $lostQuery->where('accession_number', 'like', '%' . $request->accession_number . '%');
        }

        if ($request->filled('course')) {
            $lostQuery->whereHas('book', fn($q) =>
                $q->where('course', $request->course)
            );
        }

        if ($request->filled('from')) {
            $lostQuery->whereDate('removed_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $lostQuery->whereDate('removed_at', '<=', $request->to);
        }

        $lost = $lostQuery->latest('removed_at')->get();

        $pdf = PDF::loadView('admin.report-pdf', compact('borrows', 'overdue', 'lost'));

        return $pdf->download('library_report_' . now()->format('Y-m-d') . '.pdf');
    }

    public function borrowRequests()
    {
        $requests = Borrow::with(['user', 'book.availableCopies'])
            ->where('approval', 'pending')
            ->latest()
            ->get();

        return view('admin.borrows.requests', compact('requests'));
    }

    public function approveBorrow(Request $request, Borrow $borrow)
    {
        $request->validate([
            'due_at'           => ['required', 'date', 'after_or_equal:today'],
            'accession_number' => ['required', 'string'],
        ]);

        $book = $borrow->book;

        // Find the specific physical copy selected by the librarian
        $copy = BookCopy::where('book_id', $book->id)
            ->where('accession_number', $request->accession_number)
            ->where('status', 'available')
            ->first();

        if (! $copy) {
            return back()->with('error', 'Cannot approve: the selected copy is not available.');
        }

        $borrow->update([
            'approval'         => 'approved',
            'status'           => 'borrowed',
            'borrowed_at'      => now(),
            'due_at'           => $request->due_at,
            'accession_number' => $copy->accession_number,
        ]);

        // Mark this copy as borrowed
        $copy->update(['status' => 'borrowed']);

        Notification::create([
            'user_id' => $borrow->user_id,
            'title'   => 'Borrow Approved',
            'message' => 'Your request for "' . $book->title . '" has been approved. Due date: ' . Carbon::parse($borrow->due_at)->format('M d, Y') . '.',
        ]);

        // Send email notification at the same time
        if ($borrow->user && $borrow->user->email) {
            $user  = $borrow->user;
            $title = $book->title;
            $due   = Carbon::parse($borrow->due_at)->format('M d, Y');

            Mail::raw(
                "Your borrow request for \"{$title}\" has been approved. Due date: {$due}.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Borrow Approved');
                }
            );
        }

        return back()->with('success', 'Borrow request approved.');
    }

    public function rejectBorrow(Borrow $borrow)
    {
        $borrow->update([
            'approval' => 'rejected',
            'status'   => 'rejected',
        ]);

        Notification::create([
            'user_id' => $borrow->user_id,
            'title'   => 'Borrow Rejected',
            'message' => 'Your request for "' . $borrow->book->title . '" has been rejected.',
        ]);

        // Send email notification at the same time
        if ($borrow->user && $borrow->user->email) {
            $user  = $borrow->user;
            $title = $borrow->book->title;

            Mail::raw(
                "Your borrow request for \"{$title}\" has been rejected.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Borrow Rejected');
                }
            );
        }

        return back()->with('error', 'Borrow request rejected.');
    }
}