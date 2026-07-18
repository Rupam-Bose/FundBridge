@extends('layouts.header')

@section('page-title', 'Platform Reports')
@section('page-subtitle', 'Full analytics and statistics for the FundBridge platform.')

@section('content')

 <!-- KPI Cards  -->
<div class="dashboard-cards" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-users"></i></div>
        <p>Total Users</p>
        <h2>{{ number_format($stats['total_users']) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-user-tie"></i> {{ $stats['total_founders'] }} founders ·
            <i class="fa-solid fa-briefcase"></i> {{ $stats['total_investors'] }} investors
        </span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-dollar-sign"></i></div>
        <p>Total Raised (Platform)</p>
        <h2>${{ number_format($stats['total_raised']/1000, 1) }}K</h2>
        <span class="card-trend up"><i class="fa-solid fa-arrow-trend-up"></i> Across all ventures</span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <p>Total Invested</p>
        <h2>${{ number_format($stats['total_invested']/1000, 1) }}K</h2>
        <span class="card-trend up"><i class="fa-solid fa-chart-line"></i> Direct investments</span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-rocket"></i></div>
        <p>Total Ventures</p>
        <h2>{{ $stats['total_ventures'] }}</h2>
        <span class="card-trend up">{{ $stats['active_ventures'] }} active</span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-bullhorn"></i></div>
        <p>Campaigns</p>
        <h2>{{ $stats['total_campaigns'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-heart"></i></div>
        <p>Investor Interests</p>
        <h2>{{ $stats['total_interests'] }}</h2>
    </div>
</div>

 <!-- Charts Row 1 -->
<div class="chart-row" style="margin-bottom:24px;">
    <div class="chart-card">
        <h3><i class="fa-solid fa-users"></i> Monthly New Users</h3>
        <div class="chart-container">
            <canvas id="usersChart"
                data-labels='@json($monthlySignups["labels"])'
                data-values='@json($monthlySignups["values"])'
            ></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3><i class="fa-solid fa-dollar-sign"></i> Monthly Investments ($)</h3>
        <div class="chart-container">
            <canvas id="raisedChart"
                data-labels='@json($monthlyRaised["labels"])'
                data-values='@json($monthlyRaised["values"])'
            ></canvas>
        </div>
    </div>
</div>

 <!-- Charts Row 2  -->
<div class="chart-row" style="margin-bottom:24px;">
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-pie"></i> Ventures by Sector</h3>
        @if (empty($sectorData))
        <div class="empty-state" style="padding:40px 0;"><p>No sector data available.</p></div>
        @else
        <div class="chart-container">
            <canvas id="sectorChart"
                data-labels='@json(array_keys($sectorData))'
                data-values='@json(array_values($sectorData))'
            ></canvas>
        </div>
        @endif
    </div>
    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-bar"></i> Ventures by Stage</h3>
        @if (empty($stageData))
        <div class="empty-state" style="padding:40px 0;"><p>No stage data available.</p></div>
        @else
        <div class="chart-container">
            <canvas id="stageChart"
                data-labels='@json(array_keys($stageData))'
                data-values='@json(array_values($stageData))'
            ></canvas>
        </div>
        @endif
    </div>
</div>

 <!-- Bottom Row  -->
<div class="dashboard-bottom" style="margin-bottom:24px;">

     <!-- Top Ventures  -->
    <div class="panel-card">
        <h3>Top 10 Ventures by Raised Amount</h3>
        @forelse ($topVentures as $i => $venture)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);">
            <span style="width:24px;height:24px;border-radius:6px;background:rgba(0,217,156,.15);color:var(--green);font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                {{ $i+1 }}
            </span>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $venture->title }}</div>
                <div style="font-size:11px;color:var(--muted);">{{ $venture->founder->name }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div style="font-size:13px;font-weight:700;color:var(--green);">${{ number_format($venture->raised_amount,0) }}</div>
                <div style="font-size:11px;color:var(--muted);">of ${{ number_format($venture->goal_amount,0) }}</div>
            </div>
            <span class="badge badge-{{ $venture->status }}">{{ ucfirst($venture->status) }}</span>
        </div>
        @empty
        <div class="empty-state" style="padding:30px 0;"><p>No venture data yet.</p></div>
        @endforelse
    </div>

     <!-- Top Founders  -->
    <div class="panel-card">
        <h3>Top Founders by Total Raised</h3>
        @forelse ($topFounders as $i => $founder)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);">
            <span style="font-size:12px;font-weight:800;color:var(--muted);width:20px;flex-shrink:0;">#{{ $i+1 }}</span>
            <img src="{{ $founder->avatarUrl() }}" style="width:32px;height:32px;border-radius:50%;border:1px solid var(--border);" alt="">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $founder->name }}</div>
                <div style="font-size:11px;color:var(--muted);">{{ $founder->company_name ?? $founder->email }}</div>
            </div>
            <div style="font-size:13px;font-weight:700;color:var(--green);">
                ${{ number_format($founder->ventures_sum_raised_amount ?? 0, 0) }}
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:30px 0;"><p>No data yet.</p></div>
        @endforelse
    </div>

</div>

 <!-- Recent Investments Table  -->
@if ($recentInvestments->isNotEmpty())
<div class="panel-card">
    <h3>Recent Investments</h3>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Investor</th>
                    <th>Venture</th>
                    <th>Campaign</th>
                    <th>Amount</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentInvestments as $inv)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="{{ $inv->investor->avatarUrl() }}" style="width:28px;height:28px;border-radius:50%;" alt="">
                            <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $inv->investor->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $inv->venture->title }}</td>
                    <td style="font-size:13px;color:var(--muted);">{{ $inv->campaign->title }}</td>
                    <td style="color:var(--green);font-weight:700;">${{ number_format($inv->amount, 2) }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $inv->note ? Str::limit($inv->note, 40) : '—' }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $inv->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
