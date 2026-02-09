<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use PDF; 
use App\Models\User;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check for active attendance
        $activeAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('time_out')
            ->first();

        return view('attendance.index', compact('user', 'activeAttendance'));
    }

    public function timeIn()
    {
        $user = Auth::user();

        $active = Attendance::where('user_id', $user->id)
            ->whereNull('time_out')
            ->first();

        if ($active) {
            return back()->with('error', 'You are already timed in.');
        }

        Attendance::create([
            'user_id' => $user->id,
            'time_in' => now(),
        ]);

        return back()->with('success', 'Time In recorded successfully.');
    }

    public function timeOut()
    {
        $user = Auth::user();

        $active = Attendance::where('user_id', $user->id)
            ->whereNull('time_out')
            ->first();

        if (!$active) {
            return back()->with('error', 'No active attendance session found.');
        }

        $active->update([
            'time_out' => now(),
        ]);

        return back()->with('success', 'Time Out recorded.');
    }

    // Attendance Report with Filters
    public function report(Request $request)
    {
        $query = Attendance::with('user');

        // Filter by user name
        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->user . '%'));
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

            // Filter by course
    if ($request->course) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('course', $request->course);
        });
    }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('time_in', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('time_in', '<=', $request->to);
        }

        // Paginate results and keep current filters when moving between pages
        $records = $query->orderBy('time_in', 'desc')->paginate(10)->withQueryString();

        $courses = User::whereNotNull('course')->pluck('course')->unique();

        return view('attendance.report', compact('records', 'courses'));
    }

    // Download Attendance PDF

public function downloadReport(Request $request)
{
    $query = Attendance::with('user');

    // 🔎 Apply filters if any
    if ($request->filled('user')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->user . '%');
        });
    }

    if ($request->filled('role')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('role', $request->role);
        });
    }

    if ($request->filled('from')) {
        $query->whereDate('time_in', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('time_in', '<=', $request->to);
    }

    $records = $query->orderBy('time_in', 'desc')->get();

    $pdf = PDF::loadView('attendance.report-pdf', compact('records'));

    return $pdf->download('attendance_report_' . now()->format('Y-m-d') . '.pdf');
}

}
