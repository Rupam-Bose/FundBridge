{{--
    Reusable profile page partial — pass $role as 'founder' or 'investor'
    Include from: founder/profile.blade.php and investor/profile.blade.php
--}}
@extends('layouts.header')

@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account information.')

@section('content')

<div style="display:grid;grid-template-columns:300px 1fr;gap:24px;max-width:1100px;margin:0 auto;align-items:start;">

    {{-- Left: Avatar card --}}
    <div class="panel-card" style="text-align:center;">
        <div style="width:100px;height:100px;border-radius:50%;margin:0 auto 16px;border:3px solid var(--green);overflow:hidden;background:var(--bg-soft);">
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                style="width:100%;height:100%;object-fit:cover;">
        </div>
        <h3 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:4px;">{{ $user->name }}</h3>
        <p style="color:var(--muted);font-size:13px;margin-bottom:8px;">{{ $user->email }}</p>
        <span class="badge badge-{{ $user->role }}" style="font-size:12px;">{{ ucfirst($user->role) }}</span>

        @if ($user->company_name)
        <div style="margin-top:16px;padding:12px;border-radius:12px;background:rgba(0,217,156,.08);border:1px solid rgba(0,217,156,.2);">
            <div style="font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Company</div>
            <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $user->company_name }}</div>
        </div>
        @endif

        @if ($user->bio)
        <div style="margin-top:16px;text-align:left;">
            <div style="font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;">About</div>
            <p style="font-size:13px;color:var(--muted);line-height:1.6;">{{ $user->bio }}</p>
        </div>
        @endif

        <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px;text-align:left;">
            <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">
                <i class="fa-regular fa-calendar" style="width:16px;"></i>
                Member since {{ $user->created_at->format('M Y') }}
            </div>
        </div>
    </div>

    {{-- Right: Edit Forms --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Profile Info Form --}}
        <div class="panel-card">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:20px;">
                <i class="fa-solid fa-user-pen" style="color:var(--green);margin-right:8px;"></i>
                Edit Profile
            </h3>

            @if (session('status'))
            <div style="padding:12px 16px;border-radius:10px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,.3);color:var(--green);margin-bottom:16px;font-size:14px;">
                <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
            </div>
            @endif

            @if ($errors->has('name') || $errors->has('company_name') || $errors->has('bio') || $errors->has('avatar'))
            <div style="padding:12px 16px;border-radius:10px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;margin-bottom:16px;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>{{ $errors->first() }}
            </div>
            @endif

            @php
                $profileRoute  = $user->role === 'founder' ? route('founder.profile.update') : route('investor.profile.update');
            @endphp

            <form method="POST" action="{{ $profileRoute }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Company / Organization</label>
                        <input type="text" name="company_name" class="form-input" value="{{ old('company_name', $user->company_name) }}" placeholder="Your company name">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Bio <span style="color:var(--muted);font-size:11px;">(max 500 characters)</span></label>
                    <textarea name="bio" class="form-input" rows="3" maxlength="500" placeholder="A short bio about yourself...">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <div style="margin-bottom:20px;">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="avatar" class="form-input" accept="image/*" style="padding:10px 14px;cursor:pointer;">
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">JPG, PNG up to 2MB. Leave empty to keep current.</div>
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="primary-btn" style="padding:11px 28px;font-size:14px;border-radius:50px;">
                        <i class="fa-solid fa-check" style="margin-right:8px;"></i>Save Profile
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password Form --}}
        <div class="panel-card">
            <h3 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:20px;">
                <i class="fa-solid fa-lock" style="color:#fb923c;margin-right:8px;"></i>
                Change Password
            </h3>

            @if ($errors->has('current_password') || $errors->has('password'))
            <div style="padding:12px 16px;border-radius:10px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;margin-bottom:16px;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>{{ $errors->first() }}
            </div>
            @endif

            @php
                $passRoute = $user->role === 'founder' ? route('founder.profile.password') : route('investor.profile.password');
            @endphp

            <form method="POST" action="{{ $passRoute }}">
                @csrf @method('PUT')

                <div style="margin-bottom:16px;">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input" placeholder="Enter current password" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-input" placeholder="At least 8 characters" required>
                    </div>
                    <div>
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat new password" required>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit"
                        style="padding:11px 28px;font-size:14px;border-radius:50px;border:none;background:linear-gradient(135deg,#fb923c,#f97316);color:#fff;font-weight:700;cursor:pointer;transition:all .2s;">
                        <i class="fa-solid fa-key" style="margin-right:8px;"></i>Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('styles')
<style>
.form-label { display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:7px; }
.form-input { width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box; }
.form-input:focus { border-color:var(--green); }
textarea.form-input { resize:vertical; }
</style>
@endpush

@endsection
