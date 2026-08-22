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

        /* Profile Avatar Display Header (Step 2) */
        .user-profile-preview {
            display: none;
            text-align: center;
            margin-bottom: 24px;
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
            margin-bottom: 10px;
        }
        .btn-change-user {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .btn-change-user:hover {
            color: #38bdf8;
            text-decoration: underline;
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
        .footer-text a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }
        .footer-text a:hover {
            text-decoration: underline;
        }

        /* Step transitions */
        #step-password {
            display: none;
            animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
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
            <div class="brand-icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <h1 class="brand-title">Tabungan Siswa</h1>
            <p class="brand-subtitle">Masuk untuk Mengelola Tabungan Sekolah</p>
        </div>

        <!-- Dynamic User Profile Preview Header (Step 2) -->
        <div class="user-profile-preview" id="user-profile-preview">
            <div class="avatar-ring">
                <img src="" alt="Avatar" class="avatar-img" id="user-avatar-img">
            </div>
            <div class="profile-name" id="user-profile-name">Nama Pengguna</div>
            <span class="profile-role-badge" id="user-profile-role"><i class="fas fa-shield-alt mr-1"></i> Administrator</span>
            <div>
                <button type="button" class="btn-change-user" id="btn-change-user">
                    <i class="fas fa-arrow-left"></i> Ganti Akun
                </button>
            </div>
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

            <!-- Step 1: Input Email / Username -->
            <div id="step-username">
                <div class="form-group">
                    <label for="email" class="form-label">Username atau Email</label>
                    <div class="input-wrapper">
                        <input type="text" name="email" id="email" class="form-control-custom" placeholder="Masukkan username atau email Anda" value="<?= old('email') ?>" required autocomplete="username">
                        <i class="fas fa-user-circle input-icon"></i>
                    </div>
                </div>

                <button type="button" class="btn-submit" id="btn-next-step">
                    <span>Lanjutkan</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Step 2: Password Input (Revealed after Username) -->
            <div id="step-password">
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi (Password)</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-control-custom" placeholder="Masukkan password Anda" autocomplete="current-password">
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
            </div>
        </form>

        <div class="footer-text">
            Sistem Informasi Manajemen Tabungan Siswa &copy; <?= date('Y') ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const btnNextStep = document.getElementById('btn-next-step');
        const stepUsername = document.getElementById('step-username');
        const stepPassword = document.getElementById('step-password');
        const userProfilePreview = document.getElementById('user-profile-preview');
        const brandHeader = document.getElementById('brand-header');
        const btnChangeUser = document.getElementById('btn-change-user');
        const userAvatarImg = document.getElementById('user-avatar-img');
        const userProfileName = document.getElementById('user-profile-name');
        const userProfileRole = document.getElementById('user-profile-role');
        const btnTogglePassword = document.getElementById('btn-toggle-password');
        const eyeIcon = document.getElementById('eye-icon');

        let isLookupDone = false;

        // Auto focus email on load
        emailInput.focus();

        function checkUserAvatar(inputVal) {
            if (!inputVal.trim()) return;

            btnNextStep.disabled = true;
            btnNextStep.querySelector('span').innerText = 'Memeriksa...';
            btnNextStep.querySelector('i').className = 'fas fa-spinner fa-spin';

            fetch(`<?= base_url('check-user-avatar') ?>?login=${encodeURIComponent(inputVal.trim())}`)
            .then(res => res.json())
            .then(data => {
                btnNextStep.disabled = false;
                btnNextStep.querySelector('span').innerText = 'Lanjutkan';
                btnNextStep.querySelector('i').className = 'fas fa-arrow-right';

                if (data.found) {
                    userAvatarImg.src = data.avatar;
                    userProfileName.innerText = data.full_name;
                    userProfileRole.innerHTML = `<i class="fas fa-shield-alt mr-1"></i> ${data.role}`;
                    userProfilePreview.style.display = 'block';
                    brandHeader.style.display = 'none';
                } else {
                    // Fallback avatar if user not found explicitly
                    userAvatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(inputVal)}&background=6366f1&color=ffffff&size=128&bold=true`;
                    userProfileName.innerText = inputVal;
                    userProfileRole.innerHTML = `<i class="fas fa-user mr-1"></i> Pengguna Tabungan`;
                    userProfilePreview.style.display = 'block';
                    brandHeader.style.display = 'none';
                }

                // Transition to Password Step
                stepUsername.style.display = 'none';
                stepPassword.style.display = 'block';
                passwordInput.setAttribute('required', 'required');
                passwordInput.focus();
                isLookupDone = true;
            })
            .catch(err => {
                console.error('Avatar lookup error:', err);
                btnNextStep.disabled = false;
                btnNextStep.querySelector('span').innerText = 'Lanjutkan';
                btnNextStep.querySelector('i').className = 'fas fa-arrow-right';

                // Fallback transition even if network lookup error
                stepUsername.style.display = 'none';
                stepPassword.style.display = 'block';
                passwordInput.setAttribute('required', 'required');
                passwordInput.focus();
            });
        }

        btnNextStep.addEventListener('click', function() {
            if (emailInput.value.trim()) {
                checkUserAvatar(emailInput.value);
            } else {
                emailInput.focus();
            }
        });

        // Trigger on Enter key in username input
        emailInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (emailInput.value.trim()) {
                    checkUserAvatar(emailInput.value);
                }
            }
        });

        // Change account / Switch back to Step 1
        btnChangeUser.addEventListener('click', function() {
            stepPassword.style.display = 'none';
            stepUsername.style.display = 'block';
            userProfilePreview.style.display = 'none';
            brandHeader.style.display = 'block';
            passwordInput.removeAttribute('required');
            emailInput.focus();
            emailInput.select();
            isLookupDone = false;
        });

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

        // If old input was provided (e.g. after failed login submission with validation error), auto-advance to step 2
        if (emailInput.value.trim() && '<?= session("error") || session("errors") ?>') {
            checkUserAvatar(emailInput.value);
        }
    });
    </script>
</body>
</html>