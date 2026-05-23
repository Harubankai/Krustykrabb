<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Customers</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .customer-img-td {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fce206;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .view-details-btn {
            background: #fce206;
            color: #611909;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 800;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
            display: inline-block;
        }

        .view-details-btn:hover {
            background: #e6cf05;
            transform: scale(1.05);
        }

        #customerSearch {
            border: 2px solid #fce206;
            background: white;
            color: #611909;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            width: 100%;
        }

        #customerSearch:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(252, 226, 6, 0.4);
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
            width: 160px;
            display: inline-block;
        }

        .modal-actions {
            flex-shrink: 0;
            padding: 20px;
            background: #f9f9f9;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid #eee;
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
            <li><a href="{{ route('admin.manage-riders') }}"><i class='bx bx-group'></i> Manage Riders </a></li>
            <li><a href="{{ route('admin.manage-customers') }}" class="active"><i class='bx bx-user-circle'></i> Manage
                    Customers </a></li>
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
                    <h1>Manage Customers</h1>
                    <p>Overview of all registered customers and their latest activity.</p>
                </div>
            </div>
        </header>

        <section class="recent-orders" style="margin-top: 10px;">
            <h2>Search and Filter</h2>
            <div class="filters-row" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-row" style="flex: 1; min-width: 250px;">
                    <label for="customerSearch">Search Customer</label>
                    <input type="text" id="customerSearch" placeholder="Search by name, ID, email, or phone">
                </div>
                <div class="form-row" style="width: 200px;">
                    <label for="dateSort">Sort Joined</label>
                    <select id="dateSort"
                        style="border: 2px solid #fce206; background: white; color: #611909; padding: 12px; border-radius: 8px; font-size: 1rem; width: 100%; cursor: pointer; font-weight: bold;">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="recent-orders" style="margin-top: 30px;">
            <h2>Customer List</h2>
            <table>
                <thead>
                    <tr>
                        <th style="text-align: center;">Profile</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th style="text-align: center;">Latest Order</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">Loading customers...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-container" id="paginationContainer"></div>
        </section>
    </main>

    <!-- Details Modal -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="detailsTitle">
            <h3 id="detailsTitle">Customer Details</h3>
            <div class="modal-body">
                <p><strong>Customer ID:</strong> <span id="detailId">-</span></p>
                <p><strong>Name:</strong> <span id="detailName">-</span></p>
                <p><strong>Email:</strong> <span id="detailEmail">-</span></p>
                <p><strong>Phone:</strong> <span id="detailPhone">-</span></p>
                <p><strong>Total Orders:</strong> <span id="detailTotalOrders">-</span></p>
                <p><strong>Joined Date:</strong> <span id="detailJoinedDate">-</span></p>
                <p><strong>Latest Order Date:</strong> <span id="detailLatestOrder">-</span></p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" data-close="detailsModal">Close</button>
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
        const customersTableBody = document.getElementById('customersTableBody');
        const customerSearch = document.getElementById('customerSearch');
        const dateSort = document.getElementById('dateSort');

        const openModal = modal => modal.classList.add('active');
        const closeModal = modal => modal.classList.remove('active');

        // Close modal on overlay click
        detailsModal.addEventListener('click', e => {
            if (e.target === detailsModal) closeModal(detailsModal);
        });

        // Close buttons
        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById(btn.getAttribute('data-close'));
                if (modal) closeModal(modal);
            });
        });

        let allCustomers = [];
        let currentPage = 1;
        const itemsPerPage = 5;

        const fetchCustomers = async () => {
            const sort = dateSort.value;
            try {
                const res = await fetch(`{{ route('admin.customers.list') }}?sort=${sort}`);
                allCustomers = await res.json();
                renderPage(1);
            } catch (err) {
                console.error('Failed to fetch customers:', err);
                customersTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #ff4d4d;">Unable to load customers</td></tr>';
            }
        };

        const renderPage = (page) => {
            currentPage = page;
            const query = customerSearch.value.trim().toLowerCase();
            const filtered = allCustomers.filter(c => {
                const haystack = [c.id, c.name, c.email, c.phone].join(' ').toLowerCase();
                return !query || haystack.includes(query);
            });

            const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginated = filtered.slice(start, end);

            customersTableBody.innerHTML = '';

            if (filtered.length === 0) {
                customersTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">No customers found</td></tr>';
                renderPagination(0);
                return;
            }

            paginated.forEach(customer => {
                const tr = document.createElement('tr');
                tr.dataset.id = customer.id;
                tr.dataset.name = customer.name;
                tr.dataset.email = customer.email;
                tr.dataset.phone = customer.phone;
                tr.dataset.latest = customer.latest_order;
                tr.dataset.total = customer.total_orders;
                tr.dataset.joined = customer.joined_date;

                const profilePicHtml = customer.profile_picture
                    ? `<img src="${customer.profile_picture}" class="customer-img-td" alt="Profile">`
                    : `<i class='bx bx-user-circle' style="font-size: 40px; color: #ccc;"></i>`;

                tr.innerHTML = `
                    <td style="text-align: center;">${profilePicHtml}</td>
                    <td>${customer.name}</td>
                    <td>${customer.phone || '-'}</td>
                    <td style="font-size: 0.9rem; color: #fce206; text-align: center; font-weight: bold;">${customer.latest_order}</td>
                    <td style="text-align: center;"><a href="#" class="view-details view-details-btn">View</a></td>
                `;
                customersTableBody.appendChild(tr);
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

        const applyFilters = () => {
            currentPage = 1;
            renderPage(1);
        };

        customerSearch.addEventListener('input', applyFilters);
        dateSort.addEventListener('change', fetchCustomers);

        customersTableBody.addEventListener('click', e => {
            const link = e.target.closest('.view-details');
            if (!link) return;
            e.preventDefault();
            const row = link.closest('tr');

            document.getElementById('detailId').textContent = row.dataset.id;
            document.getElementById('detailName').textContent = row.dataset.name;
            document.getElementById('detailEmail').textContent = row.dataset.email;
            document.getElementById('detailPhone').textContent = row.dataset.phone || 'N/A';
            document.getElementById('detailTotalOrders').textContent = row.dataset.total;
            document.getElementById('detailJoinedDate').textContent = row.dataset.joined;
            document.getElementById('detailLatestOrder').textContent = row.dataset.latest;

            openModal(detailsModal);
        });

        fetchCustomers();
    </script>
    @include('partials.logout-modal')
</body>

</html>