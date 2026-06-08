<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - BuyNiger</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" type="image/x-icon">

    @stack('styles')
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-logo">
                    <img src="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" alt="BuyNiger" class="sidebar-logo-img">
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                @yield('sidebar')
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    @php
                        $avatar = Auth::user()->avatar_url;
                        $initial = strtoupper(substr(Auth::user()->name, 0, 1));
                    @endphp
                    @if($avatar && $avatar !== url('images/default-avatar.png'))
                        <img src="{{ $avatar }}" alt="" class="sidebar-avatar">
                    @else
                        <div class="sidebar-avatar sidebar-avatar-initial">{{ $initial }}</div>
                    @endif
                    <div class="sidebar-user-info">
                        <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                        <span class="sidebar-user-role">{{ optional(Auth::user()->role)->display_name ?? 'User' }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm btn-full">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="topbar-title">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="topbar-right">
                    @auth
                    <a href="{{ route('notifications.index') }}" class="topbar-icon-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        @php
                            $unreadCount = Auth::user()->notifications()->unread()->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="topbar-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                        @endif
                    </a>
                    @endauth
                </div>
            </header>

            <div class="page-content">
                @if(session('success'))
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="dashboard-footer">
                <p>Powered by P3Consulting Limited</p>
            </footer>
        </main>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        (function() {
            var sidebar = document.getElementById('sidebar');
            var sidebarToggle = document.getElementById('sidebarToggle');
            var mobileMenuBtn = document.getElementById('mobileMenuBtn');
            var sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('collapsed');
            }

            function openMobileSidebar() {
                sidebar.classList.add('mobile-open');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openMobileSidebar);
            if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileSidebar);
        })();
    </script>
    @stack('scripts')

    @include('partials.chatbot')
</body>
</html>
