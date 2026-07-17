@extends('layouts.header')

@section('page-title', 'My Portfolio')
@section('page-subtitle', 'Manage your tracked ventures and investment interests.')

@section('content')

{{-- Stats --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);">
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-briefcase"></i></div>
        <p>Total Tracked</p><h2>{{ $stats['total'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-fire"></i></div>
        <p>High Interest</p><h2>{{ $stats['high'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-star-half-stroke"></i></div>
        <p>Medium</p><h2>{{ $stats['medium'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-star"></i></div>
        <p>Low</p><h2>{{ $stats['low'] }}</h2>
    </div>
</div>

{{-- Charts --}}
@if ($stats['total'] > 0)
<div class="chart-row" style="margin-bottom:24px;">
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-bar"></i> Portfolio by Sector</h3>
        @if (empty($sectorChart['labels']))
            <div class="empty-state" style="padding:40px 0;"><p>Add sector info to ventures.</p></div>
        @else
        <div class="chart-container">
            <canvas id="sectorChart"
                data-labels='@json($sectorChart["labels"])'
                data-values='@json($sectorChart["values"])'
            ></canvas>
        </div>
        @endif
    </div>

    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-pie"></i> Interest Breakdown</h3>
        <div class="chart-container">
            <canvas id="interestChart"
                data-labels='@json($interestChart["labels"])'
                data-values='@json($interestChart["values"])'
            ></canvas>
        </div>
    </div>
</div>
@endif

{{-- Filters --}}
<div class="panel-card" style="margin-bottom:20px;padding:18px 24px;">
    <form method="GET" action="{{ route('investor.portfolio') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Venture name or sector..."
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Interest Level</label>
            <select name="interest_level" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All</option>
                <option value="high"   {{ request('interest_level') === 'high'   ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('interest_level') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low"    {{ request('interest_level') === 'low'    ? 'selected' : '' }}>Low</option>
            </select>
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sort</label>
            <select name="sort" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="newest"       {{ request('sort','newest') === 'newest'       ? 'selected' : '' }}>Newest Added</option>
                <option value="high_interest"{{ request('sort') === 'high_interest' ? 'selected' : '' }}>High Interest First</option>
                <option value="low_interest" {{ request('sort') === 'low_interest'  ? 'selected' : '' }}>Low Interest First</option>
            </select>
        </div>
        <button type="submit" class="primary-btn" style="padding:10px 22px;font-size:13px;border-radius:10px;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        @if (request()->anyFilled(['search','interest_level','sort']))
        <a href="{{ route('investor.portfolio') }}"
           style="padding:10px 16px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:13px;text-decoration:none;">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Portfolio Grid --}}
@if ($interests->isEmpty())
<div class="panel-card">
    <div class="empty-state" style="padding:60px;">
        <i class="fa-solid fa-briefcase"></i>
        <h4>Your portfolio is empty</h4>
        <p>Browse ventures and track the ones you're interested in.</p>
        <a href="{{ route('investor.discover') }}" class="primary-btn" style="display:inline-block;margin-top:20px;padding:12px 28px;">
            <i class="fa-solid fa-compass" style="margin-right:8px;"></i>Discover Ventures
        </a>
    </div>
</div>
@else

<div class="venture-grid" style="margin-bottom:24px;">
    @foreach ($interests as $interest)
    @php $venture = $interest->venture; @endphp
    <div class="venture-card">
        <div class="venture-card-header">
            <div class="venture-logo">{{ strtoupper(substr($venture->title,0,1)) }}</div>
            <div style="min-width:0;">
                <h4 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $venture->title }}</h4>
                <div style="font-size:11px;color:var(--muted);">
                    {{ $venture->founder->name }}
                    @if ($venture->founder->company_name) · {{ $venture->founder->company_name }} @endif
                </div>
            </div>
            <span class="badge badge-{{ $interest->interest_level }}">{{ ucfirst($interest->interest_level) }}</span>
        </div>

        <p>{{ Str::limit($venture->description, 80) }}</p>

        <div class="venture-meta">
            @if ($venture->sector)<span><i class="fa-solid fa-tag" style="margin-right:3px;"></i>{{ $venture->sector }}</span>@endif
            @if ($venture->stage)<span><i class="fa-solid fa-layer-group" style="margin-right:3px;"></i>{{ $venture->stage }}</span>@endif
            <span class="badge badge-{{ $venture->status }}" style="margin:0;">{{ ucfirst($venture->status) }}</span>
        </div>

        <div class="venture-progress-info">
            <span>Raised: <strong>${{ number_format($venture->raised_amount,0) }}</strong></span>
            <span>Goal: ${{ number_format($venture->goal_amount,0) }}</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width:{{ min($venture->progressPercent(),100) }}%;"></div>
        </div>

        {{-- Note if any --}}
        @if ($interest->note)
        <div style="margin-top:10px;padding:8px 12px;border-radius:8px;background:rgba(255,255,255,.04);border:1px solid var(--border);font-size:12px;color:var(--muted);">
            <i class="fa-solid fa-note-sticky" style="margin-right:5px;"></i>{{ $interest->note }}
        </div>
        @endif

        {{-- Update Interest Form --}}
        <form method="POST" action="{{ route('investor.portfolio.update', $venture->id) }}" style="margin-top:12px;">
            @csrf @method('PUT')
            <div style="display:flex;gap:8px;margin-bottom:8px;">
                <select name="interest_level"
                    style="flex:1;padding:8px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:12px;outline:none;">
                    <option value="low"    {{ $interest->interest_level === 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $interest->interest_level === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ $interest->interest_level === 'high'   ? 'selected' : '' }}>High</option>
                </select>
                <button type="submit"
                    style="padding:8px 12px;border-radius:8px;border:none;background:var(--green);color:#000;font-size:12px;font-weight:700;cursor:pointer;">
                    Update
                </button>
            </div>
        </form>

        {{-- Remove from portfolio --}}
        <form method="POST" action="{{ route('investor.portfolio.remove', $venture->id) }}"
              onsubmit="return confirm('Remove {{ addslashes($venture->title) }} from portfolio?')">
            @csrf @method('DELETE')
            <button type="submit"
                style="width:100%;padding:7px;border-radius:8px;border:1px solid var(--border);background:transparent;color:#ef4444;font-size:12px;cursor:pointer;transition:all .2s;"
                onmouseover="this.style.background='rgba(239,68,68,0.1)'"
                onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-trash" style="margin-right:5px;"></i>Remove from Portfolio
            </button>
        </form>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
<div style="display:flex;justify-content:center;margin-top:8px;">
    {{ $interests->appends(request()->query())->links() }}
</div>
@endif

@endsection
