@extends('layouts.header')

@section('page-title', 'Messages')
@section('page-subtitle', 'Your conversations with founders and investors.')

@section('content')

<div style="max-width:760px;margin:0 auto;">

    @if ($conversations->isEmpty())
    <div class="panel-card">
        <div class="empty-state" style="padding:60px;">
            <i class="fa-solid fa-comments"></i>
            <h4>No messages yet</h4>
            <p>
                @if (Auth::user()->role === 'investor')
                Start a conversation by messaging a founder from the <a href="{{ route('investor.discover') }}" style="color:var(--green);">Discover</a> page or <a href="{{ route('investor.campaigns') }}" style="color:var(--green);">Campaigns</a>.
                @else
                Investors will contact you here. You can also reach investors from the <a href="{{ route('founder.investor-activities') }}" style="color:var(--green);">Investor Activity</a> page.
                @endif
            </p>
        </div>
    </div>
    @else
    <div class="panel-card" style="padding:0;overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
            <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text);">
                <i class="fa-solid fa-inbox" style="color:var(--green);margin-right:8px;"></i>
                Conversations ({{ count($conversations) }})
            </h3>
        </div>

        @foreach ($conversations as $conv)
        @php $p = $conv['partner']; @endphp
        <a href="{{ route('messages.show', $p->id) }}"
           style="display:flex;align-items:center;gap:14px;padding:16px 24px;border-bottom:1px solid rgba(255,255,255,.05);text-decoration:none;transition:background .15s;position:relative;"
           onmouseover="this.style.background='rgba(255,255,255,.03)'"
           onmouseout="this.style.background='transparent'">

            {{-- Unread indicator --}}
            @if ($conv['unread'] > 0)
            <div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:6px;height:6px;border-radius:50%;background:var(--green);"></div>
            @endif

            <img src="{{ $p->avatarUrl() }}" alt="{{ $p->name }}"
                style="width:46px;height:46px;border-radius:50%;border:2px solid {{ $conv['unread'] > 0 ? 'var(--green)' : 'var(--border)' }};flex-shrink:0;">

            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                    <div style="font-size:14px;font-weight:{{ $conv['unread'] > 0 ? '700' : '600' }};color:var(--text);">
                        {{ $p->name }}
                    </div>
                    <div style="font-size:11px;color:var(--muted);flex-shrink:0;margin-left:8px;">
                        {{ $conv['latest']->created_at->diffForHumans() }}
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:13px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80%;">
                        @if ($conv['latest']->sender_id === Auth::id())
                            <span style="color:var(--green);font-weight:600;margin-right:4px;">You:</span>
                        @endif
                        {{ Str::limit($conv['latest']->content, 60) }}
                    </div>
                    @if ($conv['unread'] > 0)
                    <span style="background:var(--green);color:#000;font-size:10px;font-weight:800;padding:2px 7px;border-radius:50px;flex-shrink:0;margin-left:8px;">
                        {{ $conv['unread'] }}
                    </span>
                    @endif
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                    <span class="badge badge-{{ $p->role }}" style="padding:2px 6px;font-size:9px;">{{ ucfirst($p->role) }}</span>
                    @if ($p->company_name)
                    <span style="margin-left:4px;">{{ $p->company_name }}</span>
                    @endif
                </div>
            </div>

            <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:12px;flex-shrink:0;"></i>
        </a>
        @endforeach
    </div>
    @endif
</div>

@endsection
