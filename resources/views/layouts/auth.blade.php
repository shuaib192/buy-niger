{{-- 
    BuyNiger AI - Multi-Vendor E-Commerce Platform
    Written by Shuaibu Abdulmumin (08122598372, 07049906420)
    
    Layout: Auth - For login/register pages
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BuyNiger') - AI-Powered Marketplace</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Form -->
        <div class="auth-left">
            <div class="auth-box">
                <!-- Logo -->
                <div class="auth-logo">
                    <h1><span class="text-gradient">Buy</span>Niger</h1>
                    <p style="color: var(--secondary-500); font-size: 0.875rem; margin-top: 0.25rem;">AI-Powered Multi-Vendor Marketplace</p>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Right Side - Branding -->
        <div class="auth-right">
            <div class="auth-right-content">
                <h2>@yield('promo_title', 'Welcome to BuyNiger')</h2>
                <p>@yield('promo_text', 'Your AI-powered marketplace connecting buyers and sellers across Nigeria. Shop smart, sell smarter.')</p>
                
                <div style="margin-top: 3rem;">
                    <div style="display: flex; justify-content: center; gap: 2rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">500+</div>
                            <div style="font-size: 0.875rem; opacity: 0.8;">Active Vendors</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">10K+</div>
                            <div style="font-size: 0.875rem; opacity: 0.8;">Products</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 2rem; font-weight: 700;">50K+</div>
                            <div style="font-size: 0.875rem; opacity: 0.8;">Happy Customers</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
