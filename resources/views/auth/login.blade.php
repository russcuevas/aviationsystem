<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAAP Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --cobalt: #004AAD;
            --cobalt-dark: #003a87;
            --cobalt-light: #e8f0fb;
            --purple: #A9B6E2;
            --mine-shaft: #333333;
            --text-muted: #6b7280;
            --bg: #f4f6fb;
            --white: #ffffff;
            --border: #e5e9f2;
            --shadow-sm: 0 1px 4px rgba(0, 74, 173, 0.06), 0 2px 12px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 28px rgba(0, 74, 173, 0.14), 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--mine-shaft);
            background:
                radial-gradient(circle at 15% 20%, rgba(169, 182, 226, 0.30), transparent 45%),
                radial-gradient(circle at 85% 10%, rgba(0, 74, 173, 0.18), transparent 40%),
                linear-gradient(155deg, #eef3fb 0%, #f7f9fd 50%, #e9f0fb 100%);
            min-height: 100vh;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
        }

        .brand-side {
            position: relative;
            padding: 48px 54px;
            color: #ffffff;
            background: linear-gradient(165deg, #004AAD 0%, #003985 48%, #0a2d67 100%);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-side::before,
        .brand-side::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-side::before {
            width: 360px;
            height: 360px;
            right: -120px;
            top: -120px;
            background: rgba(255, 255, 255, 0.08);
        }

        .brand-side::after {
            width: 280px;
            height: 280px;
            left: -100px;
            bottom: -100px;
            background: rgba(169, 182, 226, 0.20);
        }

        .brand-top,
        .brand-bottom {
            position: relative;
            z-index: 2;
        }

        .brand-badge {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            background: rgba(255, 255, 255, 0.15);
            margin-bottom: 18px;
        }

        .brand-kicker {
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 700;
        }

        .brand-title {
            margin: 8px 0 10px;
            font-size: clamp(1.8rem, 3vw, 2.6rem);
            line-height: 1.15;
            font-weight: 800;
            max-width: 520px;
        }

        .brand-sub {
            margin: 0;
            max-width: 520px;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.6;
        }

        .brand-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 0.78rem;
            font-weight: 600;
        }

        .brand-bottom {
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.75);
        }

        .form-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .login-card {
            width: 100%;
            max-width: 460px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow-md);
            padding: 30px;
        }

        .login-head h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--mine-shaft);
        }

        .login-head p {
            margin: 6px 0 0;
            color: var(--text-muted);
            font-size: 0.86rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #3b4a68;
            margin-bottom: 6px;
        }

        .input-group-text {
            border-color: var(--border);
            background: #f8faff;
            color: #5b6f95;
        }

        .form-control,
        .form-select {
            border-color: var(--border);
            font-size: 0.87rem;
            padding: 0.62rem 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--cobalt);
            box-shadow: 0 0 0 0.2rem rgba(0, 74, 173, 0.15);
        }

        .btn-login {
            border: none;
            width: 100%;
            border-radius: 10px;
            padding: 0.72rem 0.9rem;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--cobalt) 0%, #135fc8 100%);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 8px 18px rgba(0, 74, 173, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0, 74, 173, 0.30);
        }

        .helper-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2px;
            margin-bottom: 18px;
        }

        .form-check-label,
        .helper-link {
            font-size: 0.77rem;
        }

        .helper-link {
            color: var(--cobalt);
            text-decoration: none;
            font-weight: 600;
        }

        .helper-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-side {
                display: none;
            }

            .form-side {
                padding: 22px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="login-shell">
        <section class="brand-side">
            <div class="brand-top">
                <div class="brand-badge"><i class="bi bi-airplane-engines"></i></div>
                <p class="brand-kicker">NAAP Aviation TMS</p>
                <h1 class="brand-title">Centralized Aviation Training Monitoring and Compliance</h1>
                <p class="brand-sub">
                    Unified access for NAAP regulators, flying school administrators, flight instructors,
                    and student pilots.
                </p>
                <div class="brand-meta">
                    <div class="meta-pill"><i class="bi bi-shield-check"></i> Regulatory Oversight</div>
                    <div class="meta-pill"><i class="bi bi-clipboard-data"></i> Progress Monitoring</div>
                    <div class="meta-pill"><i class="bi bi-clock-history"></i> Real-time Flight Records</div>
                </div>
            </div>
            <div class="brand-bottom">
                National Aviation Academy of the Philippines | Student Progress Monitoring and Regulatory Compliance
            </div>
        </section>

        <section class="form-side">
            <div class="login-card">
                <div class="login-head mb-4">
                    <h2>Welcome back</h2>
                    <p>Sign in to continue to your dashboard.</p>
                </div>

                <form id="loginForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label" for="email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" placeholder="name@naap.ph"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" placeholder="Enter your password"
                                required>
                            <button class="input-group-text" type="button" id="togglePassword"
                                aria-label="Show password">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="helper-row">
                        <div class="form-check">

                        </div>
                        <a class="helper-link" href="#" onclick="return false;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>
                </form>

            </div>
        </section>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const emailEl = document.getElementById('email');
        const passwordEl = document.getElementById('password');
        const rememberMeEl = document.getElementById('rememberMe');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePassword.addEventListener('click', function() {
            const show = passwordEl.type === 'password';
            passwordEl.type = show ? 'text' : 'password';
            togglePasswordIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });


        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            if (!loginForm.checkValidity()) {
                loginForm.reportValidity();
                return;
            }

            const storage = rememberMeEl.checked ? localStorage : sessionStorage;
            storage.setItem('naap-email', emailEl.value);
        });
    </script>
</body>

</html>
