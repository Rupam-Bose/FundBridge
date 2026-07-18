@extends('layouts.header')

@section('page-title', 'My Ventures')
@section('page-subtitle', 'Manage and track all your ventures.')

@section('content')

{{-- Stats Row --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-layer-group"></i></div>
        <p>Total</p>
        <h2>{{ $stats['total'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-circle-play"></i></div>
        <p>Active</p>
        <h2>{{ $stats['active'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-circle-check"></i></div>
        <p>Completed</p>
        <h2>{{ $stats['completed'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-file-pen"></i></div>
        <p>Drafts</p>
        <h2>{{ $stats['draft'] }}</h2>
    </div>
</div>

{{-- Header --}}
<div class="section-header" style="margin-bottom:20px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:var(--text);">All Ventures</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">
            {{ $ventures->total() }} ventures found
        </p>
    </div>
    <a href="{{ route('founder.ventures.create') }}" class="primary-btn" style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;font-size:14px;border-radius:50px;">
        <i class="fa-solid fa-plus"></i> Add Venture
    </a>
</div>

@if (session('status'))
    <div class="flash-success" style="padding:12px 18px;border-radius:12px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
        <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
    </div>
@endif

{{-- Ventures Grid --}}
@if ($ventures->isEmpty())
    <div class="panel-card">
        <div class="empty-state" style="padding:60px 24px;">
            <i class="fa-solid fa-rocket"></i>
            <h4>No ventures yet</h4>
            <p>Create your first venture and start your funding journey.</p>
            <a href="{{ route('founder.ventures.create') }}" class="primary-btn" style="display:inline-block;margin-top:20px;padding:12px 28px;">
                <i class="fa-solid fa-plus" style="margin-right:8px;"></i>Create Venture
            </a>
        </div>
    </div>
@else
<div class="venture-grid" style="margin-bottom:24px;">
    @foreach ($ventures as $venture)
    <div class="venture-card">
        <div class="venture-card-header">
            <div class="venture-logo" style="background:linear-gradient(135deg,var(--green),var(--green-dark));">
                {{ strtoupper(substr($venture->title,0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <h4 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $venture->title }}</h4>
                <div style="font-size:11px;color:var(--muted);">
                    {{ $venture->sector ?? '—' }} · {{ $venture->stage ?? '—' }}
                </div>
            </div>
            <span class="badge badge-{{ $venture->status }}">{{ ucfirst($venture->status) }}</span>
        </div>

        <p>{{ Str::limit($venture->description, 80) }}</p>

        <div class="venture-meta">
            @if ($venture->sector)<span><i class="fa-solid fa-tag" style="margin-right:4px;"></i>{{ $venture->sector }}</span>@endif
            <span><i class="fa-regular fa-eye" style="margin-right:4px;"></i>{{ number_format($venture->views) }} views</span>
            <span><i class="fa-solid fa-bullhorn" style="margin-right:4px;"></i>{{ $venture->campaigns_count ?? 0 }} campaigns</span>
            <span><i class="fa-solid fa-star" style="margin-right:4px;"></i>{{ $venture->interests_count ?? 0 }} interests</span>
        </div>

        <div class="venture-progress-info">
            <span>Raised: <strong>${{ number_format($venture->raised_amount,0) }}</strong></span>
            <span>Goal: ${{ number_format($venture->goal_amount,0) }}</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ min($venture->progressPercent(),100) }}%;"></div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:8px;margin-top:14px;">
            <a href="{{ route('founder.ventures.edit', $venture->id) }}"
               class="primary-btn"
               style="flex:1;text-align:center;padding:9px 0;font-size:13px;border-radius:10px;">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <form method="POST" action="{{ route('founder.ventures.destroy', $venture->id) }}"
                  onsubmit="return confirm('Delete {{ addslashes($venture->title) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                    style="padding:9px 16px;font-size:13px;border-radius:10px;border:1px solid var(--border);background:transparent;color:#ef4444;cursor:pointer;transition:all .2s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.12)'"
                    onmouseout="this.style.background='transparent'">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div style="display:flex;justify-content:center;margin-top:8px;">
    {{ $ventures->links() }}
</div>
@endif

@endsection
