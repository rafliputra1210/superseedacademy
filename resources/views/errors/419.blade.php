<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Kedaluwarsa | Super Seed Academy</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #0f172a;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 40px 30px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .icon-box {
            width: 72px;
            height: 72px;
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px auto;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .btn-login {
            background: #10b981;
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
        }
        .btn-login:hover {
            background: #059669;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-box">
            <i class="bi bi-clock-history"></i>
        </div>
        <h3 class="fw-bold text-white mb-2">Halaman Kedaluwarsa (419)</h3>
        <p class="text-slate-400 text-secondary small mb-4" style="color: #94a3b8 !important;">
            Sesi masuk Anda telah berakhir atau token halaman telah kedaluwarsa demi keamanan. Silakan klik tombol di bawah untuk kembali ke halaman login.
        </p>
        <a href="{{ route('login') }}" class="btn-login">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Kembali ke Halaman Login</span>
        </a>
    </div>
</body>
</html>
