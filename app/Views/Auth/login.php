<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Tabungan Siswa</title>
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
            background: linear-gradient(-45deg, #0f172a, #1e1b4b, #312e81, #0f172a, #0369a1);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow-x: hidden;
            color: #f8fafc;
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
            background: #6366f1;
            top: -50px;
            left: -50px;
        }
        .blob-2 {
            width: 350px;
            height: 350px;
            background: #06b6d4;
            bottom: -50px;
            right: -50px;
            animation-delay: -5s;
        }
        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 30px) scale(1.1); }
        }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 40px 36px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6),
                        0 0 0 1px rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-icon-wrapper {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5),
                        inset 0 2px 4px rgba(255, 255, 255, 0.3);
            animation: pulseGlow 3s infinite ease-in-out;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5); }
            50% { box-shadow: 0 15px 35px 0px rgba(6, 182, 212, 0.7); }
        }
        .brand-icon-wrapper i {
            font-size: 34px;
            color: #ffffff;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 6px;
        }
        .brand-subtitle {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Profile Avatar Display Header (Live Display) */
        .user-profile-preview {
            display: none;
            text-align: center;
            margin-bottom: 20px;
            animation: fadeInDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .avatar-ring {
            position: relative;
            width: 88px;
            height: 88px;
            margin: 0 auto 12px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, #6366f1, #06b6d4, #10b981);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.6);
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #1e293b;
            border: 2px solid #0f172a;
        }
        .profile-name {
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .profile-role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(99, 102, 241, 0.2);
            border: 1px solid rgba(99, 102, 241, 0.4);
            color: #818cf8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            padding: 14px 16px 14px 46px;
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
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
        }
        .form-control-custom:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #818cf8;
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s ease;
        }
        .password-toggle-btn:hover {
            color: #38bdf8;
        }

        /* Checkbox Switch */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 13px;
            color: #cbd5e1;
            user-select: none;
        }
        .custom-checkbox {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            background: rgba(30, 41, 59, 0.7);
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }
        .custom-checkbox:checked {
            background: #6366f1;
            border-color: #6366f1;
        }
        .custom-checkbox:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 11px;
            color: #ffffff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Buttons */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #0284c7 100%);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.5);
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(2, 132, 199, 0.6);
            background: linear-gradient(135deg, #4f46e5 0%, #0369a1 100%);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alerts */
        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
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

        /* Fullscreen Greeting & Motivation Overlay */
        .welcome-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeInOverlay 0.4s ease forwards;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .welcome-card {
            width: 90%;
            max-width: 480px;
            padding: 40px 32px;
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            animation: scaleUpCard 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes scaleUpCard {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .welcome-avatar-img {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6366f1;
            box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.7);
            margin-bottom: 16px;
        }
        .welcome-title {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .welcome-subtitle {
            font-size: 16px;
            font-weight: 700;
            color: #38bdf8;
            margin-bottom: 20px;
        }
        .quote-card {
            background: rgba(15, 23, 42, 0.6);
            border-left: 4px solid #38bdf8;
            border-radius: 14px;
            padding: 18px 20px;
            position: relative;
            text-align: left;
            margin-bottom: 24px;
        }
        .quote-icon {
            color: #38bdf8;
            font-size: 20px;
            margin-bottom: 8px;
            opacity: 0.8;
        }
        .quote-text {
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
            font-style: italic;
        }
        .progress-bar-glow {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #6366f1, #38bdf8, #10b981);
            border-radius: 10px;
            transition: width 2.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>

    <!-- Floating Background Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-card">
        <!-- Brand Header -->
        <div class="brand-header" id="brand-header">
            <div class="brand-icon-wrapper" id="brand-icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>

            <!-- Dynamic User Profile Preview Header (Live Display) -->
            <div class="user-profile-preview" id="user-profile-preview">
                <div class="avatar-ring">
                    <img src="" alt="Avatar" class="avatar-img" id="user-avatar-img">
                </div>
                <div class="profile-name" id="user-profile-name">Nama Pengguna</div>
                <span class="profile-role-badge" id="user-profile-role"><i class="fas fa-shield-alt mr-1"></i> Administrator</span>
            </div>

            <h1 class="brand-title" id="brand-title">Tabungan Siswa</h1>
            <p class="brand-subtitle" id="brand-subtitle">Masuk untuk Mengelola Tabungan Sekolah</p>
        </div>

        <!-- Alerts -->
        <?php if (session('errors')): ?>
            <div class="alert-box alert-error">
                <i class="fas fa-exclamation-circle text-danger"></i>
                <div>
                    <?php foreach (session('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
            <div class="alert-box alert-error">
                <i class="fas fa-exclamation-circle text-danger"></i>
                <div><?= esc(session('error')) ?></div>
            </div>
        <?php endif; ?>

        <?php if (session('message')): ?>
            <div class="alert-box alert-success">
                <i class="fas fa-check-circle text-success"></i>
                <div><?= esc(session('message')) ?></div>
            </div>
        <?php endif; ?>

        <!-- Main Form -->
        <form action="<?= url_to('login') ?>" method="post" id="form-login">
            <?= csrf_field() ?>

            <!-- Username / Email Input -->
            <div class="form-group">
                <label for="email" class="form-label">Username atau Email</label>
                <div class="input-wrapper">
                    <input type="text" name="email" id="email" class="form-control-custom" placeholder="Masukkan username atau email Anda" value="<?= old('email') ?>" required autocomplete="username">
                    <i class="fas fa-user-circle input-icon"></i>
                </div>
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi (Password)</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control-custom" placeholder="Masukkan password Anda" required autocomplete="current-password">
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle-btn" id="btn-toggle-password" title="Tampilkan Password">
                        <i class="far fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember" id="remember" class="custom-checkbox" <?php if (old('remember')) : ?> checked <?php endif ?>>
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="btn-submit" id="btn-login-submit">
                <i class="fas fa-sign-in-alt"></i>
                <span>Masuk ke Sistem</span>
            </button>
        </form>

        <div class="footer-text">
            Sistem Informasi Manajemen Tabungan Siswa &copy; <?= date('Y') ?>
        </div>
    </div>

    <!-- Fullscreen Greeting & Motivation Overlay -->
    <div class="welcome-overlay" id="welcome-overlay">
        <div class="welcome-card">
            <div>
                <img src="" alt="Avatar" class="welcome-avatar-img" id="welcome-avatar-img">
            </div>
            <h2 class="welcome-title" id="welcome-greeting-text">Selamat Datang Kembali!</h2>
            <p class="welcome-subtitle" id="welcome-user-name">Pengguna Tabungan</p>
            
            <div class="quote-card">
                <i class="fas fa-quote-left quote-icon"></i>
                <p class="quote-text" id="motivation-quote">
                    "Pendidikan adalah senjata paling mematikan untuk mengubah dunia. Selamat bertugas!"
                </p>
            </div>

            <div class="progress-bar-glow">
                <div class="progress-fill" id="progress-fill"></div>
            </div>
            <p style="font-size: 12px; color: #94a3b8; font-weight: 600;"><i class="fas fa-shield-alt text-info mr-1"></i> Membuka Dashboard Sistem Tabungan...</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const formLogin = document.getElementById('form-login');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const userProfilePreview = document.getElementById('user-profile-preview');
        const brandIconWrapper = document.getElementById('brand-icon-wrapper');
        const brandTitle = document.getElementById('brand-title');
        const brandSubtitle = document.getElementById('brand-subtitle');
        const userAvatarImg = document.getElementById('user-avatar-img');
        const userProfileName = document.getElementById('user-profile-name');
        const userProfileRole = document.getElementById('user-profile-role');
        const btnTogglePassword = document.getElementById('btn-toggle-password');
        const eyeIcon = document.getElementById('eye-icon');

        const welcomeOverlay = document.getElementById('welcome-overlay');
        const welcomeGreetingText = document.getElementById('welcome-greeting-text');
        const welcomeUserName = document.getElementById('welcome-user-name');
        const welcomeAvatarImg = document.getElementById('welcome-avatar-img');
        const motivationQuote = document.getElementById('motivation-quote');
        const progressFill = document.getElementById('progress-fill');

        const quotes = [
            '"Pendidikan adalah investasi terbaik untuk masa depan. Selamat bertugas mengelola tabungan siswa!"',
            '"Kesabaran dan ketelitian Anda dalam mengelola tabungan hari ini adalah bukti pengabdian mulia."',
            '"Setiap rupiah tabungan siswa yang Anda jaga adalah wujud komitmen membangun generasi cerdas."',
            '"Selamat datang kembali! Mari bersama wujudkan manajemen keuangan sekolah yang transparan dan akuntabel."',
            '"Menabung hari ini adalah jembatan menuju mimpi besar anak-anak bangsa di masa depan. Tetap semangat!"'
        ];

        let debounceTimer;

        function performAvatarLookup(val) {
            const inputVal = val.trim();
            if (!inputVal) {
                userProfilePreview.style.display = 'none';
                brandIconWrapper.style.display = 'flex';
                brandTitle.innerText = 'Tabungan Siswa';
                brandSubtitle.innerText = 'Masuk untuk Mengelola Tabungan Sekolah';
                return;
            }

            fetch(`<?= base_url('check-user-avatar') ?>?login=${encodeURIComponent(inputVal)}`)
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    userAvatarImg.src = data.avatar;
                    userProfileName.innerText = data.full_name;
                    userProfileRole.innerHTML = `<i class="fas fa-shield-alt mr-1"></i> ${data.role}`;
                    userProfilePreview.style.display = 'block';
                    brandIconWrapper.style.display = 'none';
                    brandTitle.innerText = 'Selamat Datang!';
                    brandSubtitle.innerText = 'Silakan masukkan password untuk melanjutkan';
                }
            })
            .catch(err => console.error('Avatar fetch error:', err));
        }

        // Live Real-Time Lookup as user types in username input
        emailInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performAvatarLookup(this.value);
            }, 350);
        });

        emailInput.addEventListener('blur', function() {
            performAvatarLookup(this.value);
        });

        // Trigger on page load if email has a value (e.g. remembered / old input)
        if (emailInput.value.trim()) {
            performAvatarLookup(emailInput.value);
        } else {
            emailInput.focus();
        }

        // Toggle Password Visibility
        btnTogglePassword.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'far fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'far fa-eye';
            }
        });

        // Form Submit: Show Welcome Greeting & Motivational Words Overlay
        let isSubmitting = false;
        formLogin.addEventListener('submit', function(e) {
            if (isSubmitting) return;

            if (!emailInput.value.trim() || !passwordInput.value) {
                return;
            }

            e.preventDefault();
            isSubmitting = true;

            const hour = new Date().getHours();
            let timeGreeting = 'Selamat Datang Kembali!';
            if (hour >= 5 && hour < 11) timeGreeting = 'Selamat Pagi! 🌅';
            else if (hour >= 11 && hour < 15) timeGreeting = 'Selamat Siang! ☀️';
            else if (hour >= 15 && hour < 18) timeGreeting = 'Selamat Sore! 🌇';
            else timeGreeting = 'Selamat Malam! 🌙';

            const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
            motivationQuote.innerText = randomQuote;

            welcomeGreetingText.innerText = timeGreeting;
            welcomeUserName.innerText = (userProfileName.innerText && userProfileName.innerText !== 'Nama Pengguna') 
                ? userProfileName.innerText 
                : emailInput.value;
                
            welcomeAvatarImg.src = userAvatarImg.src || `https://ui-avatars.com/api/?name=${encodeURIComponent(emailInput.value)}&background=6366f1&color=ffffff&size=128&bold=true`;

            welcomeOverlay.style.display = 'flex';

            setTimeout(() => {
                progressFill.style.width = '100%';
            }, 50);

            setTimeout(() => {
                formLogin.submit();
            }, 2200);
        });
    });
    </script>
</body>
</html>