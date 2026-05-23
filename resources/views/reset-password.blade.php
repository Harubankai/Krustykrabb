<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background:
                linear-gradient(160deg, rgba(0, 18, 60, 0.72) 0%, rgba(0, 55, 120, 0.55) 100%),
                url("{{ asset('images/bg4.jpg') }}") center / cover no-repeat;
        }

        .reset-wrap {
            min-height: 100vh;
            padding: 80px 20px 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .reset-card {
            width: 100%;
            max-width: 420px;
            background: rgba(4, 28, 72, 0.62);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1.5px solid rgba(100, 180, 255, 0.25);
            border-radius: 22px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            padding: 50px 44px;
            position: relative;
            z-index: 1;
        }

        /* Gold shimmer accent bar */
        .reset-card::before {
            content: "";
            position: absolute;
            left: 20px;
            right: 20px;
            top: 14px;
            height: 5px;
            border-radius: 99px;
            background: linear-gradient(
                90deg,
                rgba(252, 226, 6, 0.1),
                rgba(252, 226, 6, 0.85),
                rgba(252, 226, 6, 0.1)
            );
            pointer-events: none;
        }

        .reset-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 16px;
            text-align: center;
        }

        .reset-logo {
            height: 46px;
            width: auto;
            filter: drop-shadow(0 0 8px rgba(252, 226, 6, 0.5));
        }

        .reset-header h2 {
            margin: 0;
            font-size: 1.9rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .reset-note {
            margin: 0 0 20px;
            text-align: center;
            font-size: 0.92rem;
            color: rgba(200, 230, 255, 0.82);
            line-height: 1.5;
        }

        .alert {
            padding: 10px 12px;
            border-radius: 11px;
            margin: 0 0 14px;
            font-size: 0.92rem;
            line-height: 1.4;
        }

        .alert--error {
            background: rgba(180, 0, 40, 0.25);
            border: 1.5px solid rgba(255, 80, 100, 0.45);
            color: #ffb3b3;
        }

        .alert--success {
            background: rgba(0, 120, 60, 0.25);
            border: 1.5px solid rgba(80, 220, 140, 0.45);
            color: #a5f3c4;
        }

        form { margin-top: 6px; }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        label {
            font-size: 0.88rem;
            font-weight: 600;
            color: rgba(200, 230, 255, 0.92);
            letter-spacing: 0.3px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 11px;
            border: 1.5px solid rgba(100, 180, 255, 0.35);
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }

        input::placeholder {
            color: rgba(180, 210, 255, 0.45);
        }

        input:focus {
            border-color: rgba(252, 226, 6, 0.75);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 3px rgba(252, 226, 6, 0.18);
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            padding-right: 40px !important;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(200, 230, 255, 0.7);
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.2s;
            z-index: 5;
        }

        .toggle-password:hover {
            color: #fce206;
        }

        .btn {
            width: 100%;
            margin-top: 6px;
            padding: 12px 14px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #fce206 0%, #f0b800 100%);
            color: #0a1a3a;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 4px 14px rgba(252, 226, 6, 0.30);
        }

        .btn:hover {
            filter: brightness(1.08);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(252, 226, 6, 0.40);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(252, 226, 6, 0.25);
        }

        .back-link {
            display: block;
            margin-top: 14px;
            text-align: center;
            font-size: 0.88rem;
            color: rgba(252, 226, 6, 0.85);
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #fce206;
            text-decoration: underline;
        }

        @media (max-width: 420px) {
            .reset-card { padding: 38px 22px; }
            .reset-header h2 { font-size: 1.75rem; }
        }
    </style>
</head>
<body>
    <main class="reset-wrap">
        <div class="reset-card">
            <div class="reset-header">
                <img src="{{ asset('images/logo.png') }}" alt="Krusty Krab" class="reset-logo">
                <h2>Reset Password</h2>
            </div>

            @if(session('success'))
                <div class="alert alert--success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert--error">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert--error">
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="reset-note">Enter your new password below. This link will expire in 1 hour.</p>

            <form action="{{ route('reset.password.post') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>
                        <i class='bx bx-hide toggle-password' onclick="togglePassword('password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" required>
                        <i class='bx bx-hide toggle-password' onclick="togglePassword('password_confirmation', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn">Reset Password</button>
                <a class="back-link" href="{{ route('index') }}#login">Back to Login</a>
            </form>
        </div>
    </main>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bx-hide', 'bx-show');
            } else {
                input.type = 'password';
                icon.classList.replace('bx-show', 'bx-hide');
            }
        }
    </script>
</body>
</html>
