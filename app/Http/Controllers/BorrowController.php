<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\LostCopy;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BorrowController extends Controller
{
    // =============================
    // Student/Faculty Borrow (self)
    // =============================
    public function create(Book $book)
    {
        // Include real-time count of available physical copies
        $book->loadCount('availableCopies');
        return view('borrows.create', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        // Check if there is at least one available physical copy
        $hasAvailableCopy = $book->copies()
            ->where('status', 'available')
            ->exists();

        if (! $hasAvailableCopy) {
            return back()->with('error', 'No copies available.');
        }

        // Create borrow request (pending) – accession_number set on approval
        Borrow::create([
            'user_id'          => Auth::id(),
            'book_id'          => $book->id,
            'accession_number' => null,
            'borrowed_at'      => null,
            'due_at'           => null,
            'returned_at'      => null,
            'status'           => 'pending',
            'approval'         => 'pending',
        ]);

        return redirect()
            ->route('books.index')
            ->with('success', 'Borrow request submitted. Waiting for admin approval.');
    }

    public function return(Borrow $borrow)
    {
        if ($borrow->status !== 'returned') {
            $borrow->update([
                'returned_at' => Carbon::today(),
                'status'      => 'returned',
            ]);

            // Mark the specific copy as available again
            if ($borrow->accession_number) {
                $copy = BookCopy::where('book_id', $borrow->book_id)
                    ->where('accession_number', $borrow->accession_number)
                    ->first();

                if ($copy) {
                    $copy->update(['status' => 'available']);
                }
            }

            // Do NOT increment $borrow->book->copies – treat it as total copies
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Book returned.');
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

        // Find an available physical copy
        $copy = $book->copies()
            ->where('status', 'available')
            ->first();

        if (! $copy) {
            return back()->with('error', 'No copies available.');
        }

        // Create the borrow record with specific accession number
        $borrow = Borrow::create([
            'user_id'          => $user->id,
            'book_id'          => $book->id,
            'accession_number' => $copy->accession_number,
            'borrowed_at'      => Carbon::today(),
            'due_at'           => Carbon::today()->addDays(7),
            'status'           => 'borrowed',
            'approval'         => 'approved',
        ]);

        // Mark this copy as borrowed
        $copy->update(['status' => 'borrowed']);

        // In-app notification for the user
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Book Borrowed',
            'message' => 'A book has been borrowed for you: "' . $book->title . '" (Accession No. ' . $copy->accession_number . '). Due date: ' . $borrow->due_at->format('M d, Y') . '.',
        ]);

        // Email notification at the same time
        if ($user->email) {
            $title = $book->title;
            $due   = $borrow->due_at->format('M d, Y');
            $acc   = $copy->accession_number;

            Mail::raw(
                "A book has been borrowed for you: \"{$title}\" (Accession No. {$acc}). Due date: {$due}.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Book Borrowed');
                }
            );
        }

        // Do NOT decrement $book->copies – it's total copies

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Book assigned to ' . $user->name);
    }

    public function showReturnForm($userId)
    {
        $user = User::findOrFail($userId);

        $borrows = Borrow::with('book')
            ->where('user_id', $userId)
            ->whereNull('returned_at')
            ->orderBy('due_at')
            ->get();

        return view('admin.return-books', compact('borrows', 'user'));
    }

    public function markReturned($borrowId)
    {
        $borrow = Borrow::findOrFail($borrowId);

        if ($borrow->status !== 'returned') {
            $borrow->returned_at = now();
            $borrow->status      = 'returned';
            $borrow->save();

            // Mark the specific copy as available again
            if ($borrow->accession_number) {
                $copy = BookCopy::where('book_id', $borrow->book_id)
                    ->where('accession_number', $borrow->accession_number)
                    ->first();

                if ($copy) {
                    $copy->update(['status' => 'available']);
                }
            }
        }

        return back()->with('success', 'Book marked as returned.');
    }

    public function markMissing($borrowId)
    {
        $borrow = Borrow::with(['book', 'user'])->findOrFail($borrowId);

        // Only handle currently unreturned borrows
        if (is_null($borrow->returned_at)) {
            $copy = null;
            if ($borrow->accession_number) {
                $copy = BookCopy::where('book_id', $borrow->book_id)
                    ->where('accession_number', $borrow->accession_number)
                    ->first();
            }

            // Log lost copy linked to this user
            LostCopy::create([
                'book_id'          => $borrow->book_id,
                'user_id'          => $borrow->user_id,
                'accession_number' => $borrow->accession_number,
                'removed_at'       => now(),
            ]);

            // Remove the physical copy from the catalog so it can no longer be borrowed
            if ($copy) {
                $copy->delete();
            }

            // Mark borrow as closed/missing
            $borrow->returned_at = now();
            $borrow->status      = 'lost';
            $borrow->save();
        }

        return back()->with('success', 'Book marked as missing and recorded in reports.');
    }
}
