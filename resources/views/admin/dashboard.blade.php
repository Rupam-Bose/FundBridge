@extends('layouts.header')

@section('page-title')
    Admin Dashboard
@endsection

@section('page-subtitle')
    Platform overview and management controls.
@endsection

@push('styles')
@vite(['resources/css/admin.css'])
@endpush

@section('content')

{{-- Platform Stats Cards --}}
<div class="dashboard-cards">

    <div class="dashboard-card">
        <div class="card-icon green">
            <i class="fa-solid fa-users"></i>
        </div>
        <p>Total Users</p>
        <h2>{{ number_format($totalUsers) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-arrow-trend-up"></i> Platform members
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon blue">
            <i class="fa-solid fa-rocket"></i>
        </div>
        <p>Total Ventures</p>
        <h2>{{ number_format($totalVentures) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-layer-group"></i> All stages
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon orange">
            <i class="fa-solid fa-bullhorn"></i>
        </div>
        <p>Active Campaigns</p>
        <h2>{{ number_format($totalCampaigns) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-fire"></i> Fundraising now
        </span>
    </div>

    <div class="dashboard-card">
        <div class="card-icon purple">
            <i class="fa-solid fa-dollar-sign"></i>
        </div>
        <p>Total Raised</p>
        <h2>${{ $totalRaised >= 1000000
            ? number_format($totalRaised / 1000000, 2) . 'M'
            : ($totalRaised >= 1000 ? number_format($totalRaised / 1000, 1) . 'K' : number_format($totalRaised, 0)) }}</h2>
        <span class="card-trend up">
            <i class="fa-solid fa-chart-line"></i> Platform total
        </span>
    </div>

</div>


{{--Role Breakdown Row--}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;">

    <div class="dashboard-card" style="text-align:center;">
        <div class="card-icon founder" style="width:48px;height:48px;margin:0 auto 12px;background:rgba(0,217,156,0.15);color:var(--green);">
            <i class="fa-solid fa-user-tie"></i>
        </div>
        <p style="text-transform:none;letter-spacing:0;font-size:14px;">Founders</p>
        <h2>{{ $totalFounders }}</h2>
    </div>

    <div class="dashboard-card" style="text-align:center;">
        <div class="card-icon" style="width:48px;height:48px;margin:0 auto 12px;background:rgba(139,92,246,0.15);color:#a78bfa;">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <p style="text-transform:none;letter-spacing:0;font-size:14px;">Investors</p>
        <h2>{{ $totalInvestors }}</h2>
    </div>

    <div class="dashboard-card" style="text-align:center;">
        <div class="card-icon" style="width:48px;height:48px;margin:0 auto 12px;background:rgba(239,68,68,0.15);color:#ef4444;">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <p style="text-transform:none;letter-spacing:0;font-size:14px;">Admins</p>
        <h2>{{ $totalAdmins }}</h2>
    </div>

</div>


{{--Charts Row--}}
<div class="chart-row" style="margin-bottom:28px;">

    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-bar"></i> User Growth (Last 6 Months)</h3>
        <div class="chart-container">
            <canvas id="userGrowthChart"
                data-labels='@json($userGrowthChart["labels"])'
                data-values='@json($userGrowthChart["values"])'
            ></canvas>
        </div>
    </div>

    <div class="chart-card">
        <h3><i class="fa-solid fa-chart-pie"></i> Ventures by Sector</h3>
        @if (empty($sectorChart['labels']))
            <div class="empty-state" style="padding:30px 0;">
                <i class="fa-solid fa-pie-chart" style="font-size:28px;margin-bottom:8px;display:block;"></i>
                <p>No sector data yet.</p>
            </div>
        @else
        <div class="chart-container">
            <canvas id="sectorChart"
                data-labels='@json($sectorChart["labels"])'
                data-values='@json($sectorChart["values"])'
            ></canvas>
        </div>
        @endif
    </div>

</div>


{{--Recent Users--}}
<div class="panel-card">
    <h3>
        Recent Registrations
        <a href="{{ route('admin.users') }}">Manage all →</a>
    </h3>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Company</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentUsers as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                                style="width:32px;height:32px;border-radius:50%;border:2px solid var(--border);">
                            <span style="font-weight:600;color:var(--text);">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--muted);font-size:13px;">{{ $user->email }}</td>
                    <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td style="color:var(--muted);font-size:13px;">{{ $user->company_name ?? '—' }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $user->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:30px;color:var(--muted);">No users yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
