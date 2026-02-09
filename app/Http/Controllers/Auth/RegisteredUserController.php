<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\EmailVerificationCodeMail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            'unique:'.User::class,
        ],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'role' => ['required', 'in:student,faculty'], // role validation
        'course' => ['required_if:role,student', 'nullable', 'in:BSIT,BSBA,BSCRIM,BEED,BSED'], // course validation
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'course' => $request->role === 'student' ? $request->course : null, // save course only for students
    ]);

    // Generate a 6-digit code that expires in 10 minutes
    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    $user->forceFill([
        'email_verification_code' => $code,
        'email_verification_expires_at' => now()->addMinutes(10),
    ])->save();

    // Send the code to the user's email
    Mail::to($user->email)->send(new EmailVerificationCodeMail($user, $code));

    Auth::login($user);

    // Always send new users to the code verification page
    return redirect()->route('verification.code.show');
}

}
