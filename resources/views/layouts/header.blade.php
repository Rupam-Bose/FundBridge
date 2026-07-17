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
    'resources/js/fundbridge.js',
    'resources/js/dashboard.js',
])

<style>
/* ── Notification Dropdown ── */
.notif-wrapper {
    position: relative;
}
.notification-btn {
    position: relative;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--text);
    font-size: 18px;
    padding: 8px;
    border-radius: 10px;
    transition: background .2s;
}
.notification-btn:hover { background: rgba(255,255,255,.06); }

.notif-count {
    position: absolute;
    top: 2px; right: 2px;
    min-width: 17px; height: 17px;
    background: var(--green);
    color: #000;
    font-size: 9px;
    font-weight: 800;
    border-radius: 50px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px;
    animation: notifPop .3s ease;
}
@keyframes notifPop { from { transform: scale(0); } to { transform: scale(1); } }

.notif-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: -12px;
    width: 340px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
    z-index: 9999;
    overflow: hidden;
    display: none;
    animation: fadeDown .2s ease;
}
@keyframes fadeDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.notif-dropdown.open { display: block; }

.notif-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.notif-header h4 { font-size: 14px; font-weight: 700; color: var(--text); margin: 0; }
.notif-mark-all {
    font-size: 11px; color: var(--green); background: transparent; border: none;
    cursor: pointer; font-weight: 600; padding: 4px 8px; border-radius: 6px;
    transition: background .2s;
}
.notif-mark-all:hover { background: rgba(0,217,156,.1); }

.notif-list { max-height: 320px; overflow-y: auto; }

.notif-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255,255,255,.04);
    text-decoration: none;
    transition: background .15s;
    cursor: pointer;
    position: relative;
}
.notif-item:hover { background: rgba(255,255,255,.04); }
.notif-item.unread { background: rgba(0,217,156,.04); }
.notif-item.unread::before {
    content: '';
    position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
    width: 5px; height: 5px; border-radius: 50%; background: var(--green);
}

.notif-icon {
    width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
}
.notif-icon.message  { background: rgba(59,130,246,.15); color:#3b82f6; }
.notif-icon.campaign { background: rgba(0,217,156,.15); color:var(--green); }
.notif-icon.info     { background: rgba(139,92,246,.15); color:#8b5cf6; }

.notif-text { flex: 1; min-width: 0; }
.notif-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-body  { font-size: 12px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-time  { font-size: 10px; color: var(--muted); margin-top: 3px; }

.notif-empty {
    padding: 32px 16px; text-align: center; color: var(--muted); font-size: 13px;
}
.notif-empty i { font-size: 24px; display: block; margin-bottom: 8px; opacity:.4; }
</style>

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
                <a href="{{ route('messages.index') }}"
                   style="position:relative;padding:8px;border-radius:10px;color:var(--text);font-size:17px;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,.06)'"
                   onmouseout="this.style.background='transparent'"
                   title="Messages">
                    <i class="fa-regular fa-comment-dots"></i>
                    @php $unreadMsg = Auth::user()?->receivedMessages()->whereNull('read_at')->count() ?? 0; @endphp
                    @if ($unreadMsg > 0)
                    <span style="position:absolute;top:2px;right:2px;width:17px;height:17px;background:var(--green);color:#000;font-size:9px;font-weight:800;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        {{ $unreadMsg }}
                    </span>
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
            <div class="flash-msg flash-success" style="margin:16px 30px 0;padding:12px 18px;border-radius:12px;background:rgba(0,217,156,0.15);border:1px solid rgba(0,217,156,0.3);color:var(--green);font-size:14px;">
                <i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>{{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flash-msg flash-error" style="margin:16px 30px 0;padding:12px 18px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;font-size:14px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i>
                {{ $errors->first() }}
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