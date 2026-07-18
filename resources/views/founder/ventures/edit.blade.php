@extends('layouts.header')

@section('page-title', 'Edit Venture')
@section('page-subtitle', 'Update your venture details.')

@section('content')

<div style="max-width:780px;margin:0 auto;">

    {{-- Breadcrumb --}}
    <div class="breadcrumb" style="margin-bottom:20px;">
        <a href="{{ route('founder.ventures') }}">My Ventures</a>
        <span class="sep">/</span>
        <span class="current">Edit: {{ $venture->title }}</span>
    </div>

    <div class="panel-card">

        <h3 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:24px;display:flex;align-items:center;gap:10px;">
            <span style="width:36px;height:36px;border-radius:10px;background:rgba(0,217,156,.15);display:inline-flex;align-items:center;justify-content:center;color:var(--green);">
                <i class="fa-solid fa-pen-to-square"></i>
            </span>
            Edit Venture
        </h3>

        @if ($errors->any())
        <div style="padding:14px 18px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>{{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('founder.ventures.update', $venture->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Venture Title *</label>
                    <input type="text" name="title" class="form-input" value="{{ old('title', $venture->title) }}" required>
                </div>
                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-input">
                        @foreach(['draft','active','paused','completed'] as $s)
                            <option value="{{ $s }}" {{ $venture->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="4">{{ old('description', $venture->description) }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Sector</label>
                    <input type="text" name="sector" class="form-input" value="{{ old('sector', $venture->sector) }}">
                </div>
                <div>
                    <label class="form-label">Stage</label>
                    <select name="stage" class="form-input">
                        <option value="">Select stage</option>
                        @foreach(['Pre-Seed','Seed','Series A','Series B','Series C','Growth'] as $st)
                            <option value="{{ $st }}" {{ $venture->stage === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Funding Goal ($) *</label>
                    <input type="number" name="goal_amount" class="form-input" min="0" step="1000" value="{{ old('goal_amount', $venture->goal_amount) }}" required>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label">
                    Update Logo
                    @if ($venture->logo_path)
                        <span style="color:var(--green);font-size:11px;margin-left:8px;">(Current logo exists)</span>
                    @endif
                </label>
                <input type="file" name="logo" class="form-input" accept="image/*" style="padding:10px 14px;cursor:pointer;">
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('founder.ventures') }}"
                   style="padding:12px 24px;border-radius:50px;border:1px solid var(--border);background:transparent;color:var(--text);font-size:14px;font-weight:600;text-decoration:none;">
                    Cancel
                </a>
                <button type="submit" class="primary-btn" style="padding:12px 32px;font-size:14px;border-radius:50px;">
                    <i class="fa-solid fa-check" style="margin-right:8px;"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
{{-- Form styles loaded via resources/css/forms.css --}}
@endpush

@endsection
