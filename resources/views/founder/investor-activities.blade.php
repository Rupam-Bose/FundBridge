@extends('layouts.header')

@section('page-title', 'Investor Activities')
@section('page-subtitle', 'See who is interested in your ventures.')

@section('content')

{{-- Stats --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-users"></i></div>
        <p>Total Interests</p><h2>{{ $stats['total'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-fire"></i></div>
        <p>High Interest</p><h2>{{ $stats['high'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-star-half-stroke"></i></div>
        <p>Medium</p><h2>{{ $stats['medium'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-star"></i></div>
        <p>Low</p><h2>{{ $stats['low'] }}</h2>
    </div>
</div>

{{-- Filters --}}
<div class="panel-card" style="margin-bottom:20px;padding:18px 24px;">
    <form method="GET" action="{{ route('founder.investor-activities') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

        <div style="flex:1;min-width:200px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Search Investor</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
        </div>

        <div style="min-width:160px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Interest Level</label>
            <select name="interest_level"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Levels</option>
                <option value="high"   {{ request('interest_level') === 'high'   ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('interest_level') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low"    {{ request('interest_level') === 'low'    ? 'selected' : '' }}>Low</option>
            </select>
        </div>

        <div style="min-width:180px;">
            <label style="font-size:12px;font-weight:600;color:var(--muted);display:block;margin-bottom:5px;">Venture</label>
            <select name="venture_id"
                style="width:100%;padding:10px 14px;border-radius:10px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                <option value="">All Ventures</option>
                @foreach ($ventures as $v)
                    <option value="{{ $v->id }}" {{ request('venture_id') == $v->id ? 'selected' : '' }}>{{ $v->title }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="primary-btn" style="padding:10px 24px;font-size:13px;border-radius:10px;white-space:nowrap;">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
        </button>
        <a href="{{ route('founder.investor-activities') }}"
           style="padding:10px 16px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:13px;text-decoration:none;white-space:nowrap;">
            Clear
        </a>
    </form>
</div>

{{-- Table --}}
<div class="panel-card">
    <h3 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:18px;">
        Investor Interest Log
        <span style="font-size:13px;font-weight:400;color:var(--muted);margin-left:8px;">{{ $interests->total() }} records</span>
    </h3>

    @if ($interests->isEmpty())
    <div class="empty-state" style="padding:50px;">
        <i class="fa-solid fa-users"></i>
        <h4>No investor interest yet</h4>
        <p>When investors show interest in your ventures, they'll appear here.</p>
    </div>
    @else
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Investor</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Venture</th>
                    <th>Interest</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($interests as $interest)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $interest->investor->avatarUrl() }}" alt="{{ $interest->investor->name }}"
                                style="width:34px;height:34px;border-radius:50%;border:2px solid var(--border);">
                            <div style="font-weight:600;color:var(--text);">{{ $interest->investor->name }}</div>
                        </div>
                    </td>
                    <td style="color:var(--muted);font-size:13px;">{{ $interest->investor->email }}</td>
                    <td style="color:var(--muted);font-size:13px;">{{ $interest->investor->company_name ?? '—' }}</td>
                    <td>
                        <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $interest->venture->title }}</div>
                        <span class="badge badge-{{ $interest->venture->status }}" style="margin-top:3px;">{{ ucfirst($interest->venture->status) }}</span>
                    </td>
                    <td><span class="badge badge-{{ $interest->interest_level }}">{{ ucfirst($interest->interest_level) }}</span></td>
                    <td style="font-size:13px;color:var(--muted);max-width:180px;">
                        {{ $interest->note ? Str::limit($interest->note, 50) : '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $interest->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($interests->hasPages())
    <div style="padding:16px;display:flex;justify-content:center;">
        {{ $interests->appends(request()->query())->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
