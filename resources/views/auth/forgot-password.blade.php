@extends('layouts.app')

@section('title')
    Forgot Password | FundBridge
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

        <h1 style="text-align:center;font-size:30px;margin:30px 0 8px;">Reset Password</h1>
        <p style="text-align:center;margin-bottom:30px;">Enter your email and we'll send you a reset link.</p>

        @if (session('status'))
            <div style="padding:14px 18px;border-radius:14px;background:rgba(0,217,156,0.15);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
                <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="padding:14px 18px;border-radius:14px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="Enter your registered email"
                value="{{ old('email') }}"
                required
            >

            <button class="primary-btn" style="width:100%;margin-top:20px;">
                <i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i>
                Send Reset Link
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:14px;">
            Remembered your password?
            <a href="{{ route('login') }}" style="color:var(--green);font-weight:600;">Log in</a>
        </p>

    </div>
</section>
@endsection
