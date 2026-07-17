@extends('layouts.app')

@section('title')
    Reset Password | FundBridge
@endsection

@section('content')
<section class="auth-page login-page">
    <div class="auth-left">
        <div class="brand">
            <span><i class="fa-solid fa-bridge"></i></span>
            FundBridge
        </div>
        <p class="brand-text">Bridging Ideas, Capital &amp; Opportunities</p>
        <div class="stats">
            <div>Raised <strong>$125M+</strong></div>
            <div>Startups <strong>1500+</strong></div>
            <div>Investors <strong>800+</strong></div>
        </div>
    </div>

    <div class="auth-right">

        <div class="auth-tabs">
            <a href="{{ route('register') }}">Register</a>
            <a href="{{ route('login') }}">Login</a>
        </div>

        <h1 style="text-align:center;font-size:30px;margin:30px 0 8px;">Set New Password</h1>
        <p style="text-align:center;margin-bottom:30px;">Choose a strong password for your account.</p>

        @if ($errors->any())
            <div style="padding:14px 18px;border-radius:14px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <label>Email Address</label>
            <input
                type="email"
                name="email_display"
                value="{{ $email }}"
                readonly
                style="opacity:0.6;cursor:not-allowed;"
            >

            <label>New Password</label>
            <input
                type="password"
                name="password"
                placeholder="At least 8 characters"
                required
            >

            <label>Confirm New Password</label>
            <input
                type="password"
                name="password_confirmation"
                placeholder="Repeat your new password"
                required
            >

            <button class="primary-btn" style="width:100%;margin-top:20px;">
                <i class="fa-solid fa-key" style="margin-right:8px;"></i>
                Reset Password
            </button>
        </form>

    </div>
</section>
@endsection
