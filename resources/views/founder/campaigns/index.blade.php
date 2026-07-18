@extends('layouts.header')

@section('page-title', 'Campaigns')
@section('page-subtitle', 'Launch and manage your fundraising campaigns.')

@section('content')

{{-- Stats --}}
<div class="dashboard-cards" style="grid-template-columns:repeat(4,1fr);">
    <div class="dashboard-card">
        <div class="card-icon blue"><i class="fa-solid fa-bullhorn"></i></div>
        <p>Total</p><h2>{{ $stats['total'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon green"><i class="fa-solid fa-fire"></i></div>
        <p>Active</p><h2>{{ $stats['active'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon purple"><i class="fa-solid fa-circle-check"></i></div>
        <p>Completed</p><h2>{{ $stats['completed'] }}</h2>
    </div>
    <div class="dashboard-card">
        <div class="card-icon orange"><i class="fa-solid fa-dollar-sign"></i></div>
        <p>Total Raised</p>
        <h2>${{ number_format($stats['total_raised'],0) }}</h2>
    </div>
</div>

{{-- Header --}}
<div class="section-header" style="margin-bottom:20px;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:var(--text);">All Campaigns</h2>
        <p style="font-size:13px;color:var(--muted);margin-top:3px;">{{ $campaigns->total() }} campaigns</p>
    </div>
    <a href="{{ route('founder.campaigns.create') }}" class="primary-btn" style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;font-size:14px;border-radius:50px;">
        <i class="fa-solid fa-plus"></i> New Campaign
    </a>
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
        <p>Launch a campaign to start raising funds for your ventures.</p>
        <a href="{{ route('founder.campaigns.create') }}" class="primary-btn" style="display:inline-block;margin-top:20px;padding:12px 28px;">
            <i class="fa-solid fa-plus" style="margin-right:8px;"></i>Create Campaign
        </a>
    </div>
</div>
@else

<div class="panel-card">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaigns as $campaign)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text);">{{ $campaign->title }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ Str::limit($campaign->description,40) }}</div>
                    </td>
                    <td>
                        <span style="font-size:13px;color:var(--text);">{{ $campaign->venture->title }}</span>
                    </td>
                    <td>${{ number_format($campaign->goal,0) }}</td>
                    <td style="color:var(--green);font-weight:600;">${{ number_format($campaign->raised,0) }}</td>
                    <td style="min-width:100px;">
                        <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">{{ $campaign->progressPercent() }}%</div>
                        <div class="progress-bar"><div class="progress-fill" style="width:{{ min($campaign->progressPercent(),100) }}%;"></div></div>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">
                        {{ $campaign->deadline ? \Carbon\Carbon::parse($campaign->deadline)->format('d M Y') : '—' }}
                    </td>
                    <td><span class="badge badge-{{ $campaign->status }}">{{ ucfirst($campaign->status) }}</span></td>
                    <td>
                        {{-- Inline edit form --}}
                        <button onclick="toggleEdit({{ $campaign->id }})"
                            style="padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:transparent;color:var(--text);cursor:pointer;font-size:12px;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
                            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <form method="POST" action="{{ route('founder.campaigns.destroy', $campaign->id) }}"
                              onsubmit="return confirm('Delete this campaign?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:transparent;color:#ef4444;cursor:pointer;font-size:12px;margin-left:6px;transition:all .2s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.1)'"
                                onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                {{-- Inline edit row --}}
                <tr id="edit-row-{{ $campaign->id }}" style="display:none;">
                    <td colspan="8" style="padding:0;">
                        <div style="padding:16px 20px;background:rgba(0,217,156,.05);border-top:1px solid var(--border);">
                            <form method="POST" action="{{ route('founder.campaigns.update', $campaign->id) }}">
                                @csrf @method('PUT')
                                <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:12px;align-items:end;">
                                    <div>
                                        <label style="font-size:11px;color:var(--muted);font-weight:600;display:block;margin-bottom:4px;">Title</label>
                                        <input type="text" name="title" value="{{ $campaign->title }}" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;color:var(--muted);font-weight:600;display:block;margin-bottom:4px;">Goal ($)</label>
                                        <input type="number" name="goal" value="{{ $campaign->goal }}" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;color:var(--muted);font-weight:600;display:block;margin-bottom:4px;">Raised ($)</label>
                                        <input type="number" name="raised" value="{{ $campaign->raised }}" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;color:var(--muted);font-weight:600;display:block;margin-bottom:4px;">Deadline</label>
                                        <input type="date" name="deadline" value="{{ $campaign->deadline ? \Carbon\Carbon::parse($campaign->deadline)->format('Y-m-d') : '' }}" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;box-sizing:border-box;">
                                    </div>
                                    <div>
                                        <label style="font-size:11px;color:var(--muted);font-weight:600;display:block;margin-bottom:4px;">Status</label>
                                        <select name="status" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:13px;outline:none;">
                                            @foreach(['active','paused','completed'] as $s)
                                                <option value="{{ $s }}" {{ $campaign->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <input type="hidden" name="description" value="{{ $campaign->description }}">
                                        <button type="submit" class="primary-btn" style="padding:9px 16px;font-size:13px;border-radius:8px;white-space:nowrap;">
                                            <i class="fa-solid fa-check"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($campaigns->hasPages())
    <div style="padding:16px;display:flex;justify-content:center;">
        {{ $campaigns->links() }}
    </div>
    @endif
</div>
@endif

@push('scripts')
<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-row-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
@endpush

@endsection
