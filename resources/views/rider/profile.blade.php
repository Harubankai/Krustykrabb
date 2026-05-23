<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider - Profile</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/rider.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>



    <div class="mobile-toggle" id="mobileToggle">
        <i class='bx bx-menu'></i>
    </div>

    <div class="mobile-overlay" id="mobileOverlay"></div>



    <nav class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" class="logoImg">
            <span class="brand-title">RIDER</span>
        </div>




        <ul class="menu">
            <li><a href="{{ route('rider.dashboard') }}"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('rider.delivery') }}"><i class="bx bx-cycling"></i> Delivery</a></li>
            <li><a href="{{ route('rider.profile') }}" class="active"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <section id="profile" class="recent-orders" style="margin-top: 24px;">
            <h2>Profile</h2>

            @if(session('success'))
                <p style="color: #4caf50; font-weight: bold; margin-bottom: 20px;">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p style="color: #f44336; font-weight: bold; margin-bottom: 20px;">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <div style="color: #f44336; font-weight: bold; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="profileForm" class="profile-form" method="POST" action="{{ route('rider.profile.update') }}"
                enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 25px;">
                    <div
                        style="display: flex; align-items: center; justify-content: center; gap: 20px; flex-wrap: wrap;">
                        <div id="avatarWrapper" style="position: relative; display: inline-block; cursor: zoom-in;"
                            onclick="openAvatarZoom()">
                            @if(isset($user->profile_picture) && $user->profile_picture)
                                <img id="avatarPreview" src="{{ asset($user->profile_picture) }}" alt="Profile Picture"
                                    style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #fce206; transition: transform 0.2s, box-shadow 0.2s; display: block;">
                            @else
                                <img id="avatarPreview" src="" alt="Profile Picture"
                                    style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #fce206; display: none;">
                                <i id="avatarIcon" class='bx bx-user-circle'
                                    style="font-size: 110px; color: #fce206; display: block;"></i>
                            @endif
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.4); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; pointer-events: none; opacity: 0; transition: opacity 0.2s;"
                                id="zoomOverlay">
                                <i class='bx bx-zoom-in' style="color:#fff; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 5px; text-align: left;">
                            <span style="font-weight: bold; color: #fce206;">Profile Photo</span>
                        </div>
                    </div>
                    @error('profile_picture') <span
                    style="color: #f44336; display: block; margin-top: 10px;">{{ $message }}</span> @enderror
                </div>

                <table>
                    <tbody>
                        <tr>
                            <td>Full Name</td>
                            <td>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" disabled required
                                    oninput="this.value = this.value.replace(/[0-9]/g, '');">
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" disabled
                                    required oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');">
                            </td>
                        </tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" disabled
                                    maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>
                                <input type="text" name="address" value="{{ old('address', $user->address) }}" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>Vehicle</td>
                            <td>
                                <input type="text" name="vehicle" value="{{ old('vehicle', $user->vehicle) }}" disabled>
                            </td>
                        </tr>

                    </tbody>
                </table>

                <div style="margin-top: 20px; text-align: center; color: #888; font-style: italic;">
                    Profile information can only be updated by the Administrator.
                </div>
            </form>

            <h2 style="margin-top: 40px;">Change Password</h2>
            <form class="profile-form" method="POST" action="{{ route('rider.profile.password') }}">
                @csrf

                <table>
                    <tbody>
                        <tr>
                            <td>Current Password</td>
                            <td>
                                <div class="password-wrapper">
                                    <input type="password" name="current_password" required id="currPass">
                                    <i class='bx bx-hide toggle-password'
                                        onclick="togglePassword('currPass', this)"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>New Password</td>
                            <td>
                                <div class="password-wrapper">
                                    <input type="password" name="password" required id="newPass">
                                    <i class='bx bx-hide toggle-password' onclick="togglePassword('newPass', this)"></i>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Confirm Password</td>
                            <td>
                                <div class="password-wrapper">
                                    <input type="password" name="password_confirmation" required id="confPass">
                                    <i class='bx bx-hide toggle-password'
                                        onclick="togglePassword('confPass', this)"></i>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="profile-actions" style="margin-top: 20px;">
                    <button type="submit" class="save-btn">Change Password</button>
                </div>
            </form>
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

    // KEEP HAMBURGER VISIBLE
    if (sidebar.classList.contains("active")) {
        toggle.style.left = "294px";
    } else {
        toggle.style.left = "14px";
    }
});

overlay.addEventListener("click", () => {

    sidebar.classList.remove("active");
    overlay.classList.remove("active");

    // RETURN HAMBURGER
    toggle.style.left = "14px";
});

        const avatarPreview = document.getElementById('avatarPreview');
        const lightbox = document.getElementById('avatarLightbox');
        const lightboxImg = document.getElementById('lightboxImg');

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
    @include('partials.logout-modal')
</body>

</html>