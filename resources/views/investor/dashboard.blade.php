@extends('layouts.header')

@section('page-title')
    Investor Dashboard
@endsection

@section('page-subtitle')
    Discover opportunities and track your portfolio.
@endsection

@section('content')

{{-- ── Stats Cards ────────────────────────────────────────── --}}
<div class="dashboard-cards">

    <div class="dashboard-card">
        <div class="card-icon purple">
            <i class="fa-solid fa-briefcase"></i>
        </div>
        <p>Tracked Ventures</p>
        <h2>{{ $totalTracked }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-chart-line"></i> In portfolio
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon orange">
            <i class="fa-solid fa-fire"></i>
        </div>
        <p>High Interest</p>
        <h2>{{ $highInterestCount }}</h2>
        <span class="card-trend {{ $highInterestCount > 0 ? 'up' : 'down' }}">
            <i class="fa-solid fa-star"></i> Priority deals
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon blue">
            <i class="fa-solid fa-compass"></i>
        </div>
        <p>Available Ventures</p>
        <h2>{{ $totalActiveVentures }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-rocket"></i> Active startups
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon green">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <p>Unread Messages</p>
        <h2>{{ $unreadMessages }}</h2>
        @if ($unreadMessages > 0)
            <span class="card-trend down"><i class="fa-solid fa-bell"></i> Needs attention</span>
        @else
            <span class="card-trend up"><i class="fa-solid fa-circle-check"></i> All read</span>
        @endif
    </div>

</div>


{{-- ── Chart Row ──────────────────────────────────────────── --}}
<div class="chart-row">

    {{-- Interest Distribution Chart --}}
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-pie"></i> Portfolio Interest Breakdown</h3>
        @if ($totalTracked === 0)
            <div class="empty-state">
                <i class="fa-solid fa-pie-chart"></i>
                <h4>No portfolio data yet</h4>
                <p>Mark interest in ventures below to start building your portfolio.</p>
            </div>
        @else
        <div class="chart-container">
            <canvas id="interestChart"
                data-labels='@json($interestChart["labels"])'
                data-values='@json($interestChart["values"])'
            ></canvas>
        </div>
        @endif
    </div>

    {{-- My Portfolio Snapshot --}}
    <div class="chart-card">
        <h3><i class="fa-solid fa-briefcase"></i> My Portfolio</h3>

        @forelse ($myInterests->take(5) as $interest)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
            <div class="venture-logo" style="width:36px;height:36px;font-size:13px;border-radius:10px;">
                {{ strtoupper(substr($interest->venture->title, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $interest->venture->title }}
                </div>
                <div style="font-size:11px;color:var(--muted);">
                    {{ $interest->venture->sector ?? 'General' }}
                </div>
            </div>
            <span class="badge badge-{{ $interest->interest_level }}">
                {{ ucfirst($interest->interest_level) }}
            </span>
        </div>
        @empty
        <div class="empty-state" style="padding:30px 0;">
            <i class="fa-solid fa-briefcase" style="font-size:28px;margin-bottom:8px;display:block;"></i>
            <p>Start tracking ventures below.</p>
        </div>
        @endforelse
    </div>

</div>


{{-- ── Discover Ventures ───────────────────────────────────── --}}
<div class="panel-card" style="margin-bottom:28px;">
    <h3>
        <span><i class="fa-solid fa-compass" style="color:var(--green);margin-right:8px;"></i>Discover Ventures</span>
        <a href="#">Browse all →</a>
    </h3>

    @if ($discoverVentures->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-rocket"></i>
            <h4>You've seen all ventures!</h4>
            <p>You've already tracked all active ventures. Check back later for new ones.</p>
        </div>
    @else
    <div class="venture-grid">
        @foreach ($discoverVentures as $venture)
        <div class="venture-card">
            <div class="venture-card-header">
                <div class="venture-logo">
                    {{ strtoupper(substr($venture->title, 0, 1)) }}
                </div>
                <div>
                    <h4>{{ $venture->title }}</h4>
                    <div style="font-size:11px;color:var(--muted);">
                        by {{ $venture->founder->name }}
                        @if ($venture->founder->company_name)
                            · {{ $venture->founder->company_name }}
                        @endif
                    </div>
                </div>
            </div>

            <p>{{ Str::limit($venture->description, 90) }}</p>

            <div class="venture-meta">
                @if ($venture->sector)
                    <span><i class="fa-solid fa-tag" style="margin-right:4px;"></i>{{ $venture->sector }}</span>
                @endif
                @if ($venture->stage)
                    <span><i class="fa-solid fa-layer-group" style="margin-right:4px;"></i>{{ $venture->stage }}</span>
                @endif
                <span><i class="fa-regular fa-eye" style="margin-right:4px;"></i>{{ number_format($venture->views) }} views</span>
            </div>

            <div class="venture-progress-info">
                <span>Raised: <strong>${{ number_format($venture->raised_amount, 0) }}</strong></span>
                <span>Goal: ${{ number_format($venture->goal_amount, 0) }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:{{ $venture->progressPercent() }}%;"></div>
            </div>

            {{-- Mark Interest Form --}}
            <form method="POST" action="{{ route('investor.interest', $venture->id) }}" style="margin-top:14px;display:flex;gap:8px;">
                @csrf
                <select name="interest_level" style="flex:1;padding:8px 12px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                    <option value="low">Low Interest</option>
                    <option value="medium" selected>Medium Interest</option>
                    <option value="high">High Interest</option>
                </select>
                <button type="submit" class="primary-btn" style="padding:8px 16px;font-size:13px;border-radius:10px;">
                    Track
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>


{{-- ── Full Portfolio Table ────────────────────────────────── --}}
@if ($myInterests->isNotEmpty())
<div class="panel-card">
    <h3>Full Portfolio</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Venture</th>
                    <th>Founder</th>
                    <th>Sector</th>
                    <th>Goal</th>
                    <th>Raised</th>
                    <th>Interest</th>
                    <th>Since</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($myInterests as $interest)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="venture-logo" style="width:30px;height:30px;font-size:12px;border-radius:8px;flex-shrink:0;">
                                {{ strtoupper(substr($interest->venture->title, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--text);">{{ $interest->venture->title }}</div>
                                <span class="badge badge-{{ $interest->venture->status }}" style="margin-top:2px;">
                                    {{ ucfirst($interest->venture->status) }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;color:var(--text);">{{ $interest->venture->founder->name }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $interest->venture->founder->company_name }}</div>
                    </td>
                    <td><span style="color:var(--muted);font-size:13px;">{{ $interest->venture->sector ?? '—' }}</span></td>
                    <td>${{ number_format($interest->venture->goal_amount, 0) }}</td>
                    <td>
                        <div style="color:var(--green);font-weight:600;">${{ number_format($interest->venture->raised_amount, 0) }}</div>
                        <div class="progress-bar" style="margin-top:4px;width:80px;">
                            <div class="progress-fill" style="width:{{ $interest->venture->progressPercent() }}%;"></div>
                        </div>
                    </td>
                    <td><span class="badge badge-{{ $interest->interest_level }}">{{ ucfirst($interest->interest_level) }}</span></td>
                    <td style="font-size:12px;color:var(--muted);">{{ $interest->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
