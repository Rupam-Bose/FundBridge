@extends('layouts.header')

@section('page-title', 'Discover Ventures')
@section('page-subtitle', 'Find promising startups to add to your portfolio.')

@section('content')

<!-- Search & Filter Bar -->
<div class="panel-card" style="margin-bottom:24px;padding:20px 24px;">
    <form method="GET" action="{{ route('investor.discover') }}" id="discoverForm"
          style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

        <div style="flex:1;min-width:220px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Search Ventures</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Name, description, or sector..."
                style="width:100%;padding:11px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;"
                id="searchInput">
        </div>

        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sector</label>
            <select name="sector" onchange="this.form.submit()"
                style="width:100%;padding:11px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Sectors</option>
                @foreach ($sectors as $s)
                    <option value="{{ $s }}" {{ request('sector') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Stage</label>
            <select name="stage" onchange="this.form.submit()"
                style="width:100%;padding:11px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Stages</option>
                @foreach ($stages as $st)
                    <option value="{{ $st }}" {{ request('stage') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>

        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sort By</label>
            <select name="sort" onchange="this.form.submit()"
                style="width:100%;padding:11px 16px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected' : '' }}>Newest First</option>
                <option value="most_funded"{{ request('sort') === 'most_funded' ? 'selected' : '' }}>Most Funded</option>
                <option value="most_viewed"{{ request('sort') === 'most_viewed' ? 'selected' : '' }}>Most Viewed</option>
                <option value="goal_asc"   {{ request('sort') === 'goal_asc'    ? 'selected' : '' }}>Goal: Low → High</option>
            </select>
        </div>

        <button type="submit" class="primary-btn" style="padding:11px 22px;font-size:13px;border-radius:10px;white-space:nowrap;">
            <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>

        @if (request()->anyFilled(['search','sector','stage','sort']))
        <a href="{{ route('investor.discover') }}"
           style="padding:11px 16px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:13px;text-decoration:none;">
            Clear
        </a>
        @endif

    </form>
</div>

<!-- Results Header -->
<div class="section-header" style="margin-bottom:20px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:var(--text);">
            {{ $ventures->total() }} Active Ventures
        </h2>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">
            Showing {{ $ventures->firstItem() }}–{{ $ventures->lastItem() }} of {{ $ventures->total() }}
        </p>
    </div>
</div>

@if (session('status'))
<div style="padding:12px 18px;border-radius:12px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
    <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
</div>
@endif

@if ($ventures->isEmpty())
<div class="panel-card">
    <div class="empty-state" style="padding:60px;">
        <i class="fa-solid fa-compass"></i>
        <h4>No ventures found</h4>
        <p>Try adjusting your search or filters.</p>
        <a href="{{ route('investor.discover') }}" class="primary-btn" style="display:inline-block;margin-top:16px;padding:11px 24px;">Clear Filters</a>
    </div>
</div>
@else
<div class="venture-grid" style="margin-bottom:28px;">
    @foreach ($ventures as $venture)
    <div class="venture-card" style="{{ $trackedIds->contains($venture->id) ? 'border-color:rgba(0,217,156,.4);' : '' }}">

        @if ($trackedIds->contains($venture->id))
        <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
            <span class="badge badge-active" style="font-size:10px;">
                <i class="fa-solid fa-check"></i> In Portfolio
            </span>
        </div>
        @endif

        <div class="venture-card-header">
            <div class="venture-logo">{{ strtoupper(substr($venture->title,0,1)) }}</div>
            <div style="min-width:0;">
                <h4 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $venture->title }}</h4>
                <div style="font-size:11px;color:var(--muted);">
                    {{ $venture->founder->name }}
                    @if ($venture->founder->company_name) · {{ $venture->founder->company_name }} @endif
                </div>
            </div>
        </div>

        <p>{{ Str::limit($venture->description, 90) }}</p>

        <div class="venture-meta">
            @if ($venture->sector)<span><i class="fa-solid fa-tag" style="margin-right:3px;"></i>{{ $venture->sector }}</span>@endif
            @if ($venture->stage)<span><i class="fa-solid fa-layer-group" style="margin-right:3px;"></i>{{ $venture->stage }}</span>@endif
            <span><i class="fa-regular fa-eye" style="margin-right:3px;"></i>{{ number_format($venture->views) }}</span>
        </div>

        <div class="venture-progress-info">
            <span>Raised: <strong>${{ number_format($venture->raised_amount,0) }}</strong></span>
            <span>Goal: ${{ number_format($venture->goal_amount,0) }}</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ min($venture->progressPercent(),100) }}%;"></div>
        </div>

         <!-- Track Button -->
        <form method="POST" action="{{ route('investor.interest', $venture->id) }}" style="margin-top:14px;display:flex;gap:8px;">
            @csrf
            <select name="interest_level"
                style="flex:1;padding:9px 12px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="low">Low Interest</option>
                <option value="medium" selected>Medium Interest</option>
                <option value="high">High Interest</option>
            </select>
            <button type="submit" class="primary-btn" style="padding:9px 16px;font-size:13px;border-radius:10px;white-space:nowrap;">
                {{ $trackedIds->contains($venture->id) ? 'Update' : 'Track' }}
            </button>
        </form>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div style="display:flex;justify-content:center;margin-top:8px;">
    {{ $ventures->appends(request()->query())->links() }}
</div>
@endif

@push('scripts')
<script>
// Live search on typing (debounced)
let timer;
document.getElementById('searchInput')?.addEventListener('input', function() {
    clearTimeout(timer);
    timer = setTimeout(() => document.getElementById('discoverForm').submit(), 600);
});
</script>
@endpush

@endsection
