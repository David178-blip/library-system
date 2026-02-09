<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailCodeVerificationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.verify-code', compact('user'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        if (! $user->email_verification_code || ! $user->email_verification_expires_at) {
            return back()->withErrors(['code' => 'No verification code was generated. Please request a new one.']);
        }

        if ($user->email_verification_expires_at->isPast()) {
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        if ($request->input('code') !== $user->email_verification_code) {
            return back()->withErrors(['code' => 'The verification code is incorrect.']);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ])->save();

        return redirect()->intended('/dashboard')->with('status', 'email-verified');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'email_verification_code' => $code,
            'email_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new EmailVerificationCodeMail($user, $code));

        return back()->with('status', 'verification-code-sent');
    }
}
