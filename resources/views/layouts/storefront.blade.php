<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Toko Online Terpercaya') - E-Commerce</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #2563EB;
            --primary-hover: #1D4ED8;
            --primary-light: #EFF6FF;
            --primary-gradient: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            
            --bg-body: #F8FAFC;
            --card-bg: #FFFFFF;
            --border-color: #E2E8F0;
            --header-bg: rgba(255, 255, 255, 0.95);
            
            --text-main: #0F172A;
            --text-muted: #64748B;
            --text-light: #94A3B8;

            --success: #10B981;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFFBEB;
            --danger: #EF4444;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EFF6FF;

            --radius-xl: 20px;
            --radius-lg: 14px;
            --radius-md: 10px;
            --radius-sm: 6px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.07), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);
        }

        html.dark {
            --primary: #3B82F6;
            --primary-hover: #60A5FA;
            --primary-light: rgba(59, 130, 246, 0.15);
            --primary-gradient: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);

            --bg-body: #020617;
            --card-bg: #0F172A;
            --border-color: #1E293B;
            --header-bg: rgba(15, 23, 42, 0.95);

            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-light: #64748B;

            --success-bg: rgba(16, 185, 129, 0.15);
            --warning-bg: rgba(245, 158, 11, 0.15);
            --danger-bg: rgba(239, 68, 68, 0.15);
            --info-bg: rgba(59, 130, 246, 0.15);

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.5);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.5);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.6);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.7);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Container helper */
        .store-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        /* Top Announcement / Sub-bar */
        .top-bar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.8125rem;
            color: var(--text-muted);
            padding: 0.4rem 0;
        }
        .top-bar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Main Navbar */
        .main-header {
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            height: 76px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        }

        /* Search Box */
        .search-container {
            flex: 1;
            max-width: 640px;
            position: relative;
        }
        .search-form {
            display: flex;
            align-items: center;
            background: var(--card-bg);
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 0.35rem 0.5rem 0.35rem 1rem;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-sm);
        }
        .search-form:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }
        .search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.9375rem;
            color: var(--text-main);
        }
        .search-input::placeholder {
            color: var(--text-light);
        }
        .search-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            outline: none;
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-btn:hover {
            background: var(--primary-hover);
        }

        /* Nav Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }
        .icon-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }
        .cart-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: #fff;
            font-size: 0.6875rem;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--card-bg);
        }

        .btn-auth-outline {
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--primary);
            color: var(--primary);
            background: transparent;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-auth-outline:hover {
            background: var(--primary-light);
        }

        .btn-auth-solid {
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-md);
            border: none;
            color: #fff;
            background: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        }
        .btn-auth-solid:hover {
            background: var(--primary-hover);
        }

        /* User Menu Dropdown */
        .user-menu-wrap {
            position: relative;
        }
        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 9999px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8125rem;
        }
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 210px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 0.5rem;
            display: none;
            z-index: 60;
        }
        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.15s ease-out;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--text-main);
            font-weight: 500;
            transition: background 0.15s;
        }
        .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }
        .dropdown-divider {
            height: 1px;
            background: var(--border-color);
            margin: 0.4rem 0;
        }

        /* Category Bar */
        .category-nav {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none;
        }
        .category-nav::-webkit-scrollbar {
            display: none;
        }
        .category-list {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 0;
        }
        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 9999px;
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .cat-pill:hover, .cat-pill.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* Slide-over Drawer (Cart) */
        .drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .drawer-panel {
            position: fixed;
            top: 0;
            right: -480px;
            width: 100%;
            max-width: 440px;
            height: 100vh;
            background: var(--card-bg);
            box-shadow: var(--shadow-xl);
            z-index: 101;
            display: flex;
            flex-direction: column;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .drawer-overlay.active .drawer-panel {
            right: 0;
        }
        .drawer-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .drawer-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-body);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        /* Auth Modal Popup */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            z-index: 110;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        .modal-card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-card {
            transform: scale(1);
        }
        .modal-tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-body);
        }
        .modal-tab-btn {
            flex: 1;
            padding: 1rem;
            text-align: center;
            background: none;
            border: none;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .modal-tab-btn.active {
            color: var(--primary);
            background: var(--card-bg);
            border-bottom-color: var(--primary);
        }

        .auth-form-group {
            margin-bottom: 1rem;
        }
        .auth-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: var(--text-main);
        }
        .auth-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--bg-body);
            color: var(--text-main);
            font-size: 0.9375rem;
            outline: none;
            transition: all 0.2s;
        }
        .auth-input:focus {
            border-color: var(--primary);
            background: var(--card-bg);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .auth-submit-btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            border: none;
            background: var(--primary-gradient);
            color: #fff;
            font-weight: 700;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 0.5rem;
        }
        .auth-submit-btn:hover {
            opacity: 0.92;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 150;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .toast {
            pointer-events: auto;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-xl);
            padding: 0.85rem 1.15rem;
            min-width: 280px;
            max-width: 380px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-size: 0.875rem;
            font-weight: 500;
        }
        .toast.show {
            transform: translateX(0);
        }
        .toast-icon {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
        }

        /* Footer */
        .main-footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            margin-top: auto;
            padding: 3.5rem 0 1.5rem 0;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 2.5rem;
            margin-bottom: 2.5rem;
        }
        @media (max-width: 900px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 600px) {
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
        .footer-title {
            font-size: 0.9375rem;
            font-weight: 700;
            margin-bottom: 1.1rem;
            color: var(--text-main);
        }
        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .footer-links a:hover {
            color: var(--primary);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Info Bar -->
    <div class="top-bar">
        <div class="store-container top-bar-inner">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span>Belanja mudah, aman & terpercaya dengan ribuan produk original</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <a href="{{ route('home') }}">Bantuan</a>
                @auth
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" style="color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                            <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i> Panel Admin
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="main-header">
        <div class="store-container header-content">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="brand-icon">
                    <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
                </div>
                <span>E-<span style="color: var(--primary);">Commerce</span></span>
            </a>

            <!-- Search Form -->
            <div class="search-container">
                <form action="{{ route('home') }}" method="GET" class="search-form">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari smartphone, laptop, pakaian, sepatu..." class="search-input" autocomplete="off">
                    <button type="submit" class="search-btn">
                        <i data-lucide="search" style="width: 16px; height: 16px;"></i>
                        <span>Cari</span>
                    </button>
                </form>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Theme Toggle Button -->
                <button type="button" class="icon-btn" id="themeToggle" title="Ganti Tema">
                    <i data-lucide="sun" id="sunIcon" style="width: 20px; height: 20px; display: none;"></i>
                    <i data-lucide="moon" id="moonIcon" style="width: 20px; height: 20px;"></i>
                </button>

                <!-- Cart Button with Live Badge -->
                <button type="button" class="icon-btn" id="openCartDrawer" title="Keranjang Belanja">
                    <i data-lucide="shopping-cart" style="width: 20px; height: 20px;"></i>
                    <span class="cart-badge" id="headerCartBadge" style="{{ ($cartCount ?? 0) > 0 ? '' : 'display: none;' }}">
                        {{ $cartCount ?? 0 }}
                    </span>
                </button>

                <!-- Auth Buttons or User Dropdown -->
                @auth
                    <div class="user-menu-wrap">
                        <button type="button" class="user-menu-btn" id="userMenuBtn">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span style="max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ auth()->user()->name }}
                            </span>
                            <i data-lucide="chevron-down" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div style="padding: 0.5rem 0.75rem; font-size: 0.75rem; color: var(--text-muted); border-bottom: 1px solid var(--border-color); margin-bottom: 0.25rem;">
                                Masuk sebagai:<br>
                                <strong style="color: var(--text-main); font-size: 0.8125rem;">{{ auth()->user()->email }}</strong>
                            </div>
                            <a href="{{ route('orders.index') }}" class="dropdown-item">
                                <i data-lucide="package" style="width: 16px; height: 16px;"></i> Pesanan Saya
                            </a>
                            <a href="{{ route('cart.index') }}" class="dropdown-item">
                                <i data-lucide="shopping-cart" style="width: 16px; height: 16px;"></i> Keranjang
                            </a>
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item" style="color: var(--primary);">
                                    <i data-lucide="shield" style="width: 16px; height: 16px;"></i> Panel Admin
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item" style="width: 100%; border: none; background: none; cursor: pointer; color: var(--danger);">
                                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <button type="button" class="btn-auth-outline" onclick="openAuthModal('login')">Masuk</button>
                    <button type="button" class="btn-auth-solid" onclick="openAuthModal('register')">Daftar</button>
                @endauth
            </div>
        </div>
    </header>

    <!-- Category Pills Quick Navigation -->
    @if(isset($categories) && $categories->isNotEmpty())
        <nav class="category-nav">
            <div class="store-container">
                <div class="category-list">
                    <a href="{{ route('home') }}" class="cat-pill {{ !request('category') ? 'active' : '' }}">
                        <i data-lucide="layout-grid" style="width: 14px; height: 14px;"></i> Semua Kategori
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('home', ['category' => $cat->slug]) }}" class="cat-pill {{ request('category') === $cat->slug ? 'active' : '' }}">
                            <span>{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>
    @endif

    <!-- Main View Content -->
    <main style="flex: 1; padding: 2rem 0;">
        <div class="store-container">
            @if(session('success'))
                <div style="background: var(--success-bg); border: 1px solid var(--success); color: var(--success); padding: 0.9rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.65rem;">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: var(--danger-bg); border: 1px solid var(--danger); color: var(--danger); padding: 0.9rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.65rem;">
                    <i data-lucide="alert-circle" style="width: 20px; height: 20px; flex-shrink: 0;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Slide-over Cart Drawer -->
    <div class="drawer-overlay" id="cartDrawerOverlay">
        <div class="drawer-panel" id="cartDrawerPanel">
            <div class="drawer-header">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="shopping-cart" style="width: 20px; height: 20px; color: var(--primary);"></i>
                    <h3 style="font-size: 1.125rem; font-weight: 700;">Keranjang Belanja</h3>
                </div>
                <button type="button" class="icon-btn" id="closeCartDrawer" style="width: 32px; height: 32px;">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            <div class="drawer-body" id="drawerCartBody">
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                    <i data-lucide="shopping-bag" style="width: 48px; height: 48px; margin: 0 auto 0.75rem auto; opacity: 0.4;"></i>
                    <p>Memuat keranjang...</p>
                </div>
            </div>
            <div class="drawer-footer" id="drawerCartFooter" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9375rem;">
                    <span style="color: var(--text-muted);">Total Belanja:</span>
                    <strong style="font-size: 1.15rem; color: var(--primary);" id="drawerSubtotal">Rp 0</strong>
                </div>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.25rem;">
                    <a href="{{ route('cart.index') }}" class="btn-auth-outline" style="flex: 1; text-align: center; text-decoration: none;">
                        Lihat Keranjang
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn-auth-solid" id="drawerCheckoutBtn" style="flex: 1; text-align: center; text-decoration: none;">
                        Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth Modal Popup (Login & Register in Slide-over/Popup) -->
    <div class="modal-overlay" id="authModalOverlay">
        <div class="modal-card">
            <div class="modal-tabs">
                <button type="button" class="modal-tab-btn active" id="tabLoginBtn" onclick="switchAuthTab('login')">Masuk</button>
                <button type="button" class="modal-tab-btn" id="tabRegisterBtn" onclick="switchAuthTab('register')">Daftar Akun Baru</button>
            </div>
            
            <div style="padding: 1.75rem;">
                <div id="authAlert" style="display: none; padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1rem; font-size: 0.875rem;"></div>

                <!-- Form Login -->
                <form id="ajaxLoginForm" style="display: block;">
                    @csrf
                    <div class="auth-form-group">
                        <label class="auth-label">Alamat Email</label>
                        <input type="email" name="email" id="loginEmail" class="auth-input" placeholder="nama@email.com" required>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-label">Kata Sandi</label>
                        <input type="password" name="password" id="loginPassword" class="auth-input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="auth-submit-btn" id="loginSubmitBtn">
                        Masuk Sekarang
                    </button>
                </form>

                <!-- Form Register -->
                <form id="ajaxRegisterForm" style="display: none;">
                    @csrf
                    <div class="auth-form-group">
                        <label class="auth-label">Nama Lengkap</label>
                        <input type="text" name="name" id="regName" class="auth-input" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-label">Alamat Email</label>
                        <input type="email" name="email" id="regEmail" class="auth-input" placeholder="budi@email.com" required>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-label">Nomor WhatsApp / HP</label>
                        <input type="tel" name="phone" id="regPhone" class="auth-input" placeholder="08123456789" required>
                    </div>
                    <div class="auth-form-group">
                        <label class="auth-label">Kata Sandi (Minimal 8 karakter)</label>
                        <input type="password" name="password" id="regPassword" class="auth-input" placeholder="••••••••" required minlength="8">
                    </div>
                    <button type="submit" class="auth-submit-btn" id="regSubmitBtn">
                        Buat Akun & Lanjut Belanja
                    </button>
                </form>
            </div>
            
            <div style="background: var(--bg-body); padding: 0.85rem 1.75rem; text-align: center; border-top: 1px solid var(--border-color); font-size: 0.8125rem; color: var(--text-muted);">
                <button type="button" onclick="closeAuthModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                    Tutup Jendela
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="store-container">
            <div class="footer-grid">
                <div>
                    <div class="brand-logo" style="margin-bottom: 1rem;">
                        <div class="brand-icon">
                            <i data-lucide="shopping-bag" style="width: 22px; height: 22px;"></i>
                        </div>
                        <span>E-<span style="color: var(--primary);">Commerce</span></span>
                    </div>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.25rem;">
                        Platform e-commerce modern dengan ribuan pilihan produk original bergaransi resmi, pengiriman cepat ke seluruh Indonesia, dan transaksi yang 100% aman.
                    </p>
                    <div style="display: flex; gap: 0.5rem;">
                        <span style="font-size: 0.75rem; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.35rem 0.65rem; border-radius: var(--radius-sm); font-weight: 600;">
                            ✓ 100% Original
                        </span>
                        <span style="font-size: 0.75rem; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.35rem 0.65rem; border-radius: var(--radius-sm); font-weight: 600;">
                            ✓ Jaminan Uang Kembali
                        </span>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">Jelajahi</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Semua Produk</a></li>
                        <li><a href="{{ route('home', ['sort' => 'newest']) }}">Produk Terbaru</a></li>
                        <li><a href="{{ route('cart.index') }}">Keranjang Belanja</a></li>
                        <li><a href="{{ route('orders.index') }}">Lacak Pesanan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">Bantuan</h4>
                    <ul class="footer-links">
                        <li><a href="#">Cara Pemesanan</a></li>
                        <li><a href="#">Ketentuan Garansi</a></li>
                        <li><a href="#">Tarif & Ekspedisi</a></li>
                        <li><a href="#">Hubungi CS</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">Layanan Pengiriman & Pembayaran</h4>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        Didukung oleh kurir terpercaya dan metode pembayaran resmi instan:
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600;">JNE</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600;">SiCepat</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600;">BCA VA</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600;">Mandiri VA</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-weight: 600;">QRIS</span>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.8125rem; color: var(--text-muted);">
                <span>&copy; {{ date('Y') }} E-Commerce. Hak Cipta Dilindungi Undang-Undang.</span>
                <span>Dibangun dengan standar performa dan keandalan tinggi.</span>
            </div>
        </div>
    </footer>

    <!-- Global Client Script -->
    <script>
        // Init Lucide icons
        lucide.createIcons();

        // Theme Switcher (Default: White / Light Mode)
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');

        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                if (sunIcon) sunIcon.style.display = 'block';
                if (moonIcon) moonIcon.style.display = 'none';
            } else {
                document.documentElement.classList.remove('dark');
                if (sunIcon) sunIcon.style.display = 'none';
                if (moonIcon) moonIcon.style.display = 'block';
            }
        }

        // Basic awal selalu white mode (light mode), kecuali user secara sengaja memilih 'dark'
        const savedTheme = localStorage.getItem('theme');
        applyTheme(savedTheme === 'dark');

        themeToggle?.addEventListener('click', () => {
            const isDark = document.documentElement.classList.contains('dark');
            applyTheme(!isDark);
            localStorage.setItem('theme', !isDark ? 'dark' : 'light');
            lucide.createIcons();
        });

        // User Dropdown
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        userMenuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown?.classList.toggle('show');
        });
        document.addEventListener('click', () => {
            userDropdown?.classList.remove('show');
        });

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast';

            const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
            const iconColor = type === 'success' ? 'var(--success)' : 'var(--danger)';

            toast.innerHTML = `
                <i data-lucide="${iconName}" class="toast-icon" style="color: ${iconColor};"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Cart Drawer Logic
        const cartDrawerOverlay = document.getElementById('cartDrawerOverlay');
        const openCartDrawerBtn = document.getElementById('openCartDrawer');
        const closeCartDrawerBtn = document.getElementById('closeCartDrawer');

        function openCartDrawer() {
            cartDrawerOverlay?.classList.add('active');
            loadCartDrawer();
        }

        function closeCartDrawer() {
            cartDrawerOverlay?.classList.remove('active');
        }

        openCartDrawerBtn?.addEventListener('click', openCartDrawer);
        closeCartDrawerBtn?.addEventListener('click', closeCartDrawer);
        cartDrawerOverlay?.addEventListener('click', (e) => {
            if (e.target === cartDrawerOverlay) closeCartDrawer();
        });

        function updateCartBadge(count) {
            const badge = document.getElementById('headerCartBadge');
            if (badge) {
                badge.innerText = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        }

        async function loadCartDrawer() {
            const body = document.getElementById('drawerCartBody');
            const footer = document.getElementById('drawerCartFooter');
            const subtotalEl = document.getElementById('drawerSubtotal');

            try {
                const res = await fetch("{{ route('cart.summary') }}", {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.success || data.cart.items.length === 0) {
                    body.innerHTML = `
                        <div style="text-align: center; padding: 4rem 1rem; color: var(--text-muted);">
                            <i data-lucide="shopping-bag" style="width: 48px; height: 48px; margin: 0 auto 0.75rem auto; opacity: 0.3;"></i>
                            <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">Keranjang Anda Kosong</h4>
                            <p style="font-size: 0.875rem;">Yuk cari produk impian Anda sekarang!</p>
                        </div>
                    `;
                    footer.style.display = 'none';
                    updateCartBadge(0);
                    lucide.createIcons();
                    return;
                }

                updateCartBadge(data.cart.total_items);
                subtotalEl.innerText = data.cart.formatted_subtotal;
                footer.style.display = 'flex';

                let itemsHtml = '';
                data.cart.items.forEach(item => {
                    itemsHtml += `
                        <div style="display: flex; gap: 0.85rem; padding-bottom: 0.85rem; border-bottom: 1px solid var(--border-color);">
                            <div style="width: 64px; height: 64px; border-radius: var(--radius-md); background: var(--bg-body); border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                ${item.image ? `<img src="${item.image}" style="width: 100%; height: 100%; object-fit: cover;">` : `<i data-lucide="image" style="width: 24px; height: 24px; opacity: 0.3;"></i>`}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    ${item.product_name}
                                </h4>
                                ${item.attributes ? `<p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.3rem;">${item.attributes}</p>` : ''}
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--primary);">
                                        Rp ${(item.price).toLocaleString('id-ID')}
                                    </span>
                                    <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                        x${item.quantity}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                });

                body.innerHTML = itemsHtml;
                lucide.createIcons();
            } catch (err) {
                console.error(err);
            }
        }

        // Global Add To Cart Function
        async function addToCart(variantId, quantity = 1, openDrawer = true) {
            try {
                const res = await fetch("{{ route('cart.add') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ variant_id: variantId, quantity: quantity })
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    updateCartBadge(data.data.total_items);
                    if (openDrawer) {
                        openCartDrawer();
                    }
                } else {
                    showToast(data.message, 'error');
                }
            } catch (err) {
                showToast('Gagal menambahkan ke keranjang belanja.', 'error');
            }
        }

        // Auth Modal Popup Logic
        const authModalOverlay = document.getElementById('authModalOverlay');
        const tabLoginBtn = document.getElementById('tabLoginBtn');
        const tabRegisterBtn = document.getElementById('tabRegisterBtn');
        const ajaxLoginForm = document.getElementById('ajaxLoginForm');
        const ajaxRegisterForm = document.getElementById('ajaxRegisterForm');
        const authAlert = document.getElementById('authAlert');

        function openAuthModal(tab = 'login') {
            switchAuthTab(tab);
            authModalOverlay?.classList.add('active');
        }

        function closeAuthModal() {
            authModalOverlay?.classList.remove('active');
            authAlert.style.display = 'none';
        }

        authModalOverlay?.addEventListener('click', (e) => {
            if (e.target === authModalOverlay) closeAuthModal();
        });

        function switchAuthTab(tab) {
            authAlert.style.display = 'none';
            if (tab === 'login') {
                tabLoginBtn.classList.add('active');
                tabRegisterBtn.classList.remove('active');
                ajaxLoginForm.style.display = 'block';
                ajaxRegisterForm.style.display = 'none';
            } else {
                tabRegisterBtn.classList.add('active');
                tabLoginBtn.classList.remove('active');
                ajaxRegisterForm.style.display = 'block';
                ajaxLoginForm.style.display = 'none';
            }
        }

        // AJAX Login Submit
        ajaxLoginForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('loginSubmitBtn');
            btn.innerText = 'Memproses...';
            btn.disabled = true;
            authAlert.style.display = 'none';

            try {
                const res = await fetch("{{ route('ajax.login') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: document.getElementById('loginEmail').value,
                        password: document.getElementById('loginPassword').value,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    authAlert.className = 'auth-alert';
                    authAlert.style.background = 'var(--success-bg)';
                    authAlert.style.color = 'var(--success)';
                    authAlert.style.display = 'block';
                    authAlert.innerText = data.message + ' Mengalihkan...';

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 600);
                } else {
                    authAlert.style.background = 'var(--danger-bg)';
                    authAlert.style.color = 'var(--danger)';
                    authAlert.style.display = 'block';
                    authAlert.innerText = data.message;
                    btn.innerText = 'Masuk Sekarang';
                    btn.disabled = false;
                }
            } catch (err) {
                authAlert.style.background = 'var(--danger-bg)';
                authAlert.style.color = 'var(--danger)';
                authAlert.style.display = 'block';
                authAlert.innerText = 'Terjadi kesalahan sistem.';
                btn.innerText = 'Masuk Sekarang';
                btn.disabled = false;
            }
        });

        // AJAX Register Submit
        ajaxRegisterForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('regSubmitBtn');
            btn.innerText = 'Mendaftar...';
            btn.disabled = true;
            authAlert.style.display = 'none';

            try {
                const res = await fetch("{{ route('ajax.register') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('regName').value,
                        email: document.getElementById('regEmail').value,
                        phone: document.getElementById('regPhone').value,
                        password: document.getElementById('regPassword').value,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    authAlert.style.background = 'var(--success-bg)';
                    authAlert.style.color = 'var(--success)';
                    authAlert.style.display = 'block';
                    authAlert.innerText = data.message + ' Mengalihkan...';

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 600);
                } else {
                    authAlert.style.background = 'var(--danger-bg)';
                    authAlert.style.color = 'var(--danger)';
                    authAlert.style.display = 'block';
                    authAlert.innerText = data.message;
                    btn.innerText = 'Buat Akun & Lanjut Belanja';
                    btn.disabled = false;
                }
            } catch (err) {
                authAlert.style.background = 'var(--danger-bg)';
                authAlert.style.color = 'var(--danger)';
                authAlert.style.display = 'block';
                authAlert.innerText = 'Terjadi kesalahan sistem.';
                btn.innerText = 'Buat Akun & Lanjut Belanja';
                btn.disabled = false;
            }
        });

        // Handle URL parameter ?auth=login or ?auth=register
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auth')) {
            openAuthModal(urlParams.get('auth'));
        }
    </script>
    @stack('scripts')
</body>
</html>
