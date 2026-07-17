@extends('layouts.header')

@section('page-title')
    Welcome back, {{ Auth::user()->name }} 👋
@endsection

@section('page-subtitle')
    Here's what's happening with your ventures today.
@endsection

@section('content')

{{-- ── Stats Cards ────────────────────────────────────────── --}}
<div class="dashboard-cards">

    <div class="dashboard-card">
        <div class="card-icon green">
            <i class="fa-solid fa-dollar-sign"></i>
        </div>
        <p>Total Raised</p>
        <h2>${{ $totalRaised >= 1000000
            ? number_format($totalRaised / 1000000, 2) . 'M'
            : ($totalRaised >= 1000 ? number_format($totalRaised / 1000, 1) . 'K' : number_format($totalRaised, 0)) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-arrow-trend-up"></i> +12% vs last month
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon blue">
            <i class="fa-solid fa-bullhorn"></i>
        </div>
        <p>Active Campaigns</p>
        <h2>{{ $activeCampaignsCount }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-circle-check"></i> Running now
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon purple">
            <i class="fa-solid fa-eye"></i>
        </div>
        <p>Investor Views</p>
        <h2>{{ number_format($totalViews) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-arrow-trend-up"></i> Profile visits
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon orange">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <p>Unread Messages</p>
        <h2>{{ $unreadMessages }}</h2>
        @if ($unreadMessages > 0)
            <span class="card-trend down">
                <i class="fa-solid fa-bell"></i> Needs attention
            </span>
        @else
            <span class="card-trend up">
                <i class="fa-solid fa-circle-check"></i> All read
            </span>
        @endif
    </div>

</div>


{{-- ── Chart Row ────────────────────────────────────────────── --}}
<div class="chart-row">

    {{-- Fundraising Growth Chart --}}
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-line"></i> Fundraising Growth (Last 6 Months)</h3>
        <div class="chart-container">
            <canvas id="fundraisingChart"
                data-labels='@json($chartData["labels"])'
                data-values='@json($chartData["values"])'
            ></canvas>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="chart-card">
        <h3><i class="fa-solid fa-rocket"></i> My Ventures</h3>

        @forelse ($ventures->take(4) as $venture)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
            <div class="venture-logo" style="width:36px;height:36px;font-size:14px;border-radius:10px;">
                {{ strtoupper(substr($venture->title, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $venture->title }}
                </div>
                <div style="font-size:11px;color:var(--muted);">
                    {{ $venture->sector ?? 'General' }} · {{ $venture->stage ?? 'Seed' }}
                </div>
                <div class="progress-bar" style="margin-top:5px;">
                    <div class="progress-fill" style="width:{{ $venture->progressPercent() }}%;"></div>
                </div>
            </div>
            <span class="badge badge-{{ $venture->status }}">{{ ucfirst($venture->status) }}</span>
        </div>
        @empty
        <div class="empty-state" style="padding:30px 0;">
            <i class="fa-solid fa-rocket" style="font-size:32px;margin-bottom:10px;display:block;"></i>
            <p>No ventures yet. Create your first venture!</p>
            <a href="#" class="primary-btn" style="display:inline-block;margin-top:14px;font-size:13px;padding:10px 22px;">
                + Add Venture
            </a>
        </div>
        @endforelse
    </div>

</div>


{{-- ── Bottom: Campaigns + Investor Activity ─────────────────── --}}
<div class="dashboard-bottom">

    {{-- Campaign Table --}}
    <div class="panel-card">
        <h3>
            My Campaigns
            <a href="#">View all →</a>
        </h3>

        @if ($recentCampaigns->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-bullhorn"></i>
                <h4>No campaigns yet</h4>
                <p>Launch a campaign to start raising funds.</p>
                <a href="#" class="primary-btn" style="display:inline-block;margin-top:14px;font-size:13px;padding:10px 22px;">
                    + Create Campaign
                </a>
            </div>
        @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Goal</th>
                        <th>Raised</th>
                        <th>Progress</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentCampaigns as $campaign)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text);">{{ $campaign->title }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ $campaign->venture->title }}</div>
                        </td>
                        <td>${{ number_format($campaign->goal, 0) }}</td>
                        <td>${{ number_format($campaign->raised, 0) }}</td>
                        <td style="min-width:100px;">
                            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">
                                {{ $campaign->progressPercent() }}%
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $campaign->progressPercent() }}%;"></div>
                            </div>
                        </td>
                        <td><span class="badge badge-{{ $campaign->status }}">{{ ucfirst($campaign->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Investor Activity --}}
    <div class="panel-card">
        <h3>
            Investor Activity
            <a href="#">All →</a>
        </h3>

        @if ($recentInterests->isEmpty())
            <div class="empty-state" style="padding:30px 0;">
                <i class="fa-solid fa-users"></i>
                <h4>No activity yet</h4>
                <p>Investors will appear here when they show interest in your ventures.</p>
            </div>
        @else
        <div class="activity-list">
            @foreach ($recentInterests as $interest)
            <div class="activity-item">
                <img
                    src="{{ $interest->investor->avatarUrl() }}"
                    alt="{{ $interest->investor->name }}"
                    class="activity-avatar"
                >
                <div class="activity-info">
                    <strong>{{ $interest->investor->name }}</strong>
                    <span>{{ $interest->venture->title }}</span>
                </div>
                <span class="badge badge-{{ $interest->interest_level }}">
                    {{ ucfirst($interest->interest_level) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection