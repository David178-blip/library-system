<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    // Show attendance entry form
    public function index()
    {
        return view('attendance.index');
    }

    // Store attendance record
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:student,faculty',
        ]);

        Attendance::create([
            'name' => $request->name,
            'role' => $request->role,
            'time_in' => now(),
        ]);

        return redirect()->back()->with('success', 'Attendance recorded successfully!');
    }

    // View attendance report (admin)
    public function report()
    {
        $records = Attendance::orderBy('time_in', 'desc')->get();

        return view('attendance.report', compact('records'));
    }
}
