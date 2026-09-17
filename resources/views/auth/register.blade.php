<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - E-Commerce</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #2563EB;
            --primary-hover: #1D4ED8;
            --primary-light: #EFF6FF;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --bg-body: #F8FAFC;
            --card-bg: #FFFFFF;
            --border-color: #E2E8F0;
            --radius-xl: 24px;
            --radius-md: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
            padding: 2.5rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .brand-logo span {
            color: var(--text-main);
        }

        .form-group {
            margin-bottom: 1.15rem;
        }
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }
        .form-input {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            outline: none;
            background: var(--bg-body);
            color: var(--text-main);
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            border-radius: var(--radius-md);
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.5rem;
        }
        .btn-submit:hover {
            background: var(--primary-hover);
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="{{ route('home') }}" class="brand-logo">
            <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="shopping-bag" style="width: 20px; height: 20px;"></i>
            </div>
            <span>E-<span style="color: var(--primary);">Commerce</span></span>
        </a>

        <h1 style="font-size: 1.45rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.4rem;">
            Daftar Akun Baru
        </h1>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.75rem;">
            Nikmati kemudahan belanja produk original bergaransi resmi.
        </p>

        @if($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #EF4444; color: #EF4444; padding: 0.75rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.25rem; font-size: 0.8125rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="nama@email.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor WhatsApp / Telepon</label>
                <input type="tel" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="081234567890">
            </div>

            <div class="form-group">
                <label class="form-label">Kata Sandi</label>
                <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi kata sandi" required>
            </div>

            <button type="submit" class="btn-submit">
                Daftar Sekarang
            </button>
        </form>

        <p style="text-align: center; font-size: 0.875rem; color: var(--text-muted); margin-top: 1.5rem;">
            Sudah punya akun? 
            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700; text-decoration: none;">
                Masuk di sini
            </a>
        </p>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
