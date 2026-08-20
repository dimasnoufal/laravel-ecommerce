<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #1877F2;
            --primary-hover: #1465D2;
            --primary-light: #EFF6FF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --text-subtle: #94A3B8;
            --input-bg: #F8FAFC;
            --input-border: #E2E8F0;
            --card-border: #F1F5F9;
            --radius-xl: 28px;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background-color: #E2E8F0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 2.5vh, 2rem);
            color: var(--text-main);
        }

        /* Main Window Card */
        .app-window {
            width: 100%;
            max-width: 1060px;
            height: calc(100vh - clamp(2rem, 5vh, 4rem));
            max-height: 740px;
            min-height: 560px;
            background: #FFFFFF;
            border-radius: var(--radius-xl);
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* =========================================
           LEFT PANEL: SHOWCASE & CAROUSEL
           ========================================= */
        .showcase-panel {
            background: #FAFAFC;
            border-right: 1px solid #F1F5F9;
            padding: clamp(2rem, 4vh, 3rem) clamp(2rem, 4vw, 3.5rem);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            user-select: none;
            overflow: hidden;
        }

        /* Brand Title */
        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2.5px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-logo-icon::after {
            content: '';
            width: 11px;
            height: 11px;
            border-radius: 50%;
            border: 2.5px solid var(--primary);
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #0F172A;
            text-transform: uppercase;
        }

        /* Overlapping Visual Cards Container */
        .cards-composition {
            position: relative;
            width: 100%;
            max-width: 390px;
            height: 275px;
            margin: auto auto;
        }

        /* Card 1: Current Balance (Top-Left) */
        .card-balance {
            position: absolute;
            top: 0;
            left: 15px;
            width: 175px;
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            padding: 1.15rem 1.25rem;
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(241, 245, 249, 1);
            z-index: 1;
            transition: transform 0.3s ease;
        }
        .card-balance:hover {
            transform: translateY(-2px);
        }

        .card-balance .icon-wrap {
            width: 34px;
            height: 34px;
            background: #EBF5FF;
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.65rem;
        }

        .card-balance .label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: var(--text-subtle);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }

        .card-balance .amount {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
        }

        /* Card 2: 34% Donut Chart (Floating with margin on right) */
        .card-donut {
            position: absolute;
            top: 55px;
            right: 15px;
            width: 165px;
            height: 165px;
            background: #FFFFFF;
            border-radius: var(--radius-lg);
            padding: 1rem;
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.1), 0 0 0 1px rgba(241, 245, 249, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: transform 0.3s ease;
        }
        .card-donut:hover {
            transform: translateY(-3px) scale(1.02);
        }

        .donut-ring {
            width: 95px;
            height: 95px;
            border-radius: 50%;
            background: conic-gradient(
                #1877F2 0% 45%,
                #10B981 45% 72%,
                #EF4444 72% 88%,
                #F59E0B 88% 100%
            );
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donut-center {
            width: 66px;
            height: 66px;
            background: #FFFFFF;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .donut-center .pct {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1;
        }
        .donut-center .cat {
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .cursor-pointer-mock {
            position: absolute;
            right: 4px;
            top: 24px;
            width: 16px;
            height: 16px;
            background: #0F172A;
            clip-path: polygon(0 0, 0 100%, 35% 70%, 65% 100%, 85% 85%, 55% 55%, 100% 45%);
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));
        }

        /* Card 3: New Transaction Dashed Box (Bottom-Left with clean vertical space) */
        .card-transaction {
            position: absolute;
            bottom: 0;
            left: 15px;
            width: 175px;
            background: #FFFFFF;
            border: 1.5px dashed #CBD5E1;
            border-radius: var(--radius-lg);
            padding: 0.9rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            z-index: 2;
            transition: transform 0.3s ease;
        }
        .card-transaction:hover {
            transform: translateY(-2px);
        }

        .btn-plus-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.45rem;
            box-shadow: 0 4px 10px rgba(24, 119, 242, 0.3);
        }

        .card-transaction .title {
            font-size: 0.78125rem;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 0.15rem;
        }
        .card-transaction .sub {
            font-size: 0.65rem;
            color: var(--text-subtle);
        }

        /* Slider / Carousel Bottom */
        .carousel-container {
            width: 100%;
        }

        .carousel-track {
            position: relative;
            min-height: 70px;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: all 0.4s ease;
        }

        .carousel-slide.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            position: relative;
        }

        .slide-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
            line-height: 1.3;
        }

        .slide-desc {
            font-size: 0.84375rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Symmetrical, Aesthetic Carousel Controls */
        .carousel-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.25rem;
        }

        .slider-arrow-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            color: #0F172A;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .slider-arrow-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #EFF6FF;
            transform: scale(1.06);
        }

        .dots-wrapper {
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .dot-indicator {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #CBD5E1;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot-indicator.active {
            width: 22px;
            border-radius: 10px;
            background: #0F172A;
        }

        /* =========================================
           RIGHT PANEL: CENTERING & FORM BALANCE
           ========================================= */
        .form-panel {
            background: #FFFFFF;
            padding: clamp(2rem, 4vh, 3rem) clamp(2rem, 4vw, 3.5rem);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow-y: auto;
        }

        .form-content-centered {
            width: 100%;
            max-width: 380px;
            margin: auto 0;
        }

        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
            line-height: 1.25;
        }

        .form-header p {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Alert Error Box */
        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FEE2E2;
            color: #991B1B;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.8125rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Form Inputs */
        .input-group {
            position: relative;
            margin-bottom: 0.95rem;
        }

        .input-icon-left {
            position: absolute;
            left: 1.15rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-subtle);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .custom-input {
            width: 100%;
            padding: 0.85rem 1.15rem 0.85rem 2.85rem;
            background: var(--input-bg);
            border: 1.5px solid transparent;
            border-radius: var(--radius-md);
            font-size: 0.90625rem;
            color: #0F172A;
            transition: all 0.2s ease;
            outline: none;
        }
        .custom-input.has-action {
            padding-right: 2.85rem;
        }
        .custom-input:focus {
            background: #FFFFFF;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(24, 119, 242, 0.1);
        }
        .custom-input::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }

        .toggle-password-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-subtle);
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0.25rem;
            transition: color 0.2s;
        }
        .toggle-password-btn:hover {
            color: #0F172A;
        }

        .forgot-pass-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.25rem;
            margin-bottom: 1.25rem;
        }

        .forgot-link {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: var(--primary);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 6px 16px rgba(24, 119, 242, 0.28);
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background: var(--primary-hover);
            box-shadow: 0 8px 20px rgba(24, 119, 242, 0.38);
            transform: translateY(-1px);
        }
        .btn-login:active {
            transform: translateY(0);
        }

        .btn-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        /* Divider */
        .divider-row {
            display: flex;
            align-items: center;
            margin: 1.25rem 0;
            color: var(--text-subtle);
            font-size: 0.75rem;
        }
        .divider-row::before,
        .divider-row::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }
        .divider-row span {
            padding: 0 0.85rem;
        }

        /* Social Login Buttons */
        .social-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            padding: 0.7rem 1rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: var(--radius-md);
            font-size: 0.84375rem;
            font-weight: 600;
            color: #0F172A;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .social-btn:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
        }

        .signup-prompt {
            text-align: center;
            font-size: 0.8125rem;
            color: var(--text-muted);
        }
        .signup-prompt a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }
        .signup-prompt a:hover {
            text-decoration: underline;
        }

        .copyright-text {
            text-align: center;
            font-size: 0.6875rem;
            color: var(--text-subtle);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding-top: 0.75rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Breakpoint */
        @media (max-width: 880px) {
            html, body {
                overflow-y: auto;
            }
            body {
                height: auto;
                min-height: 100vh;
            }
            .app-window {
                grid-template-columns: 1fr;
                height: auto;
                max-width: 440px;
                margin: 1.5rem 0;
            }
            .showcase-panel {
                display: none;
            }
            .form-panel {
                padding: 2.5rem 1.75rem;
            }
        }
    </style>
</head>
<body>

    <main class="app-window">
        <!-- ===== LEFT PANEL: SHOWCASE & CAROUSEL ===== -->
        <section class="showcase-panel">
            <!-- Brand Name: E-COMMERCE -->
            <div class="brand-header">
                <div class="brand-logo-icon"></div>
                <span class="brand-title">E-Commerce</span>
            </div>

            <!-- Overlapping Visual Cards Composition -->
            <div class="cards-composition">
                <!-- Card 1: Current Balance (Top-Left) -->
                <div class="card-balance">
                    <div class="icon-wrap">
                        <i data-lucide="wallet" style="width: 18px; height: 18px;"></i>
                    </div>
                    <div class="label">Current Balance</div>
                    <div class="amount">$24,359</div>
                </div>

                <!-- Card 2: 34% Donut Chart (Overlapping Right) -->
                <div class="card-donut">
                    <div class="donut-ring">
                        <div class="donut-center">
                            <span class="pct">34 %</span>
                            <span class="cat" id="chartCategoryLabel">Sales</span>
                        </div>
                        <div class="cursor-pointer-mock"></div>
                    </div>
                </div>

                <!-- Card 3: New Transaction Dashed Box (Bottom-Left) -->
                <div class="card-transaction">
                    <div class="btn-plus-icon">
                        <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                    </div>
                    <div class="title">New transaction</div>
                    <div class="sub">or upload .xls file</div>
                </div>
            </div>

            <!-- Carousel / Slider Area -->
            <div class="carousel-container">
                <div class="carousel-track">
                    <!-- Slide 1 -->
                    <div class="carousel-slide active" data-slide="0">
                        <h3 class="slide-title">Welcome back!</h3>
                        <p class="slide-desc">Start managing your store faster and better with real-time sales metrics.</p>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-slide" data-slide="1">
                        <h3 class="slide-title">Automate Inventory</h3>
                        <p class="slide-desc">Sync product stocks instantly with PostgreSQL and Redis caching layers.</p>
                    </div>

                    <!-- Slide 3 -->
                    <div class="carousel-slide" data-slide="2">
                        <h3 class="slide-title">Track Orders Live</h3>
                        <p class="slide-desc">Process shipments and monitor revenue performance across all channels.</p>
                    </div>
                </div>

                <!-- Symmetrical, Aesthetic Carousel Controls -->
                <div class="carousel-controls">
                    <button type="button" class="slider-arrow-btn" id="prevSlideBtn" title="Previous Slide">
                        <i data-lucide="chevron-left" style="width: 16px; height: 16px;"></i>
                    </button>

                    <div class="dots-wrapper">
                        <span class="dot-indicator active" data-index="0"></span>
                        <span class="dot-indicator" data-index="1"></span>
                        <span class="dot-indicator" data-index="2"></span>
                    </div>

                    <button type="button" class="slider-arrow-btn" id="nextSlideBtn" title="Next Slide">
                        <i data-lucide="chevron-right" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- ===== RIGHT PANEL: CENTERING & FORM BALANCE ===== -->
        <section class="form-panel">
            <div class="form-content-centered">
                <div class="form-header">
                    <h2>Welcome back!</h2>
                    <p>Start managing your store faster and better</p>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div class="alert-error">
                        <i data-lucide="alert-circle" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf

                    <!-- Email Input -->
                    <div class="input-group">
                        <span class="input-icon-left">
                            <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            class="custom-input" 
                            placeholder="you@example.com" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                        >
                    </div>

                    <!-- Password Input -->
                    <div class="input-group">
                        <span class="input-icon-left">
                            <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="custom-input has-action" 
                            placeholder="At least 8 characters" 
                            required
                        >
                        <button type="button" class="toggle-password-btn" id="togglePasswordBtn" title="Toggle password visibility">
                            <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="forgot-pass-row">
                        <a href="#" class="forgot-link" onclick="alert('Please contact your administrator to reset your password.'); return false;">Forgot password?</a>
                    </div>

                    <!-- Submit Login Button -->
                    <button type="submit" class="btn-login" id="submitBtn">
                        <span class="btn-spinner" id="btnSpinner"></span>
                        <span id="btnText">Login</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider-row">
                    <span>or</span>
                </div>

                <!-- Social Login Buttons -->
                <div class="social-grid">
                    <button type="button" class="social-btn" onclick="alert('Google Sign-In integration ready.');">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Google</span>
                    </button>

                    <button type="button" class="social-btn" onclick="alert('Facebook Sign-In integration ready.');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Facebook</span>
                    </button>
                </div>

                <!-- Sign Up Prompt -->
                <div class="signup-prompt">
                    Don't you have an account? <a href="#" onclick="alert('Sign up is currently restricted to invited staff.'); return false;">Sign Up</a>
                </div>
            </div>

            <!-- Footer Copyright (Cleanly positioned at bottom) -->
            <div class="copyright-text">
                &copy; {{ date('Y') }} ALL RIGHTS RESERVED
            </div>
        </section>
    </main>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Password visibility toggle
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.innerHTML = isPassword 
                ? '<i data-lucide="eye-off" style="width: 18px; height: 18px;"></i>' 
                : '<i data-lucide="eye" style="width: 18px; height: 18px;"></i>';
            lucide.createIcons();
        });

        // Form Submit Loading State
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');

        loginForm.addEventListener('submit', function () {
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.85';
            btnSpinner.style.display = 'inline-block';
            btnText.textContent = 'Logging in...';
        });

        // ==========================================
        // INTERACTIVE CAROUSEL / SLIDER LOGIC
        // ==========================================
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.dot-indicator');
        const prevBtn = document.getElementById('prevSlideBtn');
        const nextBtn = document.getElementById('nextSlideBtn');
        const chartCategoryLabel = document.getElementById('chartCategoryLabel');

        const categories = ['Sales', 'Stock', 'Growth'];
        let currentSlide = 0;

        function goToSlide(index) {
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');

            currentSlide = (index + slides.length) % slides.length;

            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');

            if (chartCategoryLabel) {
                chartCategoryLabel.textContent = categories[currentSlide] || 'Sales';
            }
        }

        prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
        nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const targetIndex = parseInt(dot.getAttribute('data-index'), 10);
                goToSlide(targetIndex);
            });
        });

        // Auto-play slider every 5 seconds
        let autoPlayTimer = setInterval(() => {
            goToSlide(currentSlide + 1);
        }, 5000);

        // Pause auto-play on hover
        const showcasePanel = document.querySelector('.showcase-panel');
        showcasePanel.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
        showcasePanel.addEventListener('mouseleave', () => {
            clearInterval(autoPlayTimer);
            autoPlayTimer = setInterval(() => {
                goToSlide(currentSlide + 1);
            }, 5000);
        });
    </script>
</body>
</html>
