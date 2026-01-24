{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Layout: Vendor Dashboard
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Dashboard') - BuyNiger</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-200: #bfdbfe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --secondary-50: #f8fafc;
            --secondary-100: #f1f5f9;
            --secondary-200: #e2e8f0;
            --secondary-300: #cbd5e1;
            --secondary-400: #94a3b8;
            --secondary-500: #64748b;
            --secondary-600: #475569;
            --secondary-700: #334155;
            --secondary-800: #1e293b;
            --secondary-900: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--secondary-50);
            color: var(--secondary-800);
            min-height: 100vh;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: white;
            border-right: 1px solid var(--secondary-100);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--secondary-100);
        }

        .sidebar-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--secondary-900);
            text-decoration: none;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--secondary-600);
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 4px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: var(--secondary-50);
            color: var(--secondary-800);
        }

        .nav-link.active {
            background: var(--primary-50);
            color: var(--primary-600);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--secondary-100);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--secondary-50);
            border-radius: 12px;
        }

        .sidebar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-100);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-600);
            font-weight: 700;
        }

        .sidebar-user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .sidebar-user-role {
            font-size: 12px;
            color: var(--secondary-400);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 24px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--secondary-900);
        }

        .page-header p {
            color: var(--secondary-500);
            margin-top: 4px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--secondary-200);
            background: transparent;
            color: var(--secondary-700);
        }

        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-full { width: 100%; }

        /* Form Controls */
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--secondary-200);
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-400);
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px;
                background: white;
                border-bottom: 1px solid var(--secondary-100);
                margin: -24px -24px 24px;
            }

            .mobile-menu-btn {
                font-size: 20px;
                color: var(--secondary-600);
            }
        }

        @media (min-width: 769px) {
            .mobile-header { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-logo">
                    <span class="text-gradient">Buy</span>Niger
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('vendor.products') }}" class="nav-link {{ request()->routeIs('vendor.products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('vendor.orders') }}" class="nav-link {{ request()->routeIs('vendor.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Orders</span>
                </a>
                <a href="{{ route('vendor.settings') }}" class="nav-link {{ request()->routeIs('vendor.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <hr style="border: none; border-top: 1px solid var(--secondary-100); margin: 16px 0;">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="fas fa-store"></i>
                    <span>View Shop</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    <div>
                        <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                        <div class="sidebar-user-role">{{ Auth::user()->vendor->business_name ?? 'Vendor' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin-top: 12px;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-full btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="mobile-header">
                <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="sidebar-logo"><span class="text-gradient">Buy</span>Niger</span>
                <div></div>
            </div>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
