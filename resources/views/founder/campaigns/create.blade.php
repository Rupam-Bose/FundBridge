@extends('layouts.header')

@section('page-title', 'Create Campaign')
@section('page-subtitle', 'Launch a new fundraising campaign.')

@section('content')

<div style="max-width:680px;margin:0 auto;">

    <div class="breadcrumb" style="margin-bottom:20px;">
        <a href="{{ route('founder.campaigns') }}">Campaigns</a>
        <span class="sep">/</span>
        <span class="current">Create New</span>
    </div>

    <div class="panel-card">

        <h3 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:24px;">
            <i class="fa-solid fa-bullhorn" style="color:var(--green);margin-right:10px;"></i>
            Campaign Details
        </h3>

        @if ($errors->any())
        <div style="padding:14px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('founder.campaigns.store') }}">
            @csrf

            <div style="margin-bottom:20px;">
                <label class="form-label">Venture *</label>
                <select name="venture_id" class="form-input" required>
                    <option value="">Select a venture</option>
                    @foreach ($ventures as $v)
                        <option value="{{ $v->id }}" {{ old('venture_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->title }} ({{ ucfirst($v->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Campaign Title *</label>
                <input type="text" name="title" class="form-input" placeholder="e.g. Seed Round 2026" value="{{ old('title') }}" required>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="3" placeholder="What is this campaign for?">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Funding Goal ($) *</label>
                    <input type="number" name="goal" class="form-input" placeholder="500000" min="1" step="1000" value="{{ old('goal') }}" required>
                </div>
                <div>
                    <label class="form-label">Amount Already Raised ($)</label>
                    <input type="number" name="raised" class="form-input" placeholder="0" min="0" step="100" value="{{ old('raised',0) }}">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
                <div>
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-input" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('deadline') }}">
                </div>
                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-input">
                        <option value="active"  {{ old('status','active') === 'active'  ? 'selected' : '' }}>Active</option>
                        <option value="paused"  {{ old('status') === 'paused'  ? 'selected' : '' }}>Paused</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('founder.campaigns') }}"
                   style="padding:12px 24px;border-radius:50px;border:1px solid var(--border);background:transparent;color:var(--text);font-size:14px;font-weight:600;text-decoration:none;">
                    Cancel
                </a>
                <button type="submit" class="primary-btn" style="padding:12px 32px;font-size:14px;border-radius:50px;">
                    <i class="fa-solid fa-bullhorn" style="margin-right:8px;"></i>Launch Campaign
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.form-label { display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:7px; }
.form-input { width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--border);background:var(--bg-soft);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;box-sizing:border-box; }
.form-input:focus { border-color:var(--green); }
textarea.form-input { resize:vertical; }
</style>
@endpush

@endsection
