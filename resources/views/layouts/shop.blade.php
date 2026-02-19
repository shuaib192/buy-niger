{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Layout: Shop - Public marketplace layout
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BuyNiger') - AI-First Multi-Vendor Marketplace</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" type="image/x-icon">
    
    @stack('styles')
</head>
<body class="shop-body">
    <!-- Navbar -->
    <nav class="shop-navbar">
        <div class="container">
            <div class="navbar-wrapper">
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>

                <a href="{{ route('home') }}" class="shop-logo">
                    <img src="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" alt="BuyNiger" class="logo-img">
                </a>

                <!-- Desktop Search -->
                <div class="search-bar desktop-only">
                    <form action="{{ route('catalog') }}" method="GET">
                        <input type="text" name="search" placeholder="Search products, brands, categories..." value="{{ request('search') }}">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div class="navbar-nav-links desktop-only">
                    <a href="{{ route('catalog') }}">Shop</a>
                    <a href="{{ route('stores') }}">Stores</a>
                    <a href="{{ route('about') }}">About</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>

                <div class="navbar-actions">
                    <!-- Mobile Search Button -->
                    <button class="nav-icon-btn mobile-only" id="mobileSearchBtn">
                        <i class="fas fa-search"></i>
                    </button>

                    @auth
                        <div class="user-dropdown">
                            <button class="nav-icon-btn">
                                <i class="fas fa-user-circle"></i>
                                <span class="desktop-only">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            </button>
                            <div class="dropdown-content">
                                @if(Auth::user()->role_id == 1)
                                    <a href="{{ route('superadmin.dashboard') }}">Admin Dashboard</a>
                                @elseif(Auth::user()->role_id == 3)
                                    <a href="{{ route('vendor.dashboard') }}">Vendor Dashboard</a>
                                @elseif(Auth::user()->role_id == 4)
                                    <a href="{{ route('customer.dashboard') }}">My Account</a>
                                @endif
                                <a href="{{ route('orders.index') }}">My Orders</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-icon-btn desktop-only">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm desktop-only">Join</a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="nav-icon-btn cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge" id="cart-badge">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Search Overlay -->
    <div class="mobile-search-overlay" id="mobileSearchOverlay">
        <div class="mobile-search-container">
            <form action="{{ route('catalog') }}" method="GET">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}" autofocus>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <button class="close-search" id="closeSearch"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="drawer-header">
            <a href="{{ route('home') }}" class="shop-logo">
                <img src="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" alt="BuyNiger" class="logo-img">
            </a>
            <button id="closeDrawer"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-content">
            <a href="{{ route('home') }}" class="drawer-link"><i class="fas fa-home"></i> Home</a>
            <a href="{{ route('catalog') }}" class="drawer-link"><i class="fas fa-th-large"></i> Shop All</a>
            <a href="{{ route('stores') }}" class="drawer-link"><i class="fas fa-store"></i> Stores</a>
            <a href="{{ route('about') }}" class="drawer-link"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="{{ route('contact') }}" class="drawer-link"><i class="fas fa-envelope"></i> Contact</a>
            <a href="{{ route('cart.index') }}" class="drawer-link"><i class="fas fa-shopping-cart"></i> Cart</a>
            @auth
                <a href="{{ route('vendor.dashboard') }}" class="drawer-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="drawer-link"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="drawer-link"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                <a href="{{ route('register') }}" class="drawer-link"><i class="fas fa-user-plus"></i> Create Account</a>
            @endauth
        </div>
    </div>

    <!-- Content -->
    <main class="shop-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="shop-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="shop-logo">
                        <img src="{{ asset('images/cropped-buy-niger-logo-main-3-1.png') }}" alt="BuyNiger" class="logo-img-footer">
                    </a>
                    <p>The first AI-powered multi-vendor marketplace in Nigeria. Connecting quality vendors with smart buyers.</p>
                </div>
                <div class="footer-links">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Fashion</a></li>
                        <li><a href="#">Home & Living</a></li>
                        <li><a href="#">Agric Products</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('track.order') }}">Track Order</a></li>
                        <li><a href="{{ route('catalog') }}">All Products</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Vendor</h4>
                    <ul>
                        <li><a href="{{ route('register', ['role' => 3]) }}">Become a Vendor</a></li>
                        <li><a href="#">Vendor Policy</a></li>
                        <li><a href="#">Success Stories</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} BuyNiger. Powered by P3Consulting Limited</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;"></div>

    <!-- Scripts -->
    <script>
    // Premium Toast Notification
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: ${type === 'success' ? '#10b981' : '#ef4444'};
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: slideIn 0.3s ease-out;
            min-width: 280px;
        `;
        
        toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}" style="font-size: 20px;"></i>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Define keyframe animations
    const styleSheet = document.createElement("style");
    styleSheet.innerText = `
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    `;
    document.head.appendChild(styleSheet);

    document.addEventListener('DOMContentLoaded', function() {
        // Cart count
        fetch('{{ route("cart.count") }}')
            .then(res => res.json())
            .then(data => {
                document.getElementById('cart-badge').textContent = data.count;
            })
            .catch(() => {});

        // Unify Add to Cart handler
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.add-to-cart-btn') || e.target.closest('#addToCartBtn');
            if (btn) {
                e.preventDefault();
                const productId = btn.dataset.productId;
                const qtyInput = document.getElementById('qty');
                const quantity = qtyInput ? parseInt(qtyInput.value) : 1;
                const icon = btn.querySelector('i');
                const originalClass = icon.className;
                const originalText = btn.innerHTML;
                
                icon.className = 'fas fa-spinner fa-spin';
                btn.disabled = true;
                
                fetch('{{ route("cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    if (data.success) {
                        icon.className = 'fas fa-check';
                        document.getElementById('cart-badge').textContent = data.cart_count;
                        showToast(data.message || 'Added to cart!');
                        setTimeout(() => { icon.className = originalClass; }, 2000);
                    } else {
                        icon.className = 'fas fa-times';
                        showToast(data.message || 'Error adding to cart', 'error');
                        setTimeout(() => { icon.className = originalClass; }, 2000);
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    icon.className = 'fas fa-times';
                    showToast('Network error. Please try again.', 'error');
                    setTimeout(() => { icon.className = originalClass; }, 2000);
                });
            }
        });

        // Add to Wishlist
        window.addToWishlist = function(productId) {
            fetch('{{ route("wishlist.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Added to wishlist!');
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        showToast(data.message || 'Error adding to wishlist', 'error');
                    }
                }
            })
            .catch(() => {
                showToast('Network error. Please try again.', 'error');
            });
        };

        // Search Autocomplete
        const searchInput = document.querySelector('.search-bar input');
        const searchBar = document.querySelector('.search-bar');
        
        if (searchInput) {
            const resultsDiv = document.createElement('div');
            resultsDiv.className = 'search-autocomplete-results';
            searchBar.appendChild(resultsDiv);

            searchInput.addEventListener('input', function() {
                const val = this.value;
                if (val.length < 2) {
                    resultsDiv.innerHTML = '';
                    resultsDiv.classList.remove('active');
                    return;
                }

                fetch('{{ route("search.suggestions") }}?search=' + encodeURIComponent(val))
                    .then(res => res.json())
                    .then(data => {
                        let html = '';
                        if (data.categories.length > 0) {
                            html += '<div class="search-section-title">Categories</div>';
                            data.categories.forEach(c => {
                                html += `<a href="/category/${c.slug}" class="search-result-item">
                                    <i class="fas fa-th-large"></i> ${c.name}
                                </a>`;
                            });
                        }
                        if (data.products.length > 0) {
                            html += '<div class="search-section-title">Products</div>';
                            data.products.forEach(p => {
                                html += `<a href="/product/${p.slug}" class="search-result-item">
                                    <i class="fas fa-box"></i> ${p.name}
                                </a>`;
                            });
                        }

                        if (html === '') {
                            resultsDiv.innerHTML = '';
                            resultsDiv.classList.remove('active');
                        } else {
                            resultsDiv.innerHTML = html;
                            resultsDiv.classList.add('active');
                        }
                    });
            });

            document.addEventListener('click', (e) => {
                if (!searchBar.contains(e.target)) {
                    resultsDiv.classList.remove('active');
                }
            });
        }

        // Mobile Search Toggle
        const mobileSearchBtn = document.getElementById('mobileSearchBtn');
        const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
        const closeSearch = document.getElementById('closeSearch');
        
        if (mobileSearchBtn) {
            mobileSearchBtn.addEventListener('click', () => mobileSearchOverlay.classList.add('active'));
        }
        if (closeSearch) {
            closeSearch.addEventListener('click', () => mobileSearchOverlay.classList.remove('active'));
        }

        // Mobile Drawer
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const mobileDrawerOverlay = document.getElementById('mobileDrawerOverlay');
        const closeDrawer = document.getElementById('closeDrawer');
        
        function openDrawer() {
            if (mobileDrawer) mobileDrawer.classList.add('open');
            if (mobileDrawerOverlay) mobileDrawerOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawerFn() {
            if (mobileDrawer) mobileDrawer.classList.remove('open');
            if (mobileDrawerOverlay) mobileDrawerOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openDrawer);
        if (closeDrawer) closeDrawer.addEventListener('click', closeDrawerFn);
        if (mobileDrawerOverlay) mobileDrawerOverlay.addEventListener('click', closeDrawerFn);

        // Scroll to Top
        const scrollToTopBtn = document.getElementById('scrollToTop');
        if (scrollToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 400) {
                    scrollToTopBtn.classList.add('visible');
                } else {
                    scrollToTopBtn.classList.remove('visible');
                }
            });
            scrollToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });

    // Add Autocomplete Styles
    const autocompleteStyles = document.createElement("style");
    autocompleteStyles.innerText = `
        .search-bar { position: relative; }
        .search-autocomplete-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 10px;
            display: none;
            z-index: 1000;
            overflow: hidden;
            border: 1px solid var(--secondary-100);
        }
        .search-autocomplete-results.active { display: block; }
        .search-section-title {
            padding: 10px 15px;
            background: var(--secondary-50);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--secondary-400);
            letter-spacing: 1px;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--secondary-700);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9375rem;
        }
        .search-result-item:hover { background: var(--primary-50); color: var(--primary-600); }
        .search-result-item i { color: var(--secondary-300); }
    `;
    document.head.appendChild(autocompleteStyles);
    </script>

    @stack('scripts')
</body>
</html>
