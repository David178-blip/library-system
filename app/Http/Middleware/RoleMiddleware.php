<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!Auth::check()) {
            return redirect('/login');
        }

if (!in_array(Auth::user()->role, $roles)) {
    switch (Auth::user()->role) {
        case 'admin':
            return redirect()->route('admin.dashboard')->with('error', 'Access denied.');
        case 'student':
            return redirect()->route('student.dashboard')->with('error', 'Access denied.');
        case 'faculty':
            return redirect()->route('faculty.dashboard')->with('error', 'Access denied.');
        default:
            Auth::logout();
            return redirect('/login');
    }
}

        return $next($request);
    }
}
