<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krusty Krab - Profile</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

    <div class="mobile-toggle" id="mobileToggle">
        <i class='bx bx-menu'></i>
    </div>

    <div class="mobile-overlay" id="mobileOverlay"></div>


    <nav class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logoImg">
            <span class="brand-title">CUSTOMER</span>
        </div>

        <ul class="menu">
            <li><a href="{{ route('customer.dashboard') }}"><i class="bx bx-store"></i> Menu</a></li>
            <li><a href="{{ route('customer.cart') }}"><i class="bx bx-cart"></i> Cart</a></li>
            <li><a href="{{ route('customer.orders') }}"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('customer.profile') }}" class="active"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="header">
            <h1>Account Settings</h1>
            <p>Customers can manage their account.</p>
        </header>

        @if (session('success'))
            <div class="alert alert--success" role="status" id="profileSuccessAlert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert--error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert--error" role="alert">
                <strong>Please fix the errors below.</strong>
            </div>
        @endif

        <section class="dashboard-grid profile-grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Edit Profile</h2>
                    <button type="button" class="action-btn action-btn--ghost" id="editProfileBtn">Edit profile</button>
                </div>
                <form method="POST" action="{{ route('customer.profile.update') }}" class="profile-form"
                    id="profileForm" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <label class="field field--full" style="margin-bottom: 25px;">
                            <div
                                style="display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap;">
                                <div id="avatarWrapper"
                                    style="position: relative; display: inline-block; cursor: zoom-in;"
                                    onclick="openAvatarZoom()">
                                    @if(isset($user->profile_picture) && $user->profile_picture)
                                        <img id="avatarPreview" src="{{ asset($user->profile_picture) }}"
                                            alt="Profile Picture"
                                            style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #fce206; transition: transform 0.2s, box-shadow 0.2s; display: block;">
                                    @else
                                        <img id="avatarPreview" src="" alt="Profile Picture"
                                            style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #fce206; transition: transform 0.2s, box-shadow 0.2s; display: none;">
                                        <i id="avatarIcon" class='bx bx-user-circle'
                                            style="font-size: 110px; color: #fce206; display: block;"></i>
                                    @endif
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.4); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; pointer-events: none; opacity: 0; transition: opacity 0.2s;"
                                        id="zoomOverlay">
                                        <i class='bx bx-zoom-in' style="color:#fff; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 5px;">
                                    <span class="field-label" style="text-align: left;">Change Photo</span>
                                    <input id="profilePicInput" type="file" name="profile_picture"
                                        class="field-input profile-input" accept="image/*" disabled
                                        style="width: auto; max-width: 250px; cursor: pointer;">
                                </div>
                            </div>
                            @error('profile_picture')<span class="field-error"
                            style="text-align: center; display: block; margin-top: 10px;">{{ $message }}</span>@enderror
                        </label>
                        <label class="field">
                            <span class="field-label">Name</span>
                            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
                                class="field-input profile-input" disabled required
                                oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">
                            @error('name')<span class="field-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="field">
                            <span class="field-label">Email</span>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                class="field-input profile-input" disabled required oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                            @error('email')<span class="field-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="field">
                            <span class="field-label">Phone number</span>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                class="field-input profile-input" disabled maxlength="12" placeholder="09... or 639..."
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); validatePhone(this, 'phoneError')">
                            <span id="phoneError" class="field-error" style="display: none;">Invalid format. Use 09...
                                (11 digits) or 639... (12 digits).</span>
                            @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="field field--full">
                            <span class="field-label">Address</span>
                            <textarea name="address" class="field-input field-textarea profile-input" rows="3"
                                style="resize: none;" disabled required>{{ old('address', $user->address ?? '') }}
                            </textarea>
                            @error('address')<span class="field-error">{{ $message }}</span>@enderror
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="action-btn" id="saveProfileBtn" disabled>Save</button>
                    </div>
                </form>
            </div>

            <div class="panel">
                <h2>Change password</h2>

                <form method="POST" action="{{ route('customer.profile.password') }}" class="profile-form">
                    @csrf

                    <div class="form-grid">
                        <label class="field field--full">
                            <span class="field-label">Current password</span>
                            <div class="password-wrapper">
                                <input type="password" name="current_password" class="field-input" required
                                    id="currPass">
                                <i class='bx bx-hide toggle-password' onclick="togglePassword('currPass', this)"></i>
                            </div>
                            @error('current_password')<span class="field-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="field">
                            <span class="field-label">New password</span>
                            <div class="password-wrapper">
                                <input type="password" name="password" class="field-input" required id="newPass">
                                <i class='bx bx-hide toggle-password' onclick="togglePassword('newPass', this)"></i>
                            </div>
                            @error('password')<span class="field-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="field">
                            <span class="field-label">Confirm password</span>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirmation" class="field-input" required
                                    id="confPass">
                                <i class='bx bx-hide toggle-password' onclick="togglePassword('confPass', this)"></i>
                            </div>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="action-btn">Save</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Avatar Zoom Lightbox -->
    <div id="avatarLightbox" onclick="closeAvatarZoom()" style="
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,0.88); align-items: center; justify-content: center;
        cursor: zoom-out; animation: fadeInLb 0.2s ease;">
        <img id="lightboxImg" src="" alt="Profile" style="
            max-width: 90vw; max-height: 88vh; border-radius: 12px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.6);
            transition: transform 0.3s ease; transform: scale(0.95);">
        <button onclick="closeAvatarZoom()" style="
            position: fixed; top: 18px; right: 22px; background: rgba(255,255,255,0.15);
            border: none; color: #fff; font-size: 2rem; cursor: pointer;
            border-radius: 50%; width: 44px; height: 44px; line-height: 44px;
            text-align: center; transition: background 0.2s;">✕</button>
    </div>
    <style>
        @keyframes fadeInLb {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #avatarWrapper:hover #avatarPreview {
            transform: scale(1.06);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.22);
        }

        #avatarWrapper:hover #zoomOverlay {
            opacity: 1 !important;
        }
    </style>

    <script>

        const toggle = document.getElementById("mobileToggle");
        const sidebar = document.querySelector(".sidebar");
        const overlay = document.getElementById("mobileOverlay");

        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("active");
            overlay.classList.toggle("active");
        });

        overlay.addEventListener("click", () => {
            sidebar.classList.remove("active");
            overlay.classList.remove("active");
        });


        const editBtn = document.getElementById('editProfileBtn');
        const profileForm = document.getElementById('profileForm');
        const saveBtn = document.getElementById('saveProfileBtn');
        const inputs = document.querySelectorAll('.profile-input');
        const picInput = document.getElementById('profilePicInput');
        const avatarPreview = document.getElementById('avatarPreview');
        const avatarIcon = document.getElementById('avatarIcon');
        const lightbox = document.getElementById('avatarLightbox');
        const lightboxImg = document.getElementById('lightboxImg');

        let editing = false;

        // Live preview when a new image is selected
        picInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                avatarPreview.src = e.target.result;
                avatarPreview.style.display = 'block';
                if (avatarIcon) avatarIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        // Zoom lightbox
        function openAvatarZoom() {
            const src = avatarPreview.style.display !== 'none' ? avatarPreview.src : null;
            if (!src) return;
            lightboxImg.src = src;
            lightbox.style.display = 'flex';
            setTimeout(() => { lightboxImg.style.transform = 'scale(1)'; }, 10);
        }

        function closeAvatarZoom() {
            lightboxImg.style.transform = 'scale(0.95)';
            setTimeout(() => { lightbox.style.display = 'none'; }, 200);
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAvatarZoom(); });

        // Edit toggle
        function toggleEdit() {
            editing = !editing;
            inputs.forEach(input => { input.disabled = !editing; });
            saveBtn.disabled = !editing;
            editBtn.textContent = editing ? 'Cancel' : 'Edit Profile';
            if (editing) { inputs[0].focus(); } else { profileForm.reset(); }
        }

        editBtn.addEventListener('click', toggleEdit);

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

        function validatePhone(input, errorId) {
            const err = document.getElementById(errorId);
            if (isValidPhone(input.value)) {
                err.style.display = 'none';
                input.style.borderColor = '';
            } else {
                err.style.display = 'block';
                input.style.borderColor = '#ff4d4d';
            }
        }

        function isValidPhone(val) {
            if (!val) return true; // Allow empty if not required
            if (val.startsWith('09')) return val.length === 11;
            if (val.startsWith('639')) return val.length === 12;
            return false;
        }

        profileForm.addEventListener('submit', (e) => {
            const phoneVal = document.getElementById('phone').value;
            if (!isValidPhone(phoneVal)) {
                e.preventDefault();
                validatePhone(document.getElementById('phone'), 'phoneError');
            }
        });
    </script>
    @include('partials.logout-modal')
</body>

</html>