<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /** Show the forgot password form */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /** Send password reset link */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email',
        ], [
            'email.exists' => 'No account found with that email address.',
        ]);

        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Build reset URL
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Send via configured mail driver (log driver writes to storage/logs/laravel.log)
        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl, $request->email));

        $driver = config('mail.default');
        $msg = $driver === 'log'
            ? 'Reset link logged to storage/logs/laravel.log (MAIL_MAILER=log). Copy the URL from the log to reset your password.'
            : 'Password reset link sent! Please check your inbox.';

        return back()->with('status', $msg);
    }

    /** Show the reset password form */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /** Process the password reset */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Invalid or expired reset token. Please request a new link.']);
        }

        // Check token not older than 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'This reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with that email.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully! Please log in.');
    }
}
