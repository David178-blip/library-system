<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('redirectToDashboard')) {
    function redirectToDashboard() {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            case 'faculty':
                return redirect()->route('faculty.dashboard');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Unauthorized access.');
        }
    }
}
