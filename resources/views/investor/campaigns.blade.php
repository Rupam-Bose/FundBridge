@extends('layouts.header')

@section('page-title', 'Campaigns')
@section('page-subtitle', 'All campaigns from your tracked ventures — invest directly.')

@section('content')

 <!-- My Investment Stats -->
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);">
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <p>Total Invested</p>
        <h2>${{ number_format($myStats['total_invested'],0) }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-briefcase"></i></div>
        <p>Tracked Ventures</p>
        <h2>{{ $myStats['total_ventures'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-bullhorn"></i></div>
        <p>Campaigns Available</p>
        <h2>{{ $myStats['total_campaigns'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-receipt"></i></div>
        <p>My Investments</p>
        <h2>{{ $myStats['investments_count'] }}</h2>
    </div>
</div>

<!-- Filters -->
<div class="panel-card" style="margin-bottom:20px;padding:18px 24px;">
    <form method="GET" action="{{ route('investor.campaigns') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Search Campaigns</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Campaign name..."
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <div style="min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Status</label>
            <select name="status" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All</option>
                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                <option value="paused"    {{ request('status') === 'paused'    ? 'selected' : '' }}>Paused</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sort</label>
            <select name="sort" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="newest"      {{ request('sort','newest') === 'newest'      ? 'selected' : '' }}>Newest</option>
                <option value="most_raised" {{ request('sort') === 'most_raised' ? 'selected' : '' }}>Most Raised</option>
                <option value="goal_high"   {{ request('sort') === 'goal_high'   ? 'selected' : '' }}>Highest Goal</option>
                <option value="deadline"    {{ request('sort') === 'deadline'    ? 'selected' : '' }}>Deadline Soon</option>
            </select>
        </div>
        <button type="submit" class="primary-btn" style="padding:10px 22px;font-size:13px;border-radius:10px;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>
</div>

@if (session('status'))
<div style="padding:12px 18px;border-radius:12px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
    <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
</div>
@endif

@if ($campaigns->isEmpty())
<div class="panel-card">
    <div class="empty-state" style="padding:60px;">
        <i class="fa-solid fa-bullhorn"></i>
        <h4>No campaigns yet</h4>
        <p>Track more ventures to see their campaigns here.</p>
        <a href="{{ route('investor.discover') }}" class="primary-btn" style="display:inline-block;margin-top:20px;padding:12px 28px;">
            <i class="fa-solid fa-compass" style="margin-right:8px;"></i>Discover Ventures
        </a>
    </div>
</div>
@else

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:20px;margin-bottom:24px;">

    @foreach ($campaigns as $campaign)
    @php
        $myInvested = $campaign->investments->sum('amount');
    @endphp
    <div class="panel-card" style="padding:24px;">

         <!-- Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:10px;">
            <div style="min-width:0;">
                <h4 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $campaign->title }}</h4>
                <div style="font-size:12px;color:var(--muted);margin-bottom:2px;">
                    <i class="fa-solid fa-rocket" style="margin-right:4px;"></i>
                    <strong>{{ $campaign->venture->title }}</strong>
                </div>
                <div style="font-size:11px;color:var(--muted);">
                    by {{ $campaign->venture->founder->name }}
                    @if ($campaign->venture->founder->company_name) · {{ $campaign->venture->founder->company_name }} @endif
                </div>
            </div>
            <span class="badge badge-{{ $campaign->status }}" style="flex-shrink:0;">{{ ucfirst($campaign->status) }}</span>
        </div>

        @if ($campaign->description)
        <p style="font-size:13px;color:var(--muted);line-height:1.5;margin-bottom:14px;">{{ Str::limit($campaign->description, 100) }}</p>
        @endif

         <!-- Progress -->
        <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:6px;">
            <span>Raised: <strong style="color:var(--green);">${{ number_format($campaign->raised,0) }}</strong></span>
            <span style="color:var(--muted);">Goal: ${{ number_format($campaign->goal,0) }}</span>
        </div>
        <div class="progress-bar" style="height:8px;margin-bottom:6px;">
            <div class="progress-fill" style="width:{{ min($campaign->progressPercent(),100) }}%;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-bottom:14px;">
            <span>{{ $campaign->progressPercent() }}% funded</span>
            @if ($campaign->deadline)
            <span><i class="fa-regular fa-clock" style="margin-right:3px;"></i>{{ $campaign->deadline->diffForHumans() }}</span>
            @endif
        </div>

         <!-- My Contribution -->
        @if ($myInvested > 0)
        <div style="padding:10px 14px;border-radius:10px;background:rgba(0,217,156,.08);border:1px solid rgba(0,217,156,.2);margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;color:var(--muted);font-weight:600;">My Contribution</span>
            <span style="font-size:14px;font-weight:800;color:var(--green);">${{ number_format($myInvested,2) }}</span>
        </div>
        @endif

         <!-- Invest Form  -->
        @if ($campaign->status === 'active')
        <form method="POST" action="{{ route('investor.campaigns.invest', $campaign->id) }}">
            @csrf
            <div style="margin-bottom:10px;">
                <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;">
                    Add Funds ($)
                </label>
                <input type="number" name="amount" min="1" step="any"
                    placeholder="Enter amount to invest..."
                    style="width:100%;padding:11px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--green)'"
                    onblur="this.style.borderColor='var(--border)'"
                    required>
            </div>
            <div style="margin-bottom:12px;">
                <input type="text" name="note" maxlength="200"
                    placeholder="Optional note (e.g. 'Seed round commitment')..."
                    style="width:100%;padding:10px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='var(--green)'"
                    onblur="this.style.borderColor='var(--border)'">
            </div>
            <button type="submit" class="primary-btn"
                style="width:100%;padding:12px;font-size:14px;border-radius:12px;display:flex;align-items:center;justify-content:center;gap:8px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                Invest Now
            </button>
        </form>
        @else
        <div style="padding:12px;border-radius:12px;border:1px solid var(--border);text-align:center;color:var(--muted);font-size:13px;">
            <i class="fa-solid fa-lock" style="margin-right:6px;"></i>
            Campaign is {{ $campaign->status }} — not accepting funds
        </div>
        @endif

         <!-- Contact Founder -->
        <a href="{{ route('messages.show', $campaign->venture->founder->id) }}"
           style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px;padding:9px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:12px;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
            <i class="fa-solid fa-comment"></i> Message Founder
        </a>
    </div>
    @endforeach
</div>

<div style="display:flex;justify-content:center;">{{ $campaigns->appends(request()->query())->links() }}</div>

@endif

@endsection
