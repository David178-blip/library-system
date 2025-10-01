<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Override the redirect after login.
     */
protected function authenticated($request, $user)
{
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'student') {
        return redirect()->route('student.dashboard');
    }

    if ($user->role === 'faculty') {
        return redirect()->route('faculty.dashboard');
    }

    return redirect()->route('home'); // fallback
}


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
