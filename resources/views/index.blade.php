<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Krusty Krab</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/hex.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" class="logoImg">
            <h2>KRUSTY KRAB</h2>
        </div>
        </div>

        <ul id="nav-menu">
            <li><a href="#home" class="nav-link">Home</a></li>
            <li><a href="#about" class="nav-link">About</a></li>
            <li><a href="#contact" class="nav-link">Contact</a></li>
            <li><a href="#login" class="logIn-btn" id="navLoginBtn">Login</a></li>
        </ul>

        <div class="menu-toggle" id="menu-toggle">☰</div>
    </nav>

    <!-- Home Section -->

    <section id="home" class="section home" style="position: relative; overflow: hidden;">
        <!-- Bubbles container for background -->
        <div class="bubbles-container"></div>

        <style>
            /* Bubble Animation */
            .bubbles-container {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
                pointer-events: none;
                overflow: hidden;
            }

            .bubble {
                position: absolute;
                bottom: -20px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.5);
                animation: rise linear infinite;
            }

            @keyframes rise {
                0% {
                    transform: translateY(0) scale(1);
                    opacity: 0;
                }

                10% {
                    opacity: 1;
                }

                90% {
                    opacity: 1;
                }

                100% {
                    transform: translateY(-1000px) scale(1.5);
                    opacity: 0;
                }
            }

            .hero-layout {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 60px;
                width: 100%;
                position: relative;
                z-index: 2;
            }

            /* Hover interactions & Floating */
            .hero-character {
                max-width: 250px;
                text-align: center;
                animation: float 4s ease-in-out infinite;
                display: none;
                margin-top: 100px;
                cursor: pointer;
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .left-character:hover {
                transform: scale(1.1) rotate(-5deg);
                animation-play-state: paused;
            }

            .right-character:hover {
                transform: scale(1.1) rotate(5deg);
                animation-play-state: paused;
            }

            .left-character {
                animation-delay: 0s;
            }

            .right-character {
                animation-delay: 1.5s;
            }

            .hero-character img {
                width: 100%;
                filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.6));
                pointer-events: none;
            }

            .speech-bubble {
                background: #fff;
                color: #50061e;
                padding: 18px 22px;
                border-radius: 20px;
                font-weight: 700;
                font-size: 1.1rem;
                position: relative;
                margin-bottom: 25px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
                text-shadow: none;
                font-family: 'Poppins', sans-serif;
                pointer-events: none;
            }

            .logoImg {
                height: 400px;
                vertical-align: middle;
                margin-bottom: 10px;
            }

            .home h1 {
                font-size: 3rem;
                font-family: Bradley Hand, cursive;
                font-weight: 700;
                letter-spacing: 2px;
                color: #ffffff;
                text-shadow: 0 3px 8px rgba(0, 0, 0, 0.8);
                margin: 0;
            }

            .left-character .speech-bubble::after {
                content: '';
                position: absolute;
                bottom: -15px;
                right: 50px;
                border-width: 15px 15px 0 0;
                border-style: solid;
                border-color: #fff transparent transparent transparent;
            }

            .right-character .speech-bubble::after {
                content: '';
                position: absolute;
                bottom: -15px;
                left: 50px;
                border-width: 15px 0 0 15px;
                border-style: solid;
                border-color: #fff transparent transparent transparent;
            }

            @keyframes float {
                0% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-20px);
                }

                100% {
                    transform: translateY(0);
                }
            }

            @media(min-width: 1024px) {
                .hero-character {
                    display: block;
                }
            }

            /* Pulsing Button */
            .pulse-btn {
                animation: pulseGlow 2s infinite;
                display: inline-block;
            }

            @keyframes pulseGlow {
                0% {
                    box-shadow: 0 0 0 0 rgba(252, 226, 6, 0.7);
                }

                70% {
                    box-shadow: 0 0 0 15px rgba(252, 226, 6, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(252, 226, 6, 0);
                }
            }

            /* Subtitle */
            .subtitle {
                font-size: 1.3rem;
                color: #fff;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
                margin: -25px 0 20px 0;
                font-family: 'Poppins', sans-serif;
                font-style: italic;
            }

            /* ADVANCED ADVERTISEMENT SECTION (CODEARRY STYLE) */
            .product-advertisement {
                margin-top: 20px;
                padding: 25px 0 10px;
                width: 100%;
                background: rgba(5, 10, 24, 0.85);
                position: relative;
                overflow: hidden;
                border-top: 1px solid rgba(252, 226, 6, 0.15);
            }

            .product-advertisement::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(252, 226, 6, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(252, 226, 6, 0.03) 1px, transparent 1px);
                background-size: 25px 25px;
                z-index: 0;
            }

            .ad-container {
                width: 100%;
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .ad-label {
                color: #fff;
                font-size: 1rem;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 6px;
                font-weight: 900;
                background: linear-gradient(to right, #fce206, #fff, #fce206);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: shine 6s linear infinite;
                background-size: 200% auto;
            }

            @keyframes shine {
                to {
                    background-position: 200% center;
                }
            }

            .ad-track-wrapper {
                width: 100%;
                overflow: hidden;
            }

            .ad-track {
                display: flex;
                width: max-content;
                animation: scroll 35s linear infinite;
                gap: 30px;
                padding: 10px 0;
            }

            .ad-track:hover {
                animation-play-state: paused;
            }

            .ad-item {
                position: relative;
                width: 160px;
                padding: 25px 15px 15px;
                background: rgba(255, 255, 255, 0.04);
                border-radius: 18px;
                border: 1px solid rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(10px);
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
                cursor: pointer;
                overflow: visible;
                margin-top: 14px;
            }

            .ad-item:hover {
                transform: translateY(-10px);
                background: rgba(255, 255, 255, 0.08);
                border-color: rgba(252, 226, 6, 0.4);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.7), 0 0 15px rgba(252, 226, 6, 0.1);
            }

            .ad-item img {
                width: 80px;
                height: 80px;
                object-fit: contain;
                filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.6));
                transition: transform 0.4s;
            }

            .ad-item:hover img {
                transform: scale(1.1) translateY(-3px);
            }

            .ad-info h4 {
                color: #fff;
                font-size: 0.85rem;
                margin-bottom: 2px;
                font-weight: 700;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 130px;
            }

            .ad-info .ad-price {
                color: #fce206;
                font-size: 1rem;
                font-weight: 900;
            }

            .ad-badge {
                position: absolute;
                top: -10px;
                left: 50%;
                transform: translateX(-50%);
                padding: 5px 14px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 1px;
                white-space: nowrap;
                z-index: 10;
            }

            .ad-badge.rank-1 {
                background: linear-gradient(135deg, #ffd700, #ff9500);
                color: #000;
                box-shadow: 0 0 12px rgba(255, 215, 0, 0.9), 0 2px 6px rgba(0, 0, 0, 0.4);
                font-size: 0.9rem;
            }

            .ad-badge.rank-2 {
                background: linear-gradient(135deg, #e0e0e0, #a8a8a8);
                color: #000;
                box-shadow: 0 0 10px rgba(192, 192, 192, 0.7), 0 2px 6px rgba(0, 0, 0, 0.4);
            }

            .ad-badge.rank-3 {
                background: linear-gradient(135deg, #cd7f32, #8b4513);
                color: #fff;
                box-shadow: 0 0 10px rgba(205, 127, 50, 0.7), 0 2px 6px rgba(0, 0, 0, 0.4);
            }

            .ad-badge.rank-other {
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.4);
                color: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            }

            .ad-badge.rank-new {
                background: linear-gradient(135deg, #00c6ff, #0072ff);
                color: #fff;
                box-shadow: 0 0 10px rgba(0, 114, 255, 0.6), 0 2px 6px rgba(0, 0, 0, 0.4);
            }

            @keyframes scroll {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(calc(-50% - 15px));
                }
            }
        </style>

        <div class="hero-layout">
            <!-- Left Character -->
            <div class="hero-character left-character" onclick="playAudio('spongeAudio')">
                <div class="speech-bubble">“I’m ready! Come try a Krabby Patty!”</div>
                <img src="{{ asset('images/sponge.png') }}" alt="SpongeBob cooking">
                <audio id="spongeAudio" src="{{ asset('sounds/spongebob.mp3') }}"></audio>
            </div>

            <!-- Center Logo/Title -->
            <div class="text">
                <div class="title-container">
                    <img src="{{ asset('images/logo.png') }}" class="logoImg">
                    <h1>WELCOME TO <span>KRUSTY KRAB!</span></h1>
                    <p class="subtitle" style="margin-top: 5px;">Home of the world-famous Krabby Patty!</p>
                    <div style="margin-top: 30px;">
                        <a href="#login" class="orderNow-btn pulse-btn" id="orderNowBtn"
                            style="font-size: 1.2rem; padding: 12px 35px; text-decoration: none;">Order Now</a>
                    </div>
                </div>
            </div>

            <!-- Right Character -->
            <div class="hero-character right-character" onclick="playAudio('krabsAudio')">
                <div class="speech-bubble">“Money well spent!”</div>
                <img src="{{ asset('images/krabs.png') }}" alt="Mr Krabs">
                <audio id="krabsAudio" src="{{ asset('sounds/mrkrabs.mp3') }}"></audio>
            </div>
        </div>

        <script>
            // Audio player function
            function playAudio(id) {
                const audio = document.getElementById(id);
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.log('Add the related .mp3 to public/sounds/ to hear this!', e));
                }
            }

            // Bubble generator
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.querySelector('.bubbles-container');
                if (!container) return;
                const bubbleCount = 15;
                for (let i = 0; i < bubbleCount; i++) {
                    let bubble = document.createElement('div');
                    bubble.className = 'bubble';

                    let size = Math.random() * 40 + 10;
                    bubble.style.width = size + 'px';
                    bubble.style.height = size + 'px';
                    bubble.style.left = Math.random() * 100 + '%';
                    bubble.style.animationDuration = (Math.random() * 5 + 4) + 's';
                    bubble.style.animationDelay = (Math.random() * 5) + 's';

                    container.appendChild(bubble);
                }
            });
        </script>
    </section>


    <!-- About Section -->
    <section id="about" class="about">
        <h2 class="about-title">About the <span>KRUSTY KRAB</span></h2>
        <div class="about-cards">
            <div class="about-card">
                <p>
                    Welcome to <span>The Krusty Krab</span>, the most famous fast-food restaurant under the sea!
                    Known for its legendary <span>Krabby Patty</span>, we serve delicious meals and unforgettable
                    experiences.<br><br>
                    Managed by <span>Mr. Krabs</span>, with help from <span>SpongeBob SquarePants</span> and
                    <span>Squidward Tentacles</span>, every order is prepared with care.<br><br>
                    Whether you're craving a juicy Krabby Patty or crispy sea fries, The Krusty Krab is the perfect
                    place to enjoy food with friends and family.<br><br>
                    <span>"The Krusty Krab — Home of the Krabby Patty!"</span>
                </p>
            </div>
        </div>

        <div class="product-advertisement">
            <div class="ad-container">
                <h3 class="ad-label">🔥 Most Popular Picks 🔥</h3>
                <div class="ad-track-wrapper">
                    <div class="ad-track" id="adTrack">
                        <!-- Items injected by JS -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact">
        <div class="main-wrapper">
            <div class="contact-info">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <h2>Contact Us</h2>
                <hr>
                <div class="info-item">
                    <i class="bx bx-phone"></i>
                    <a href="tel:+639129298869">+63 912 929 8869</a>
                </div>
                <div class="info-item">

                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=ayawonleona@gmail.com&su=Inquiry%20to%20Krusty%20Krab"
                        target="_blank"> <i class="bx bx-envelope"></i></a>
                    <a href="facebook.com" style="font-size: 2rem;" aria-label="Facebook"><i
                            class='bx bxl-facebook-circle'></i></a>
                    <a href="instragam.com" style="font-size: 2rem;" aria-label="Instagram"><i
                            class='bx bxl-instagram-alt'></i></a>
                    <a href="tiktok.com" style="font-size: 2rem;" aria-label="TikTok"><i class='bx bxl-tiktok'></i></a>
                    <a href="twitter.com" style="font-size: 2rem;" aria-label="Twitter"><i
                            class='bx bxl-twitter'></i></a>
                </div>

            </div>
            <div class="message">
                <h3>Send us a message!</h3>
                <form id="contactForm" onsubmit="sendMessage(event, this)">
                    @csrf
                    <input type="text" name="name" placeholder="Your Name (Optional)"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                    <input type="email" name="email" placeholder="Your Email" required oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" id="submitBtn">Send Message</button>
                </form>

            </div>
        </div>
    </section>

    <!-- Login/Register/Forgot Password Section -->
    <section id="login" class="login-section">
        <div class="login-card">


            <!-- LOGIN FORM -->
            <div id="loginForm">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Login</h2>
                </div>
                <form action="{{ route('login.post') }}" method="POST" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter your email" required
                            value="{{ $errors->has('email') ? '' : old('email') }}" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" placeholder="Enter your password" required
                                id="loginPass">
                            <i class='bx bx-hide toggle-password' onclick="togglePassword('loginPass', this)"></i>
                        </div>
                    </div>
                    <a href="#" class="forgot-pass" id="forgotPassLink">Forgot password?</a>
                    <button type="submit" class="btn-logIn">Login</button>
                    <button type="button" class="btn-register" id="showRegister">
                        Don't have an account? Create one here.
                    </button>
                </form>
            </div>

            <!-- REGISTER FORM -->
            <div id="registerForm" style="display: none;">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Register</h2>
                </div>
                <form action="{{ route('register.post') }}" method="POST" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label>Full Name</label>
                        <input oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');" type="text" name="name"
                            placeholder="Enter your full name" required
                            value="{{ $errors->has('name') ? '' : old('name') }}">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter your email" required
                            value="{{ $errors->has('email') ? '' : old('email') }}" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" placeholder="Enter your password" required
                                id="regPass">
                            <i class='bx bx-hide toggle-password' onclick="togglePassword('regPass', this)"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" placeholder="Confirm password" required
                                id="regPassConfirm">
                            <i class='bx bx-hide toggle-password' onclick="togglePassword('regPassConfirm', this)"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-logIn">Sign Up</button>
                    <a href="#" id="showLogin" class="forgot-pass">Already have an account? Login here</a>
                </form>
            </div>

            <!-- FORGOT PASSWORD FORM -->
            <div id="forgotPass" style="display: none;">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Account Recovery</h2>
                </div>
                <form action="{{ route('forgotpass.post') }}" method="POST" class="login-form">
                    @csrf
                    <p class="form-note">Please enter the email used for your account.</p>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter your email" required
                            value="{{ $errors->has('email') ? '' : old('email') }}" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                    </div>
                    <button type="submit" class="btn-logIn">Send Verification Code</button>
                    <button type="button" id="backToLogin" class="btn-register">Back to Login</button>
                </form>
            </div>

            <!-- VERIFY OTP FORM (REGISTRATION) -->
            <div id="verifyOTP" style="display: none;">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Verify Email</h2>
                </div>
                <form action="{{ route('verify.otp') }}" method="POST" class="login-form">
                    @csrf
                    <p class="form-note">Please enter the 6-digit code sent to your Gmail.</p>
                    <div class="form-group">
                        <label>Verification Code</label>
                        <input type="text" name="otp" placeholder="Enter 6-digit code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required
                            style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;">
                    </div>
                    <button type="submit" class="btn-logIn">Verify Account</button>

                    <div style="margin-top: 20px; text-align: center;">
                        <button type="button" class="btn-register"
                            onclick="event.preventDefault(); document.getElementById('resendForm').submit();">Resend
                            Code</button>
                    </div>
                </form>

                <form id="resendForm" action="{{ route('resend.otp') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <div style="text-align: center; margin-top: 15px;">
                    <a href="javascript:void(0)" onclick="showForm('registerForm')" class="back-link"
                        style="color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem;">Back to
                        Register</a>
                </div>
            </div>

            <!-- VERIFY RESET OTP FORM (PASSWORD RESET) -->
            <div id="verifyResetOTP" style="display: none;">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Verify Reset Code</h2>
                </div>
                <form action="{{ route('verify.reset.otp') }}" method="POST" class="login-form">
                    @csrf
                    <p class="form-note">Please enter the 6-digit code sent to your email to reset your password.</p>
                    <div class="form-group">
                        <label>Verification Code</label>
                        <input type="text" name="otp" placeholder="Enter 6-digit code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required
                            style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;">
                    </div>
                    <button type="submit" class="btn-logIn">Verify Code</button>

                    <div style="text-align: center; margin-top: 15px;">
                        <a href="javascript:void(0)" onclick="showForm('forgotPass')" class="btn-register"
                            style="font-size: 0.95rem;">Back to Email Entry</a>
                    </div>
                </form>
            </div>

            <!-- RESET PASSWORD FORM -->
            <div id="resetPasswordForm" style="display: none;">
                <div class="login-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h2>Set New Password</h2>
                </div>
                <form action="{{ route('reset.password.post') }}" method="POST" class="login-form">
                    @csrf
                    <p class="form-note">Please enter your new password below.</p>
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" placeholder="Enter new password" required
                                id="newPass">
                            <i class='bx bx-hide toggle-password' onclick="togglePassword('newPass', this)"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                required id="newPassConfirm">
                            <i class='bx bx-hide toggle-password' onclick="togglePassword('newPassConfirm', this)"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn-logIn">Update Password</button>
                </form>
            </div>

        </div>
    </section>

    <!-- JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginForm = document.getElementById("loginForm");
            const registerForm = document.getElementById("registerForm");
            const forgotForm = document.getElementById("forgotPass");

            const activeFormTab = "{{ session('active_form', 'loginForm') }}";


            if (activeFormTab === 'registerForm') {
                showForm('registerForm');
            } else if (activeFormTab === 'forgotPass') {
                showForm('forgotPass');
            } else if (activeFormTab === 'verifyOTP') {
                showForm('verifyOTP');
            } else if (activeFormTab === 'verifyResetOTP') {
                showForm('verifyResetOTP');
            } else if (activeFormTab === 'resetPasswordForm') {
                showForm('resetPasswordForm');
            } else {
                showForm('loginForm');
            }

            document.getElementById("showRegister").addEventListener("click", () => {
                showForm('registerForm');
            });

            document.getElementById("showLogin").addEventListener("click", (e) => {
                e.preventDefault();
                showForm('loginForm');
            });

            document.getElementById("forgotPassLink").addEventListener("click", (e) => {
                e.preventDefault();
                showForm('forgotPass');
            });

            document.getElementById("backToLogin").addEventListener("click", () => {
                showForm('loginForm');
            });

            // "Order Now" button logic to show register form
            document.getElementById("orderNowBtn").addEventListener("click", () => {
                showForm('registerForm');
            });

            // "Login" nav button logic to show login form
            document.getElementById("navLoginBtn").addEventListener("click", () => {
                showForm('loginForm');
            });
        });
        function showForm(formId) {
            const forms = ['loginForm', 'registerForm', 'forgotPass', 'verifyOTP', 'verifyResetOTP', 'resetPasswordForm'];
            forms.forEach(f => {
                const el = document.getElementById(f);
                if (el) el.style.display = (f === formId) ? 'block' : 'none';
            });
        }

        const toggle = document.getElementById("menu-toggle");
        const menu = document.getElementById("nav-menu");

        toggle.onclick = () => {
            menu.classList.toggle("active");
        };

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

        async function fetchMostSold() {
            try {
                console.log('Fetching most sold items...');
                const response = await fetch("{{ route('api.most-sold') }}");
                if (!response.ok) throw new Error('Network response was not ok');

                const items = await response.json();
                console.log('Items received:', items);
                const track = document.getElementById('adTrack');
                const assetBase = "{{ asset('') }}".replace(/\/$/, '');

                if (!track) return;
                if (items.length === 0) {
                    track.closest('.product-advertisement').style.display = 'none';
                    return;
                }

                const rankLabel = ['🥇 Top 1', '🥈 Top 2', '🥉 Top 3', '# Top 4', '# Top 5'];
                const rankClass = ['rank-1', 'rank-2', 'rank-3', 'rank-other', 'rank-other'];

                const renderItems = (itemList, showRank = true) => {
                    return itemList.map((item, index) => {
                        let imgPath = item.image ? item.image : 'images/logo.png';
                        let finalImgUrl = (imgPath.startsWith('http') || imgPath.startsWith('data:'))
                            ? imgPath
                            : `${assetBase}/${imgPath.startsWith('/') ? imgPath.substring(1) : imgPath}`;

                        const hasSales = item.total_sold > 0;
                        const badgeClass = hasSales ? (rankClass[index] || 'rank-other') : 'rank-new';
                        const badgeText = hasSales ? (rankLabel[index] || `# Top ${index + 1}`) : '✨ New';

                        return `
                            <div class="ad-item">
                                <div class="ad-badge ${badgeClass}">${badgeText}</div>
                                <img src="${finalImgUrl}" alt="${item.name}" onerror="this.src='${assetBase}/images/logo.png'">
                                <div class="ad-info">
                                    <h4>${item.name}</h4>
                                    <span class="ad-price">₱${parseFloat(item.price).toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                    }).join('');
                };

                // Duplicate items for infinite loop effect (clones have no rank shown)
                track.innerHTML = renderItems(items) + renderItems(items);
                console.log('Track innerHTML updated');
            } catch (error) {
                console.error('Error fetching most sold items:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchMostSold);

        async function sendMessage(event, form) {
            event.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerText;

            submitBtn.innerText = 'Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('messages.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showModal('success', 'Message Sent!', result.message);
                    form.reset();
                } else {
                    showModal('error', 'Error', result.message || 'Something went wrong.');
                }
            } catch (error) {
                console.error('Error:', error);
                showModal('error', 'Error', 'Failed to connect to the server.');
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>

    <!-- Global Notification Modal -->
    <style>
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .custom-modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-modal-box {
            background: rgba(4, 28, 72, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1.5px solid rgba(100, 180, 255, 0.25);
            border-radius: 22px;
            width: 90%;
            max-width: 380px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            position: relative;
        }

        .custom-modal-box::before {
            content: "";
            position: absolute;
            left: 20px;
            right: 20px;
            top: 12px;
            height: 4px;
            border-radius: 99px;
            background: linear-gradient(90deg,
                    rgba(252, 226, 6, 0.1),
                    rgba(252, 226, 6, 0.85),
                    rgba(252, 226, 6, 0.1));
        }

        .custom-modal-overlay.show .custom-modal-box {
            transform: translateY(0);
        }

        .custom-modal-icon {
            font-size: 55px;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.3));
        }

        .custom-modal-icon.error {
            color: #ff5555;
        }

        .custom-modal-icon.success {
            color: #00ff66;
        }

        .custom-modal-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .custom-modal-text {
            font-size: 1.05rem;
            color: rgba(200, 230, 255, 0.9);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .custom-modal-btn {
            background: linear-gradient(135deg, #fce206 0%, #f0b800 100%);
            color: #0a1a3a;
            border: none;
            padding: 12px 30px;
            border-radius: 11px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.2s;
            width: 100%;
            box-shadow: 0 4px 15px rgba(252, 226, 6, 0.3);
        }

        .custom-modal-btn:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(252, 226, 6, 0.4);
        }
    </style>

    <div class="custom-modal-overlay" id="notificationModal">
        <div class="custom-modal-box">
            <div class="custom-modal-icon" id="modalIcon">⚠️</div>
            <div class="custom-modal-title" id="modalTitle">Notification</div>
            <div class="custom-modal-text" id="modalText">Message</div>
            <button class="custom-modal-btn"
                onclick="document.getElementById('notificationModal').classList.remove('show')">Okay</button>
        </div>
    </div>

    <script>
        function showModal(type, title, message) {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('modalIcon');
            const titleEl = document.getElementById('modalTitle');
            const textEl = document.getElementById('modalText');

            if (type === 'error') {
                icon.innerHTML = '❌';
                icon.className = 'custom-modal-icon error';
            } else {
                icon.innerHTML = '✅';
                icon.className = 'custom-modal-icon success';
            }
            titleEl.textContent = title;
            textEl.innerHTML = message;
            modal.classList.add('show');
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showModal('success', 'Success!', "{!! addslashes(session('success')) !!}");
            @elseif(session('error'))
                showModal('error', 'Access Denied', "{!! addslashes(session('error')) !!}");
            @elseif($errors->any())
                let errorMsgs = "";
                @foreach ($errors->all() as $error)
                    errorMsgs += "{!! addslashes($error) !!}<br>";
                @endforeach
                showModal('error', 'Oops!', errorMsgs);
            @endif
        });
    </script>
    <script src="{{ asset('js/trynga.js') }}"></script>

</body>

</html>