@extends('layouts.header')

@section('page-title', 'Ventures')
@section('page-subtitle', 'All ventures on the FundBridge platform.')

@section('content')

{{-- Stats --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(5,1fr);">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-layer-group"></i></div>
        <p>Total</p><h2>{{ $stats['total'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-fire"></i></div>
        <p>Active</p><h2>{{ $stats['active'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-pause"></i></div>
        <p>Paused</p><h2>{{ $stats['paused'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-circle-check"></i></div>
        <p>Completed</p><h2>{{ $stats['completed'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-dollar-sign"></i></div>
        <p>Total Raised</p>
        <h2>${{ number_format($stats['total_raised']/1000, 1) }}K</h2>
    </div>
</div>

{{-- Filters --}}
<div class="panel-card" style="margin-bottom:20px;padding:18px 24px;">
    <form method="GET" action="{{ route('admin.ventures') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Venture title or sector..."
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <div style="min-width:140px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Status</label>
            <select name="status" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Status</option>
                @foreach (['active','paused','draft','completed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:150px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sector</label>
            <select name="sector" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Sectors</option>
                @foreach ($sectors as $s)
                    <option value="{{ $s }}" {{ request('sector') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Sort By</label>
            <select name="sort" onchange="this.form.submit()"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="newest"       {{ request('sort','newest') === 'newest'       ? 'selected' : '' }}>Newest</option>
                <option value="most_raised"  {{ request('sort') === 'most_raised'  ? 'selected' : '' }}>Most Raised</option>
                <option value="most_interests" {{ request('sort') === 'most_interests' ? 'selected' : '' }}>Most Interest</option>
                <option value="goal_high"    {{ request('sort') === 'goal_high'    ? 'selected' : '' }}>Highest Goal</option>
            </select>
        </div>
        <button type="submit" class="primary-btn" style="padding:10px 20px;font-size:13px;border-radius:10px;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        @if (request()->anyFilled(['search','status','sector','sort']))
        <a href="{{ route('admin.ventures') }}"
           style="padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:13px;text-decoration:none;">Clear</a>
        @endif
    </form>
</div>

{{-- Flash --}}
@if (session('status'))
<div style="padding:12px 18px;border-radius:12px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,0.3);color:var(--green);margin-bottom:20px;font-size:14px;">
    <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
</div>
@endif

<div class="panel-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text);">
            All Ventures
            <span style="font-size:13px;font-weight:400;color:var(--muted);margin-left:8px;">{{ $ventures->total() }} records</span>
        </h3>
    </div>

    @if ($ventures->isEmpty())
    <div class="empty-state" style="padding:50px;">
        <i class="fa-solid fa-rocket"></i>
        <h4>No ventures found</h4>
    </div>
    @else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Venture</th>
                    <th>Founder</th>
                    <th>Sector / Stage</th>
                    <th>Raised / Goal</th>
                    <th>Progress</th>
                    <th>Interests</th>
                    <th>Campaigns</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventures as $venture)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text);">{{ $venture->title }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $venture->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <img src="{{ $venture->founder->avatarUrl() }}" style="width:28px;height:28px;border-radius:50%;border:1px solid var(--border);" alt="">
                            <div>
                                <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $venture->founder->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $venture->founder->company_name ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:12px;color:var(--text);">{{ $venture->sector ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $venture->stage ?? '—' }}</div>
                    </td>
                    <td>
                        <div style="color:var(--green);font-weight:600;font-size:13px;">${{ number_format($venture->raised_amount,0) }}</div>
                        <div style="font-size:11px;color:var(--muted);">of ${{ number_format($venture->goal_amount,0) }}</div>
                    </td>
                    <td style="min-width:100px;">
                        <div style="font-size:11px;color:var(--muted);margin-bottom:3px;">{{ $venture->progressPercent() }}%</div>
                        <div class="progress-bar"><div class="progress-fill" style="width:{{ min($venture->progressPercent(),100) }}%;"></div></div>
                    </td>
                    <td style="text-align:center;font-weight:600;color:var(--text);">{{ $venture->interests_count }}</td>
                    <td style="text-align:center;font-weight:600;color:var(--text);">{{ $venture->campaigns_count }}</td>
                    <td>
                        {{-- Inline status change --}}
                        <form method="POST" action="{{ route('admin.ventures.status', $venture->id) }}">
                            @csrf @method('PUT')
                            <select name="status" onchange="this.form.submit()"
                                style="padding:5px 10px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:12px;outline:none;cursor:pointer;">
                                @foreach(['active','paused','draft','completed'] as $s)
                                    <option value="{{ $s }}" {{ $venture->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.ventures.destroy', $venture->id) }}"
                              onsubmit="return confirm('Delete {{ addslashes($venture->title) }} permanently? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:transparent;color:#ef4444;cursor:pointer;font-size:12px;transition:all .2s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.1)'"
                                onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($ventures->hasPages())
    <div style="padding:16px;display:flex;justify-content:center;">
        {{ $ventures->appends(request()->query())->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
