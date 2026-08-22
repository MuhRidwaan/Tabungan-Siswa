<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Sistem Informasi Tabungan Siswa</title>
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg, #0f172a, #0d9488, #1e293b, #0f766e, #115e59);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow-x: hidden;
            color: #f8fafc;
            padding: 20px 0;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Geometric Glow Blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.5;
            animation: floatBlob 10s infinite ease-in-out alternate;
        }
        .blob-1 {
            width: 320px;
            height: 320px;
            background: #0d9488;
            top: -50px;
            left: -50px;
        }
        .blob-2 {
            width: 350px;
            height: 350px;
            background: #2dd4bf;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 30px) scale(1.1); }
        }

        /* Register Card Container */
        .register-card {
            width: 90%;
            max-width: 460px;
            padding: 40px 32px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(13, 148, 136, 0.2);
            position: relative;
            z-index: 10;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-icon-wrapper {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px;
            border-radius: 50%;
            width: 76px;
            height: 76px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .brand-subtitle {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 16px;
            color: #64748b;
            font-size: 16px;
            transition: color 0.3s ease;
            pointer-events: none;
        }
        .form-control-custom {
            width: 100%;
            padding: 13px 16px 13px 46px;
            background: rgba(30, 41, 59, 0.7);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }
        .form-control-custom:focus {
            background: rgba(30, 41, 59, 0.95);
            border-color: #0d9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.25);
        }
        .form-control-custom:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #2dd4bf;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: inherit;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(13, 148, 136, 0.6);
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert Boxes */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
        }

        /* Footer links */
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Floating Background Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="register-card">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-icon-wrapper">
                <img src="<?= base_url('dist/img/logo_sekolah_dasar.png') ?>" alt="Logo Sekolah" style="width: 56px; height: 56px; object-fit: contain;">
            </div>
            <h1 class="brand-title">Pendaftaran Akun</h1>
            <p class="brand-subtitle">Buat Akun Baru Pengelola Tabungan Siswa</p>
        </div>

        <!-- Alerts -->
        <?php if (session('errors')): ?>
            <div class="alert-box alert-error">
                <i class="fas fa-exclamation-circle text-danger mt-1"></i>
                <div>
                    <?php foreach (session('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session('message')): ?>
            <div class="alert-box alert-success">
                <i class="fas fa-check-circle text-success mt-1"></i>
                <div><?= esc(session('message')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Main Register Form -->
        <form action="<?= url_to('register') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Username Input -->
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <div class="input-wrapper">
                    <input type="text" name="username" id="username" class="form-control-custom" placeholder="Pilih username Anda" value="<?= old('username') ?>" required autocomplete="username">
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <!-- Email Input -->
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="form-control-custom" placeholder="Masukkan alamat email aktif" value="<?= old('email') ?>" required autocomplete="email">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi (Password)</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control-custom" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <!-- Password Confirm Input -->
            <div class="form-group">
                <label for="password_confirm" class="form-label">Konfirmasi Kata Sandi</label>
                <div class="input-wrapper">
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control-custom" placeholder="Ulangi kata sandi Anda" required autocomplete="new-password">
                    <i class="fas fa-shield-alt input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i>
                <span>Daftar Akun Sekarang</span>
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 13.5px; color: #94a3b8;">
            Sudah memiliki akun? <a href="<?= url_to('login') ?>" style="color: #2dd4bf; font-weight: 700; text-decoration: none;">Masuk / Login di sini <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>

        <div class="footer-text" style="margin-top: 15px;">
            Sistem Informasi Manajemen Tabungan Siswa &copy; <?= date('Y') ?>
        </div>
    </div>

</body>
</html>