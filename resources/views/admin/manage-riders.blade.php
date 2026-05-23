<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Riders</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .rider-img-td {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fce206;
        }

        .view-details-btn {
            background: #fce206;
            color: #611909;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
            display: inline-block;
        }

        .view-details-btn:hover {
            background: #e6cf05;
        }

        #riderSearch,
        #statusFilter {
            border: 2px solid #fce206;
            background: #fffdf0;
            color: #611909;
        }

        #riderSearch::placeholder {
            color: #8b4513;
            opacity: 0.7;
        }

        .form-row label {
            color: #fce206;
            font-weight: 800;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 1.1rem;
            margin-bottom: 8px;
            display: block;
        }

        .modal .form-row label {
            color: #611909 !important;
            text-shadow: none;
        }

        /* MODAL POLISH */
        .modal {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            border: 3px solid #fce206;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
        }

        .modal h3 {
            flex-shrink: 0;
            background: #611909;
            color: #fce206;
            padding: 20px;
            margin: 0;
            text-align: center;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid #fce206;
        }

        .modal-body {
            padding: 25px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-body p {
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .modal-body strong {
            color: #611909;
            width: 140px;
            display: inline-block;
        }

        .form-row input,
        .form-row select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-row input:focus,
        .form-row select:focus {
            border-color: #fce206;
            box-shadow: 0 0 8px rgba(252, 226, 6, 0.4);
            outline: none;
        }

        .modal-actions {
            padding: 20px;
            background: #f9f9f9;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #eee;
        }

        .btn-primary {
            background: #fce206 !important;
            color: #611909 !important;
            font-weight: 800 !important;
            border: none !important;
            padding: 12px 25px !important;
            border-radius: 8px !important;
        }

        .btn-secondary {
            background: #eee !important;
            color: #666 !important;
            border: none !important;
            padding: 12px 25px !important;
            border-radius: 8px !important;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .btn-secondary:hover {
            background: #e0e0e0 !important;
        }
    </style>
</head>

<body>


    <div class="mobile-toggle" id="mobileToggle">
        <i class='bx bx-menu'></i>
    </div>

    <div class="mobile-overlay" id="mobileOverlay"></div>


    <nav class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logoImg">
            <span class="brand-title">ADMIN</span>
        </div>
        <ul class="menu">
            <li><a href="{{ route('admin.dashboard') }}"><i class='bx bx-trending-up'></i> Dashboard </a></li>
            <li><a href="{{ route('admin.orders') }}"><i class='bx bx-basket'></i> Orders</a></li>
            <li><a href="{{ route('admin.manage-riders') }}" class="active"><i class='bx bx-group'></i> Manage Riders
                </a></li>
            <li><a href="{{ route('admin.manage-customers') }}"><i class='bx bx-user-circle'></i> Manage Customers </a>
            </li>
            <li><a href="{{ route('admin.modify-menu') }}"><i class="bx bx-edit"></i> Modify Menu </a></li>
            <li><a href="{{ route('admin.messages') }}"><i class='bx bx-envelope'></i> Messages </a></li>
        </ul>
        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="header">
            <div class="header-row">
                <div class="header-text">
                    <h1>Manage Riders</h1>
                    <p>Quick view of active riders and their current availability.</p>
                </div>
                <button class="btn btn-primary" id="addRiderBtn">Add Rider</button>
            </div>
        </header>

        <section class="recent-orders" style="margin-top: 10px;">
            <h2>Search and Filter</h2>
            <div class="filters-row">
                <div class="form-row">
                    <label for="riderSearch">Search Rider</label>
                    <input type="text" id="riderSearch" placeholder="Search by name, ID, or phone">
                </div>
                <div class="form-row">
                    <label for="statusFilter">Status Filter</label>
                    <select id="statusFilter">
                        <option value="All">All</option>
                        <option value="Online">Online</option>
                        <option value="On Delivery">On Delivery</option>
                        <option value="Offline">Offline</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="recent-orders" style="margin-top: 30px;">
            <h2>Rider List</h2>
            <table>
                <thead>
                    <tr>
                        <th style="text-align: center;">Profile</th>
                        <th>Name</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Phone</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="ridersTableBody">
                    <!-- Riders dynamically loaded via JS -->
                </tbody>
            </table>
            <div class="pagination-container" id="paginationContainer"></div>
        </section>
    </main>

    <!-- Details Modal -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="detailsTitle">
            <h3 id="detailsTitle">Rider Details</h3>
            <div class="modal-body">
                <p><strong>Rider ID:</strong> <span id="detailId">-</span></p>
                <p><strong>Name:</strong> <span id="detailName">-</span></p>
                <p><strong>Email:</strong> <span id="detailEmail">-</span></p>
                <p><strong>Temp Password:</strong> <span id="detailTempPassword">-</span></p>
                <p><strong>Phone:</strong> <span id="detailPhone">-</span></p>
                <p><strong>Status:</strong> <span id="detailStatus">-</span></p>
                <p><strong>Address:</strong> <span id="detailAddress">-</span></p>
                <p><strong>Vehicle:</strong> <span id="detailVehicle">-</span></p>
                <p><strong>Last Delivery:</strong> <span id="detailLast">-</span></p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" data-close="detailsModal">Close</button>
                <button class="btn btn-danger" id="removeRiderBtn">Remove As a Rider</button>
            </div>
        </div>
    </div>

    <!-- Add Rider Modal -->
    <div class="modal-overlay" id="addRiderModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="addTitle">
            <h3 id="addTitle">Add Rider</h3>
            <form id="addRiderForm">
                <div class="modal-body">
                    <div class="form-row">
                        <label for="riderName">Name</label>
                        <input type="text" id="riderName" name="riderName"
                            oninput="this.value = this.value.replace(/[0-9]/g, '');" required>
                    </div>
                    <div class="form-row">
                        <label for="riderEmail">Email</label>
                        <input type="email" id="riderEmail" name="riderEmail" autocomplete="email" required>
                    </div>
                    <div class="form-row">
                        <label for="riderTempPassword">Temporary Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="riderTempPassword" name="riderTempPassword"
                                autocomplete="new-password" required minlength="6">
                            <i class='bx bx-hide toggle-password'
                                onclick="togglePassword('riderTempPassword', this)"></i>
                        </div>
                        <div id="riderPasswordError"
                            style="color: #ff4d4d; font-size: 0.85rem; margin-top: 4px; display: none;">The password
                            field must be at least 6 characters.</div>
                    </div>
                    <div class="form-row">
                        <label for="riderPhone">Phone</label>
                        <input type="text" id="riderPhone" name="riderPhone" required maxlength="12"
                            placeholder="e.g. 09123456789 or 639123456789"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); validatePhone(this, 'riderPhoneError')">
                        <div id="riderPhoneError"
                            style="color: #ff4d4d; font-size: 0.85rem; margin-top: 4px; display: none;">Invalid format.
                            Use 09... (11 digits) or 639... (12 digits).</div>
                    </div>
                    <div style="margin-top: 10px; color: #666; font-style: italic; font-size: 0.9rem;">
                        Note: New riders will start as <strong>Offline</strong> by default.
                    </div>
                    <div class="form-row">
                        <label for="riderVehicle">Vehicle</label>
                        <input type="text" id="riderVehicle" name="riderVehicle" required>
                    </div>
                    <div class="form-row">
                        <label for="riderAddress">Address</label>
                        <input type="text" id="riderAddress" name="riderAddress" required>
                    </div>
                    <div class="form-row">
                        <label for="riderProfilePicture">Profile Photo <span
                                style="font-weight:400;font-size:.85rem;">(Optional)</span></label>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <img id="addRiderAvatarPreview" src="{{ asset('images/rider_profile.png') }}" alt="Preview"
                                style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #fce206;background:#eee;">
                            <input type="file" id="riderProfilePicture" name="profile_picture" accept="image/*"
                                style="flex:1;" onchange="previewRiderAvatar(this,'addRiderAvatarPreview')">
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" data-close="addRiderModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Rider</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Rider Modal -->
    <div class="modal-overlay" id="editRiderModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="editTitle">
            <h3 id="editTitle">Edit Rider</h3>
            <form id="editRiderForm">
                <input type="hidden" id="editRiderRealId">
                <div class="modal-body">
                    <div class="form-row">
                        <label for="editRiderName">Name</label>
                        <input type="text" id="editRiderName" name="riderName"
                            oninput="this.value = this.value.replace(/[0-9]/g, '');" required>
                    </div>
                    <div class="form-row">
                        <label for="editRiderPhone">Phone</label>
                        <input type="text" id="editRiderPhone" name="riderPhone" required maxlength="12"
                            placeholder="e.g. 09123456789 or 639123456789"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); validatePhone(this, 'editRiderPhoneError')">
                        <div id="editRiderPhoneError"
                            style="color: #ff4d4d; font-size: 0.85rem; margin-top: 4px; display: none;">Invalid format.
                            Use 09... (11 digits) or 639... (12 digits).</div>
                    </div>
                    <div class="form-row">
                        <label for="editRiderVehicle">Vehicle</label>
                        <input type="text" id="editRiderVehicle" name="riderVehicle" required>
                    </div>
                    <div class="form-row">
                        <label for="editRiderAddress">Address</label>
                        <input type="text" id="editRiderAddress" name="riderAddress" required>
                    </div>
                    <div class="form-row">
                        <label for="editRiderStatus">Status</label>
                        <select id="editRiderStatus" name="riderStatus" required>
                            <option value="Online">Online</option>
                            <option value="On Delivery" disabled>On Delivery (System Managed)</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="editRiderProfilePicture">Profile Photo <span
                                style="font-weight:400;font-size:.85rem;">(Optional — leave blank to keep
                                current)</span></label>
                        <div style="display:flex;align-items:center;gap:14px;">
                            <img id="editRiderAvatarPreview" src="{{ asset('images/rider_profile.png') }}" alt="Preview"
                                style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #fce206;background:#eee;">
                            <input type="file" id="editRiderProfilePicture" name="profile_picture" accept="image/*"
                                style="flex:1;" onchange="previewRiderAvatar(this,'editRiderAvatarPreview')">
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" data-close="editRiderModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal" role="dialog" aria-modal="true">
            <h3>Confirm Removal</h3>
            <div class="modal-body">
                <p id="deleteConfirmText">Are you sure you want to remove this rider? This action cannot be undone.</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" data-close="deleteConfirmModal">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Remove</button>
            </div>
        </div>
    </div>
    <!-- Status Modal (Success/Error) -->
    <div class="modal-overlay" id="statusModal">
        <div class="modal" role="dialog" aria-modal="true">
            <h3 id="statusTitle">Notification</h3>
            <div class="modal-body">
                <div id="statusIcon" style="font-size: 3rem; text-align: center; margin-bottom: 10px;"></div>
                <p id="statusMessage" style="text-align: center;"></p>
            </div>
            <div class="modal-actions" style="justify-content: center;">
                <button class="btn btn-primary" data-close="statusModal">OK</button>
            </div>
        </div>
    </div>


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




        const detailsModal = document.getElementById('detailsModal');
        const addRiderModal = document.getElementById('addRiderModal');
        const addRiderBtn = document.getElementById('addRiderBtn');
        const ridersTableBody = document.getElementById('ridersTableBody');
        const removeRiderBtn = document.getElementById('removeRiderBtn');
        const addRiderForm = document.getElementById('addRiderForm');
        const riderSearch = document.getElementById('riderSearch');
        const statusFilter = document.getElementById('statusFilter');
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const deleteConfirmText = document.getElementById('deleteConfirmText');
        const statusModal = document.getElementById('statusModal');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const statusIcon = document.getElementById('statusIcon');
        let currentRow = null;

        const openModal = modal => modal.classList.add('active');
        const closeModal = modal => modal.classList.remove('active');
        const getStatusClass = status => status === 'Online' ? 'status-available' : status === 'On Delivery' ? 'status-delivery' : 'status-offline';

        const showStatus = (title, message, isSuccess = true) => {
            statusTitle.textContent = title;
            statusMessage.innerHTML = message;
            statusIcon.innerHTML = isSuccess ? "<i class='bx bx-check-circle' style='color: #4CAF50;'></i>" : "<i class='bx bx-error-circle' style='color: #ff4d4d;'></i>";
            openModal(statusModal);
        };

        [detailsModal, addRiderModal, deleteConfirmModal, statusModal, document.getElementById('editRiderModal')].forEach(modal => modal && modal.addEventListener('click', e => {
            if (e.target === modal) closeModal(modal);
        }));

        // NEW: Close buttons
        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById(btn.getAttribute('data-close'));
                if (modal) closeModal(modal);
            });
        });
        let allRiders = [];
        let currentPage = 1;
        const itemsPerPage = 5;

        // Fetch all riders from backend
        const fetchRiders = async () => {
            try {
                const res = await fetch("{{ route('admin.riders.list') }}");
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                allRiders = await res.json();
                renderPage(1);
            } catch (err) {
                console.error('Failed to fetch riders:', err);
                ridersTableBody.innerHTML = '<tr><td colspan="5">Unable to load riders</td></tr>';
            }
        };

        const renderPage = (page) => {
            currentPage = page;
            const query = riderSearch.value.trim().toLowerCase();
            const status = statusFilter.value;

            const filtered = allRiders.filter(rider => {
                const haystack = [rider.id, rider.name, rider.email, rider.phone, rider.status].join(' ').toLowerCase();
                return ((!query || haystack.includes(query)) && (status === 'All' || rider.status === status));
            });
            // Note: rider.status is the raw status (Online/Offline/On Delivery) from backend

            const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginated = filtered.slice(start, end);

            ridersTableBody.innerHTML = '';

            if (filtered.length === 0) {
                ridersTableBody.innerHTML = '<tr><td colspan="5">No riders found</td></tr>';
                renderPagination(0);
                return;
            }

            paginated.forEach(rider => {
                const tr = document.createElement('tr');
                tr.dataset.id = rider.id;
                tr.dataset.realId = rider.real_id;
                tr.dataset.name = rider.name;
                tr.dataset.email = rider.email;
                tr.dataset.phone = rider.phone || '';
                tr.dataset.status = rider.status;
                tr.dataset.address = rider.address || '';
                tr.dataset.tempPassword = rider.tempPassword || '';
                tr.dataset.vehicle = rider.vehicle || '';
                tr.dataset.profilePicture = rider.profile_picture || '';
                tr.dataset.last = rider.last || '';
                tr.dataset.lastSeenHuman = rider.last_seen_human || '';

                const profilePicHtml = rider.profile_picture
                    ? `<img src="${rider.profile_picture}" class="rider-img-td" alt="Profile">`
                    : `<i class='bx bx-user-circle' style="font-size: 40px; color: #ccc;"></i>`;

                // Build last seen text shown below the status badge
                let lastSeenHtml = '';
                if (rider.status === 'Offline' && rider.last_seen_human) {
                    lastSeenHtml = `<div style="font-size:0.72rem; color:#bbb; margin-top:3px; font-weight:400;">Active ${rider.last_seen_human}</div>`;
                } else if (rider.status === 'Online') {
                    lastSeenHtml = `<div style="font-size:0.72rem; color:#4CAF50; margin-top:3px; font-weight:400;">● Online now</div>`;
                } else if (rider.status === 'On Delivery') {
                    lastSeenHtml = `<div style="font-size:0.72rem; color:#ff9800; margin-top:3px; font-weight:400;">🚴 On the road</div>`;
                }

                tr.innerHTML = `
                    <td style="text-align: center;">${profilePicHtml}</td>
                    <td>${rider.name}</td>
                    <td class="${getStatusClass(rider.status)}" style="text-align: center;">
                        ${rider.status}
                        ${lastSeenHtml}
                    </td>
                    <td style="text-align: center;">${rider.phone || '-'}</td>
                    <td style="text-align: center;">
                        <a href="#" class="view-details view-details-btn">View</a>
                        <a href="#" class="edit-rider view-details-btn" style="background: #2196F3; color: white; margin-left: 5px;">Edit</a>
                    </td>
                `;
                ridersTableBody.appendChild(tr);
            });
            renderPagination(filtered.length);
        };

        function renderPagination(totalItems) {
            const paginationContainer = document.getElementById('paginationContainer');
            const totalPages = Math.ceil(totalItems / itemsPerPage);

            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let html = `
                <button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class='bx bx-chevron-left'></i> Previous
                </button>
                <div class="page-numbers">
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<div class="page-num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</div>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<div style="color:#fce206; display:flex; align-items:center;">...</div>`;
                }
            }

            html += `
                </div>
                <button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    Next <i class='bx bx-chevron-right'></i>
                </button>
            `;

            paginationContainer.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            renderPage(page);
            document.querySelector('.recent-orders').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Filters
        const applyFilters = () => {
            currentPage = 1;
            renderPage(1);
        };

        addRiderBtn.addEventListener('click', () => openModal(addRiderModal));
        riderSearch.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);

        // Open details modal
        ridersTableBody.addEventListener('click', e => {
            const viewLink = e.target.closest('.view-details');
            const editLink = e.target.closest('.edit-rider');

            if (viewLink) {
                e.preventDefault();
                currentRow = viewLink.closest('tr');
                ['Id', 'Name', 'Email', 'TempPassword', 'Phone', 'Status', 'Address', 'Vehicle', 'Last'].forEach(k => document.getElementById(`detail${k}`).textContent = currentRow.dataset[k.toLowerCase()] || '-');
                openModal(detailsModal);
                return;
            }

            if (editLink) {
                e.preventDefault();
                currentRow = editLink.closest('tr');
                document.getElementById('editRiderRealId').value = currentRow.dataset.realId;
                document.getElementById('editRiderName').value = currentRow.dataset.name;
                document.getElementById('editRiderPhone').value = currentRow.dataset.phone;
                document.getElementById('editRiderAddress').value = currentRow.dataset.address;
                document.getElementById('editRiderVehicle').value = currentRow.dataset.vehicle;
                document.getElementById('editRiderStatus').value = currentRow.dataset.status;
                // Show current profile picture
                const pic = currentRow.dataset.profilePicture;
                const preview = document.getElementById('editRiderAvatarPreview');
                preview.src = pic || '{{ asset("images/rider_profile.png") }}';
                document.getElementById('editRiderProfilePicture').value = '';
                openModal(document.getElementById('editRiderModal'));
                return;
            }
        });

        removeRiderBtn.addEventListener('click', () => {
            if (!currentRow) return;
            const riderName = currentRow.dataset.name;
            deleteConfirmText.textContent = `Are you sure you want to remove ${riderName} as a rider? This action cannot be undone.`;
            openModal(deleteConfirmModal);
        });

        confirmDeleteBtn.addEventListener('click', async () => {
            if (!currentRow) return;
            const riderId = currentRow.dataset.realId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const res = await fetch(`/admin/riders/${riderId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });

                const data = await res.json();
                if (data.ok) {
                    currentRow.remove();
                    currentRow = null;
                    closeModal(deleteConfirmModal);
                    closeModal(detailsModal);
                    showStatus('Success', 'Rider removed successfully.', true);
                } else {
                    showStatus('Error', data.message || 'Failed to remove rider.', false);
                }
            } catch (err) {
                console.error('Delete error:', err);
                showStatus('Error', 'An error occurred while trying to remove the rider.', false);
            }
        });

        addRiderForm.addEventListener('submit', e => {
            e.preventDefault();

            const passwordInput = document.getElementById('riderTempPassword');
            const passwordError = document.getElementById('riderPasswordError');

            if (passwordInput.value.trim().length < 6) {
                passwordError.style.display = 'block';
                passwordInput.style.borderColor = '#ff4d4d';
                return;
            } else {
                passwordError.style.display = 'none';
                passwordInput.style.borderColor = '';
            }

            const phoneInput = document.getElementById('riderPhone');
            const phoneError = document.getElementById('riderPhoneError');
            if (!isValidPhone(phoneInput.value)) {
                phoneError.style.display = 'block';
                phoneInput.style.borderColor = '#ff4d4d';
                return;
            }

            passwordInput.addEventListener('input', () => {
                passwordError.style.display = 'none';
                passwordInput.style.borderColor = '';
            });

            const formData = new FormData();
            formData.append('name', document.getElementById('riderName').value.trim());
            formData.append('email', document.getElementById('riderEmail').value.trim());
            formData.append('password', passwordInput.value.trim());
            formData.append('phone', document.getElementById('riderPhone').value.trim());
            formData.append('riderAddress', document.getElementById('riderAddress').value.trim());
            formData.append('vehicle', document.getElementById('riderVehicle').value.trim());
            const picFile = document.getElementById('riderProfilePicture').files[0];
            if (picFile) formData.append('profile_picture', picFile);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('admin.riders.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.ok) {
                    const msg = `Rider <strong>${data.rider.name}</strong> added successfully.<br><br>
                                 Email: ${data.rider.email}<br>
                                 Temp Password: ${data.rider.tempPassword}`;
                    showStatus('Success', msg, true);
                    fetchRiders();
                    addRiderForm.reset();
                    document.getElementById('addRiderAvatarPreview').src = '{{ asset("images/rider_profile.png") }}';
                    closeModal(addRiderModal);
                } else {
                    showStatus('Error', data.message || 'Unable to add rider', false);
                }
            }).catch(err => {
                showStatus('Error', 'An unexpected error occurred.', false);
            });
        });
        // EDIT RIDER API — uses FormData so profile_picture file is included
        document.getElementById('editRiderForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const riderId = document.getElementById('editRiderRealId').value;

            const phoneInput = document.getElementById('editRiderPhone');
            const phoneError = document.getElementById('editRiderPhoneError');
            if (!isValidPhone(phoneInput.value.trim())) {
                phoneError.style.display = 'block';
                phoneInput.style.borderColor = '#ff4d4d';
                return;
            }

            const formData = new FormData();
            formData.append('name', document.getElementById('editRiderName').value.trim());
            formData.append('phone', phoneInput.value.trim());
            formData.append('address', document.getElementById('editRiderAddress').value.trim());
            formData.append('vehicle', document.getElementById('editRiderVehicle').value.trim());
            formData.append('status', document.getElementById('editRiderStatus').value);
            formData.append('_method', 'PUT'); // Laravel method spoofing

            const fileInput = document.getElementById('editRiderProfilePicture');
            if (fileInput.files[0]) {
                formData.append('profile_picture', fileInput.files[0]);
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const res = await fetch(`/api/riders/${riderId}`, {
                    method: 'POST', // POST with _method=PUT for file upload
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await res.json();
                if (data.ok) {
                    showStatus('Success', 'Rider updated successfully.', true);
                    closeModal(document.getElementById('editRiderModal'));
                    fetchRiders();
                } else {
                    showStatus('Error', data.message || 'Failed to update rider.', false);
                }
            } catch (err) {
                console.error('Update error:', err);
                showStatus('Error', 'An error occurred while updating the rider.', false);
            }
        });

        // Initial load
        fetchRiders();

        // Live avatar preview helper
        function previewRiderAvatar(input, previewId) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => document.getElementById(previewId).src = e.target.result;
            reader.readAsDataURL(file);
        }

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
            if (val.startsWith('09')) return val.length === 11;
            if (val.startsWith('639')) return val.length === 12;
            return false;
        }
    </script>
    @include('partials.logout-modal')
</body>

</html>