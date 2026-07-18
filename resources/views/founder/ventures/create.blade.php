@extends('layouts.header')

@section('page-title', 'Add New Venture')
@section('page-subtitle', 'Tell investors about your startup.')

@section('content')

<div style="max-width:780px;margin:0 auto;">

    <div class="panel-card">

        <h3 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:24px;display:flex;align-items:center;gap:10px;">
            <span style="width:36px;height:36px;border-radius:10px;background:rgba(0,217,156,.15);display:inline-flex;align-items:center;justify-content:center;color:var(--green);">
                <i class="fa-solid fa-rocket"></i>
            </span>
            Venture Details
        </h3>

        @if ($errors->any())
        <div style="padding:14px 18px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;margin-bottom:20px;font-size:14px;">
            <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('founder.ventures.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Venture Title *</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. GreenEnergy AI" value="{{ old('title') }}" required>
                </div>
                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-input">
                        <option value="draft"  {{ old('status','draft') === 'draft'  ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="paused" {{ old('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-input" rows="4"
                    placeholder="Describe your venture, its mission, and the problem it solves...">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label class="form-label">Sector / Industry</label>
                    <input type="text" name="sector" class="form-input" placeholder="e.g. FinTech, HealthTech" value="{{ old('sector') }}">
                </div>
                <div>
                    <label class="form-label">Stage</label>
                    <select name="stage" class="form-input">
                        <option value="">Select stage</option>
                        @foreach(['Pre-Seed','Seed','Series A','Series B','Series C','Growth'] as $s)
                            <option value="{{ $s }}" {{ old('stage') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Funding Goal ($) *</label>
                    <input type="number" name="goal_amount" class="form-input" placeholder="500000" min="0" step="1000" value="{{ old('goal_amount') }}" required>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
                <div>
                    <label class="form-label">Venture Logo <span style="color:var(--muted);font-size:12px;">(JPG/PNG, max 2MB)</span></label>
                    <input type="file" name="logo" class="form-input" accept="image/*"
                        style="padding:10px 14px;cursor:pointer;">
                </div>
                <div>
                    <label class="form-label">Pitch Deck <span style="color:var(--muted);font-size:12px;">(PDF/PPTX, max 20MB)</span></label>
                    <input type="file" name="pitch_deck" class="form-input" accept=".pdf,.pptx,.ppt"
                        style="padding:10px 14px;cursor:pointer;">
                </div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('founder.ventures') }}"
                   style="padding:12px 24px;border-radius:50px;border:1px solid var(--border);background:transparent;color:var(--text);font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,.06)'"
                   onmouseout="this.style.background='transparent'">
                    Cancel
                </a>
                <button type="submit" class="primary-btn" style="padding:12px 32px;font-size:14px;border-radius:50px;">
                    <i class="fa-solid fa-rocket" style="margin-right:8px;"></i>Create Venture
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
.form-label {
    display:block;
    font-size:13px;
    font-weight:600;
    color:var(--text);
    margin-bottom:7px;
}
.form-input {
    width:100%;
    padding:12px 16px;
    border-radius:12px;
    border:1px solid var(--border);
    background:var(--bg-soft);
    color:var(--text);
    font-size:14px;
    font-family:inherit;
    outline:none;
    transition:border-color .2s;
    box-sizing:border-box;
}
.form-input:focus { border-color:var(--green); }
textarea.form-input { resize:vertical; }
select.form-input { appearance:auto; }
</style>
@endpush

@endsection
