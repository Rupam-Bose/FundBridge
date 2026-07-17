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

                {{-- Notifications --}}
                <button class="notification-btn" title="Notifications">
                    <i class="fa-regular fa-bell"></i>
                    @php $unread = Auth::user()?->unreadMessagesCount() ?? 0; @endphp
                    @if ($unread > 0)
                        <span class="notif-count">{{ $unread }}</span>
                    @endif
                </button>

                {{-- Theme Toggle --}}
                <button id="themeToggle" class="theme-btn" title="Toggle theme">
                    <i class="fa-solid fa-moon"></i>
                </button>

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


@stack('scripts')

</body>

</html>