<aside class="sidebar {{ Auth::user()?->role === 'admin' ? 'admin-sidebar' : '' }}">

    {{-- Toggle Button --}}
    <button class="sidebar-toggle" title="Collapse sidebar">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fa-solid fa-bridge"></i>
        </div>
        <span class="sidebar-logo-text">Fund<span>Bridge</span></span>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-menu">

        @php $role = Auth::user()?->role; @endphp
        @php $unread = Auth::user()?->unreadMessagesCount() ?? 0; @endphp

        {{-- ── Founder Navigation ── --}}
        @if ($role === 'founder')

            <span class="menu-label">Main</span>

            <a href="{{ route('founder.dashboard') }}"
               class="{{ request()->routeIs('founder.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>

            <a href="#" class="{{ request()->routeIs('founder.ventures*') ? 'active' : '' }}">
                <i class="fa-solid fa-rocket"></i>
                <span>My Ventures</span>
            </a>

            <a href="#" class="{{ request()->routeIs('founder.campaigns*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Campaigns</span>
            </a>

            <span class="menu-label">Analytics</span>

            <a href="#" class="{{ request()->routeIs('founder.analytics*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Analytics</span>
            </a>

            <span class="menu-label">Connect</span>

            <a href="#" class="{{ request()->routeIs('founder.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-message"></i>
                <span>Messages</span>
                @if ($unread > 0)
                    <span class="sidebar-badge">{{ $unread }}</span>
                @endif
            </a>

            <span class="menu-label">Account</span>

            <a href="#" class="{{ request()->routeIs('founder.profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>

            <a href="#" class="{{ request()->routeIs('founder.settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        {{-- ── Investor Navigation ── --}}
        @elseif ($role === 'investor')

            <span class="menu-label">Main</span>

            <a href="{{ route('investor.dashboard') }}"
               class="{{ request()->routeIs('investor.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>

            <a href="#" class="{{ request()->routeIs('investor.discover*') ? 'active' : '' }}">
                <i class="fa-solid fa-compass"></i>
                <span>Discover</span>
            </a>

            <a href="#" class="{{ request()->routeIs('investor.portfolio*') ? 'active' : '' }}">
                <i class="fa-solid fa-briefcase"></i>
                <span>Portfolio</span>
            </a>

            <span class="menu-label">Connect</span>

            <a href="#" class="{{ request()->routeIs('investor.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-message"></i>
                <span>Messages</span>
                @if ($unread > 0)
                    <span class="sidebar-badge">{{ $unread }}</span>
                @endif
            </a>

            <span class="menu-label">Account</span>

            <a href="#" class="{{ request()->routeIs('investor.profile*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>

            <a href="#" class="{{ request()->routeIs('investor.settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        {{-- ── Admin Navigation ── --}}
        @elseif ($role === 'admin')

            <span class="menu-label">Platform</span>

            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.users') }}"
               class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>

            <a href="#" class="{{ request()->routeIs('admin.ventures*') ? 'active' : '' }}">
                <i class="fa-solid fa-rocket"></i>
                <span>Ventures</span>
            </a>

            <a href="#" class="{{ request()->routeIs('admin.campaigns*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Campaigns</span>
            </a>

            <span class="menu-label">Analytics</span>

            <a href="#">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Reports</span>
            </a>

            <span class="menu-label">System</span>

            <a href="#">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        @endif

    </nav>

    {{-- Sidebar Footer: User Info + Logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="{{ Auth::user()?->avatarUrl() }}" alt="{{ Auth::user()?->name }}">
            <div class="sidebar-user-info">
                <strong>{{ Auth::user()?->name }}</strong>
                <span>{{ Auth::user()?->role }}</span>
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