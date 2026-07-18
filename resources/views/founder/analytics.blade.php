@extends('layouts.header')

@section('page-title', 'Analytics')
@section('page-subtitle', 'Deep insights into your fundraising performance.')

@section('content')

{{-- Summary KPIs --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-dollar-sign"></i></div>
        <p>Total Raised</p>
        <h2>${{ $totalRaised >= 1000000 ? number_format($totalRaised/1000000,2).'M' : number_format($totalRaised/1000,1).'K' }}</h2>
        <span class="card-trend up"><i class="fa-solid fa-chart-line"></i> vs goal</span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-bullseye"></i></div>
        <p>Total Goal</p>
        <h2>${{ $totalGoal >= 1000000 ? number_format($totalGoal/1000000,2).'M' : number_format($totalGoal/1000,1).'K' }}</h2>
        <span class="card-trend up"><i class="fa-solid fa-flag"></i>
            {{ $totalGoal > 0 ? number_format($totalRaised/$totalGoal*100,1).'% funded' : '0%' }}
        </span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-eye"></i></div>
        <p>Profile Views</p>
        <h2>{{ number_format($totalViews) }}</h2>
        <span class="card-trend up"><i class="fa-solid fa-arrow-trend-up"></i> All time</span>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-heart"></i></div>
        <p>Investor Interests</p>
        <h2>{{ $totalInterests }}</h2>
        <span class="card-trend up"><i class="fa-solid fa-users"></i> Total</span>
    </div>
</div>

{{-- Charts Row 1: Monthly Raised + Interest Breakdown --}}
<div class="chart-row" style="margin-bottom:24px;">

    <div class="chart-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="margin:0;"><i class="fa-solid fa-chart-area"></i> Monthly Fundraising (Last 12 Months)</h3>
            <button id="refreshStats" onclick="refreshAnalytics()"
                style="padding:7px 14px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text);font-size:12px;cursor:pointer;transition:all .2s;"
                onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
            </button>
        </div>
        <div class="chart-container">
            <canvas id="fundraisingChart"
                data-labels='@json($monthlyRaised["labels"])'
                data-values='@json($monthlyRaised["values"])'
            ></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-pie"></i> Investor Interest Levels</h3>
        @if ($totalInterests === 0)
            <div class="empty-state" style="padding:40px 0;">
                <i class="fa-solid fa-pie-chart" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                <p>No investor data yet.</p>
            </div>
        @else
        <div class="chart-container">
            <canvas id="interestChart"
                data-labels='["High","Medium","Low"]'
                data-values='[{{ $interestBreakdown["high"] ?? 0 }},{{ $interestBreakdown["medium"] ?? 0 }},{{ $interestBreakdown["low"] ?? 0 }}]'
            ></canvas>
        </div>
        @endif
    </div>

</div>

{{-- Charts Row 2: Ventures + Campaigns table --}}
<div class="dashboard-bottom" style="margin-bottom:24px;">

    {{-- Top Ventures --}}
    <div class="panel-card">
        <h3>Top Ventures by Funding Progress</h3>

        @forelse ($topVentures as $venture)
        <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--border);">
            <div class="venture-logo" style="width:40px;height:40px;font-size:15px;border-radius:10px;flex-shrink:0;">
                {{ strtoupper(substr($venture->title,0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:14px;font-weight:600;color:var(--text);">{{ $venture->title }}</span>
                    <span style="font-size:13px;font-weight:700;color:var(--green);">{{ $venture->progressPercent() }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ min($venture->progressPercent(),100) }}%;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-top:4px;">
                    <span>Raised: ${{ number_format($venture->raised_amount,0) }}</span>
                    <span>Goal: ${{ number_format($venture->goal_amount,0) }}</span>
                </div>
            </div>
            <span class="badge badge-{{ $venture->status }}">{{ ucfirst($venture->status) }}</span>
        </div>
        @empty
        <div class="empty-state" style="padding:30px 0;">
            <i class="fa-solid fa-rocket" style="font-size:28px;margin-bottom:8px;display:block;"></i>
            <p>No venture data yet.</p>
        </div>
        @endforelse
    </div>

    {{-- Sector Chart --}}
    <div class="chart-card">
        <h3><i class="fa-solid fa-layer-group"></i> Ventures by Sector</h3>
        @if (empty($sectorData))
            <div class="empty-state" style="padding:40px 0;">
                <i class="fa-solid fa-pie-chart" style="font-size:28px;display:block;margin-bottom:8px;"></i>
                <p>Add sector data to your ventures.</p>
            </div>
        @else
        <div class="chart-container">
            <canvas id="sectorChart"
                data-labels='@json(array_keys($sectorData))'
                data-values='@json(array_values($sectorData))'
            ></canvas>
        </div>
        @endif
    </div>

</div>

{{-- Campaigns Performance Table --}}
<div class="panel-card">
    <h3>Campaign Performance</h3>
    @if ($campaigns->isEmpty())
    <div class="empty-state" style="padding:40px;">
        <i class="fa-solid fa-bullhorn"></i>
        <h4>No campaigns yet</h4>
        <p><a href="{{ route('founder.campaigns.create') }}" style="color:var(--green);">Create your first campaign →</a></p>
    </div>
    @else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Venture</th>
                    <th>Goal</th>
                    <th>Raised</th>
                    <th>Progress</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaigns as $campaign)
                <tr>
                    <td style="font-weight:600;color:var(--text);">{{ $campaign->title }}</td>
                    <td style="color:var(--muted);font-size:13px;">{{ $campaign->venture->title }}</td>
                    <td>${{ number_format($campaign->goal,0) }}</td>
                    <td style="color:var(--green);font-weight:600;">${{ number_format($campaign->raised,0) }}</td>
                    <td style="min-width:120px;">
                        <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">{{ $campaign->progressPercent() }}%</div>
                        <div class="progress-bar"><div class="progress-fill" style="width:{{ min($campaign->progressPercent(),100) }}%;"></div></div>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $campaign->deadline ? \Carbon\Carbon::parse($campaign->deadline)->format('d M Y') : '—' }}
                    </td>
                    <td><span class="badge badge-{{ $campaign->status }}">{{ ucfirst($campaign->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
async function refreshAnalytics() {
    const btn = document.getElementById('refreshStats');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
    try {
        const res = await fetch('/api/founder/analytics');
        const d   = await res.json();
        btn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Refresh';
        // Update displayed totals (optional: could re-render the chart here)
        console.log('Analytics refreshed:', d);
    } catch(e) {
        btn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i> Refresh';
    }
}
</script>
@endpush

@endsection
