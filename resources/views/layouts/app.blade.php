{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Layout: App — Premium Dark Sidebar Design v2.0
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BuyNiger</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" type="image/x-icon">

    @stack('styles')
</head>
<body class="dashboard-body">
<div class="dashboard-wrapper">

    <!-- ═══════════════════════════════════════════════
         SIDEBAR — Dark Navy Premium
    ═══════════════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <!-- Header -->
        <div class="sidebar-header">
            <a href="{{ route('home') }}" class="sidebar-logo">
                <img src="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}"
                     alt="BuyNiger" class="sidebar-logo-img">
                <span class="sidebar-logo-text">BuyNiger</span>
                <span class="sidebar-logo-badge">AI</span>
            </a>
            <button class="sidebar-toggle" id="sidebarCollapseBtn" title="Collapse sidebar">
                <i class="fas fa-chevron-left" id="collapseIcon"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            @yield('sidebar')
        </nav>

        <!-- Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                @php
                    $avatarUrl = Auth::user()->avatar_url ?? null;
                    $initials  = strtoupper(substr(Auth::user()->name, 0, 1));
                @endphp
                <div class="sidebar-avatar">
                    @if($avatarUrl && !str_contains($avatarUrl, 'ui-avatars'))
                        <img src="{{ $avatarUrl }}" alt="{{ Auth::user()->name }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                    <span class="sidebar-user-role">
                        @if(Auth::user()->role_id == 1) Super Admin
                        @elseif(Auth::user()->role_id == 2) Admin
                        @elseif(Auth::user()->role_id == 3) Vendor
                        @else Customer
                        @endif
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ═══════════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════════ -->
    <main class="main-content" id="mainContent">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <!-- Mobile hamburger -->
                <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>

                <div>
                    <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
                    <div class="topbar-breadcrumb">
                        <span>Home</span>
                        <span class="sep">/</span>
                        <span class="current">@yield('page_title', 'Dashboard')</span>
                    </div>
                </div>
            </div>

            <div class="topbar-right">
                @auth
                <a href="{{ route('notifications.index') }}"
                   class="topbar-btn"
                   title="Notifications">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadCount = Auth::user()->notifications()->unread()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="topbar-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </a>
                @endauth

                <a href="{{ route('home') }}" class="topbar-btn" title="View Shop" target="_blank">
                    <i class="fas fa-store"></i>
                </a>

                <div class="topbar-user" title="{{ Auth::user()->name ?? '' }}">
                    <div class="topbar-user-avatar">
                        @php $av = Auth::user()->avatar_url ?? null; @endphp
                        @if($av && !str_contains($av, 'ui-avatars'))
                            <img src="{{ $av }}" alt="">
                        @else
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <span class="topbar-user-name">{{ Auth::user()->name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content fade-in-up">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-circle-xmark"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fas fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-circle-xmark"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 4px 0 0 16px; padding: 0;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="dashboard-footer">
            &copy; {{ date('Y') }} BuyNiger &mdash; Powered by P3Consulting Limited. All rights reserved.
        </footer>
    </main>
</div>

<script>
(function() {
    // ─── Sidebar collapse (desktop) ───────────────────────────────
    const sidebar      = document.getElementById('sidebar');
    const mainContent  = document.getElementById('mainContent');
    const collapseBtn  = document.getElementById('sidebarCollapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');

    let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    function applyCollapse(animate) {
        if (!animate) sidebar.style.transition = 'none';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
            if (collapseIcon) {
                collapseIcon.classList.remove('fa-chevron-left');
                collapseIcon.classList.add('fa-chevron-right');
            }
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('sidebar-collapsed');
            if (collapseIcon) {
                collapseIcon.classList.remove('fa-chevron-right');
                collapseIcon.classList.add('fa-chevron-left');
            }
        }
        if (!animate) requestAnimationFrame(() => sidebar.style.transition = '');
    }

    applyCollapse(false);

    if (collapseBtn) {
        collapseBtn.addEventListener('click', function() {
            isCollapsed = !isCollapsed;
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            applyCollapse(true);
        });
    }

    // ─── Mobile sidebar toggle ────────────────────────────────────
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const overlay   = document.getElementById('sidebarOverlay');

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (mobileBtn)  mobileBtn.addEventListener('click', openMobileSidebar);
    if (overlay)    overlay.addEventListener('click', closeMobileSidebar);

    // ─── Active nav link highlight ────────────────────────────────
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(function(link) {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
})();
</script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@stack('scripts')

@include('partials.chatbot')
</body>
</html>
