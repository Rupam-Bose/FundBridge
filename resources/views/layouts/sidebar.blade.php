<aside class="sidebar {{ Auth::user()?->role === 'admin' ? 'admin-sidebar' : '' }}" id="mainSidebar">

    {{-- Toggle Button --}}
    <button class="sidebar-toggle" id="sidebarToggle" title="Collapse sidebar">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fa-solid fa-bridge"></i>
        </div>
        <span class="sidebar-logo-text">Fund<span>Bridge</span></span>
    </div>

    <nav class="sidebar-menu">

        @php
            $role     = Auth::user()?->role;
            $unreadMsg = Auth::user()?->receivedMessages()->whereNull('read_at')->count() ?? 0;
        @endphp

        {{-- ── FOUNDER ── --}}
        @if ($role === 'founder')

            <span class="menu-label">Main</span>

            <a href="{{ route('founder.dashboard') }}"
               class="{{ request()->routeIs('founder.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i><span>Dashboard</span>
            </a>

            <span class="menu-label">Ventures</span>

            <a href="{{ route('founder.ventures') }}"
               class="{{ request()->routeIs('founder.ventures*') ? 'active' : '' }}">
                <i class="fa-solid fa-rocket"></i><span>My Ventures</span>
            </a>

            <a href="{{ route('founder.ventures.create') }}"
               class="{{ request()->routeIs('founder.ventures.create') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-plus"></i><span>Add Venture</span>
            </a>

            <a href="{{ route('founder.campaigns') }}"
               class="{{ request()->routeIs('founder.campaigns*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i><span>Campaigns</span>
            </a>

            <span class="menu-label">Insights</span>

            <a href="{{ route('founder.analytics') }}"
               class="{{ request()->routeIs('founder.analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i><span>Analytics</span>
            </a>

            <a href="{{ route('founder.investor-activities') }}"
               class="{{ request()->routeIs('founder.investor-activities') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>Investor Activity</span>
            </a>

            <span class="menu-label">Connect</span>

            <a href="{{ route('messages.index') }}"
               class="{{ request()->routeIs('messages.*') ? 'active' : '' }}"
               style="position:relative;">
                <i class="fa-solid fa-comments"></i>
                <span>Messages</span>
                @if ($unreadMsg > 0)
                <span style="margin-left:auto;background:var(--green);color:#000;font-size:9px;font-weight:800;padding:2px 6px;border-radius:50px;">{{ $unreadMsg }}</span>
                @endif
            </a>

            <span class="menu-label">Account</span>

            <a href="{{ route('founder.profile') }}"
               class="{{ request()->routeIs('founder.profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-pen"></i><span>Profile</span>
            </a>

        {{-- ── INVESTOR ── --}}
        @elseif ($role === 'investor')

            <span class="menu-label">Main</span>

            <a href="{{ route('investor.dashboard') }}"
               class="{{ request()->routeIs('investor.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i><span>Dashboard</span>
            </a>

            <span class="menu-label">Explore</span>

            <a href="{{ route('investor.discover') }}"
               class="{{ request()->routeIs('investor.discover') ? 'active' : '' }}">
                <i class="fa-solid fa-compass"></i><span>Discover</span>
            </a>

            <a href="{{ route('investor.portfolio') }}"
               class="{{ request()->routeIs('investor.portfolio') ? 'active' : '' }}">
                <i class="fa-solid fa-briefcase"></i><span>Portfolio</span>
            </a>

            <a href="{{ route('investor.campaigns') }}"
               class="{{ request()->routeIs('investor.campaigns*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i><span>Campaigns</span>
            </a>

            <span class="menu-label">Connect</span>

            <a href="{{ route('messages.index') }}"
               class="{{ request()->routeIs('messages.*') ? 'active' : '' }}"
               style="position:relative;">
                <i class="fa-solid fa-comments"></i>
                <span>Messages</span>
                @if ($unreadMsg > 0)
                <span style="margin-left:auto;background:var(--green);color:#000;font-size:9px;font-weight:800;padding:2px 6px;border-radius:50px;">{{ $unreadMsg }}</span>
                @endif
            </a>

            <span class="menu-label">Account</span>

            <a href="{{ route('investor.profile') }}"
               class="{{ request()->routeIs('investor.profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-pen"></i><span>Profile</span>
            </a>

        {{-- ── ADMIN ── --}}
        @elseif ($role === 'admin')

            <span class="menu-label">Platform</span>

            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i><span>Dashboard</span>
            </a>

            <a href="{{ route('admin.users') }}"
               class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i><span>Users</span>
            </a>

            <a href="{{ route('admin.ventures') }}"
               class="{{ request()->routeIs('admin.ventures*') ? 'active' : '' }}">
                <i class="fa-solid fa-rocket"></i><span>Ventures</span>
            </a>

            <span class="menu-label">Analytics</span>

            <a href="{{ route('admin.reports') }}"
               class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar"></i><span>Reports</span>
            </a>

        @endif

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="{{ Auth::user()?->avatarUrl() }}" alt="{{ Auth::user()?->name }}">
            <div class="sidebar-user-info">
                <strong>{{ Auth::user()?->name }}</strong>
                <span>{{ ucfirst(Auth::user()?->role) }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>