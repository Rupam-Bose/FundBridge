@extends('layouts.header')

@section('page-title')
    User Management
@endsection

@section('page-subtitle')
    View, manage, and moderate all platform users.
@endsection

@push('styles')
@vite(['resources/css/admin.css'])
@endpush

@section('content')

<div class="user-table-wrapper">

    {{-- Table Header: Search & Filter --}}
    <div class="user-table-header">
        <h3><i class="fa-solid fa-users" style="color:var(--green);margin-right:8px;"></i>All Users</h3>
        <div class="table-filters">
            <form method="GET" action="{{ route('admin.users') }}" style="display:flex;gap:10px;">

                <input
                    type="text"
                    name="search"
                    placeholder="Search name or email..."
                    value="{{ request('search') }}"
                    class="table-search-input"
                >

                <select name="role" class="table-filter-select" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="founder"  {{ request('role') === 'founder'  ? 'selected' : '' }}>Founders</option>
                    <option value="investor" {{ request('role') === 'investor' ? 'selected' : '' }}>Investors</option>
                    <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admins</option>
                </select>

                <button type="submit" class="primary-btn" style="padding:8px 18px;font-size:13px;border-radius:10px;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

            </form>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('status'))
        <div style="margin:0 24px 0;padding:12px 16px;border-radius:10px;background:rgba(0,217,156,0.12);border:1px solid rgba(0,217,156,0.3);color:var(--green);font-size:14px;">
            <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Company</th>
                    <th>Joined</th>
                    <th>Change Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td style="color:var(--muted);font-size:12px;">{{ $user->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                                style="width:34px;height:34px;border-radius:50%;border:2px solid var(--border);">
                            <div>
                                <div style="font-weight:600;color:var(--text);font-size:14px;">{{ $user->name }}</div>
                                @if ($user->bio)
                                    <div style="font-size:11px;color:var(--muted);">{{ Str::limit($user->bio, 30) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--muted);">{{ $user->email }}</td>
                    <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td style="font-size:13px;color:var(--muted);">{{ $user->company_name ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $user->created_at->format('d M Y') }}</td>

                    {{-- Change Role --}}
                    <td>
                        <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="role-form">
                            @csrf
                            <select name="role" class="role-select">
                                <option value="founder"  {{ $user->role === 'founder'  ? 'selected' : '' }}>Founder</option>
                                <option value="investor" {{ $user->role === 'investor' ? 'selected' : '' }}>Investor</option>
                                <option value="admin"    {{ $user->role === 'admin'    ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button type="submit" class="btn-icon" title="Save role">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    </td>

                    {{-- Delete --}}
                    <td>
                        @if ($user->id !== Auth::id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                            onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Delete user">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @else
                        <span style="font-size:11px;color:var(--muted);">You</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-users" style="font-size:32px;margin-bottom:10px;display:block;opacity:0.3;"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($users->hasPages())
    <div class="pagination-wrapper">
        @if ($users->onFirstPage())
            <span class="page-btn" style="opacity:0.4;cursor:default;">← Prev</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" class="page-btn">← Prev</a>
        @endif

        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page == $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="page-btn">Next →</a>
        @else
            <span class="page-btn" style="opacity:0.4;cursor:default;">Next →</span>
        @endif
    </div>
    @endif

</div>

@endsection
