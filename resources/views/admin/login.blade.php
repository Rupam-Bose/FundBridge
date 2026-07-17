@extends('layouts.app')

@section('title')
    Admin Login | FundBridge
@endsection

@section('content')

@vite(['resources/css/admin.css'])

<div class="admin-login-page">
    <div class="admin-login-card">

        <div class="admin-login-logo">
            <div class="shield-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1>Admin Access</h1>
            <p>Restricted to authorized administrators only</p>
        </div>

        @if (session('status'))
            <div style="padding:12px 16px;border-radius:12px;background:rgba(0,217,156,0.15);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
                <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="padding:12px 16px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <label style="display:block;margin-bottom:7px;color:var(--text);font-size:14px;">
                Admin Email
            </label>
            <input
                type="email"
                name="email"
                placeholder="admin@fundbridge.com"
                value="{{ old('email') }}"
                required
                style="width:100%;padding:14px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;outline:none;margin-bottom:16px;transition:border-color 0.2s;"
            >

            <label style="display:block;margin-bottom:7px;color:var(--text);font-size:14px;">
                Password
            </label>
            <input
                type="password"
                name="password"
                placeholder="Enter admin password"
                required
                style="width:100%;padding:14px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;outline:none;margin-bottom:24px;transition:border-color 0.2s;"
            >

            <button
                type="submit"
                style="width:100%;padding:14px;border-radius:50px;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;font-size:15px;font-weight:700;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 8px 24px rgba(239,68,68,0.3);"
                onmouseover="this.style.transform='translateY(-2px)'"
                onmouseout="this.style.transform='translateY(0)'"
            >
                <i class="fa-solid fa-shield-halved" style="margin-right:8px;"></i>
                Login to Admin Panel
            </button>
        </form>

        <a href="{{ route('home') }}" class="admin-back-link">
            <i class="fa-solid fa-arrow-left" style="margin-right:6px;"></i>
            Back to FundBridge
        </a>

    </div>
</div>

@endsection
