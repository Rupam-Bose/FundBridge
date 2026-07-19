<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('page-title', 'Dashboard') | FundBridge</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@vite([
    'resources/css/app.css',
    'resources/css/dashboard.css',
    'resources/css/dashboard-header.css',
    'resources/css/dashboard-sidebar.css',
    'resources/css/dashboard-footer.css',
    'resources/css/notifications.css',
    'resources/css/forms.css',
    'resources/css/messages.css',
    'resources/js/fundbridge.js',
    'resources/js/dashboard.js',
])

{{-- Notification + Form + Message styles loaded via notifications.css, forms.css, messages.css --}}

@stack('styles')

</head>

<body>


<div class="dashboard-wrapper">

    @include('layouts.sidebar')

    <main class="dashboard-main">

        {{-- Top Header Bar --}}
        <header class="dashboard-header">

            <div class="dashboard-header-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>@yield('page-subtitle', 'Manage your FundBridge activities')</p>
            </div>

            <div class="header-actions">

                {{-- Notifications Bell --}}
                <div class="notif-wrapper" id="notifWrapper">
                    <button class="notification-btn" id="notifBtn" title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-count" id="notifCount" style="display:none;">0</span>
                    </button>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h4><i class="fa-solid fa-bell" style="margin-right:6px;color:var(--green);"></i>Notifications</h4>
                            <button class="notif-mark-all" id="markAllRead">Mark all read</button>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">
                                <i class="fa-regular fa-bell-slash"></i>
                                No notifications yet
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Theme Toggle --}}
                <button id="themeToggle" class="theme-btn" title="Toggle theme">
                    <i class="fa-solid fa-moon"></i>
                </button>

                {{-- Messages shortcut --}}
                <a href="{{ route('messages.index') }}" class="header-msg-link" title="Messages">
                    <i class="fa-regular fa-comment-dots"></i>
                    @php $unreadMsg = Auth::user()?->receivedMessages()->whereNull('read_at')->count() ?? 0; @endphp
                    @if ($unreadMsg > 0)
                    <span class="header-msg-badge">{{ $unreadMsg }}</span>
                    @endif
                </a>

                {{-- Profile Avatar --}}
                <img
                    src="{{ Auth::user()?->avatarUrl() }}"
                    alt="{{ Auth::user()?->name }}"
                    class="profile-image"
                    title="{{ Auth::user()?->name }}"
                >

            </div>

        </header>

        {{-- Flash Messages --}}
        @if (session('status'))
            <div class="flash-msg flash-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flash-msg flash-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- Page Content --}}
        <div class="dashboard-content">
            @yield('content')
        </div>

        @include('layouts.footer')

    </main>

</div>

{{-- Notification System Script --}}
<script>
(function () {
    const btn      = document.getElementById('notifBtn');
    const dropdown = document.getElementById('notifDropdown');
    const list     = document.getElementById('notifList');
    const countEl  = document.getElementById('notifCount');
    const markAll  = document.getElementById('markAllRead');

    if (!btn) return;

    // Toggle dropdown
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');
        if (dropdown.classList.contains('open')) fetchNotifications();
    });

    // Close on outside click
    document.addEventListener('click', () => dropdown.classList.remove('open'));
    dropdown.addEventListener('click', (e) => e.stopPropagation());

    // Mark all as read
    markAll?.addEventListener('click', async () => {
        await fetch('{{ route('notifications.markAllRead') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
        fetchNotifications();
    });

    async function fetchNotifications() {
        try {
            const res  = await fetch('{{ route('api.notifications') }}');
            const data = await res.json();

            // Update badge
            if (data.unread_count > 0) {
                countEl.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                countEl.style.display = 'flex';
            } else {
                countEl.style.display = 'none';
            }

            // Render list
            if (data.notifications.length === 0) {
                list.innerHTML = `<div class="notif-empty"><i class="fa-regular fa-bell-slash"></i>No notifications yet</div>`;
                return;
            }

            list.innerHTML = data.notifications.map(n => `
                <div class="notif-item ${n.read ? '' : 'unread'}" onclick="goNotif('${n.id}','${n.url}')">
                    <div class="notif-icon ${n.type}">
                        <i class="fa-solid ${n.icon}"></i>
                    </div>
                    <div class="notif-text">
                        <div class="notif-title">${n.title}</div>
                        <div class="notif-body">${n.body}</div>
                        <div class="notif-time">${n.time}</div>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.warn('Notifications fetch failed', e);
        }
    }

    window.goNotif = async function(id, url) {
        await fetch(`/api/notifications/${id}/read`, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });
        dropdown.classList.remove('open');
        window.location.href = url;
    };

    // Initial load + poll every 30 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
})();
</script>

@stack('scripts')

</body>

</html>