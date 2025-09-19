<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Borrow;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Borrowed count (active borrows)
        $borrowedCount = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->count();

        // Overdue books
        $overdueCount = Borrow::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('due_at', '<', Carbon::now())
            ->count();

        // All borrows for history
        $borrows = Borrow::with('book')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.dashboard', compact(
            'borrowedCount',
            'overdueCount',
            'borrows'
        ));
    }
}
