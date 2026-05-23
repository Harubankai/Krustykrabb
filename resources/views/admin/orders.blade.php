<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .delivery-photo-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #fce206;
            transition: transform 0.2s;
        }

        .delivery-photo-preview:hover {
            transform: scale(1.1);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-completed {
            background: #4caf50;
            color: #fff;
        }

        .status-preparing {
            background: #ff9800;
            color: #fff;
        }

        .status-accepted {
            background: #2196f3;
            color: #fff;
        }

        .status-transit {
            background: #9c27b0;
            color: #fff;
        }

        .status-arrived {
            background: #00bcd4;
            color: #fff;
        }

        #fullPhotoModal .modal-card {
            max-width: 90vw;
            width: auto;
            background: #000;
            padding: 10px;
        }

        #fullPhotoModal img {
            max-width: 100%;
            max-height: 80vh;
            display: block;
            margin: 0 auto;
        }

        /* Pagination styles inherited from admin.css */
    </style>
</head>

<body>


    <div class="mobile-toggle" id="mobileToggle">
        <i class='bx bx-menu'></i>
    </div>

    <div class="mobile-overlay" id="mobileOverlay"></div>



    <nav class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" class="logoImg">
            <span class="brand-title">ADMIN</span>
        </div>

        <ul class="menu">
            <li><a href="{{ route('admin.dashboard') }}"><i class='bx bx-trending-up'></i> Dashboard</a></li>
            <li><a href="{{ route('admin.orders') }}" class="active"><i class='bx bx-basket'></i> Orders</a></li>
            <li><a href="{{ route('admin.manage-riders') }}"><i class='bx bx-group'></i> Manage Riders</a></li>
            <li><a href="{{ route('admin.manage-customers') }}"><i class='bx bx-user-circle'></i> Manage Customers</a>
            </li>
            <li><a href="{{ route('admin.modify-menu') }}"><i class="bx bx-edit"></i> Modify Menu</a></li>
            <li><a href="{{ route('admin.messages') }}"><i class='bx bx-envelope'></i> Messages</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="header">
            <h1>Order Management</h1>
            <p>View and verify deliveries with photo proof.</p>
        </header>

        <section class="recent-orders">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>All Orders</h2>
                <div class="search-box">
                    <input type="text" id="orderSearch" placeholder="Search Order ID, Customer, or Rider..."
                        style="padding: 10px; border-radius: 8px; border: 1px solid #fce206; width: 300px; background: rgba(0,0,0,0.5); color: #fff;">
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Rider</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Proof of Delivery</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">Loading orders...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-container" id="paginationControls">
                <!-- Pagination will be injected here -->
            </div>
        </section>
    </main>

    <!-- Photo Modal -->
    <div class="modal" id="fullPhotoModal"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; align-items: center; justify-content: center; cursor: pointer;"
        onclick="this.style.display='none'">
        <div class="modal-card">
            <img id="fullPhotoImg" src="" alt="Proof of Delivery">
            <p style="color: #fff; text-align: center; margin-top: 10px;">Tap anywhere to close</p>
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



        const ordersTableBody = document.getElementById('ordersTableBody');
        const orderSearch = document.getElementById('orderSearch');
        const fullPhotoModal = document.getElementById('fullPhotoModal');
        const fullPhotoImg = document.getElementById('fullPhotoImg');

        let allOrders = [];
        let currentPage = 1;
        const itemsPerPage = 5;
        let filteredOrders = [];

        async function fetchOrders() {
            try {
                const response = await fetch("{{ route('api.admin.orders') }}");
                const data = await response.json();

                // Only update if data changed or if it's the first load
                if (JSON.stringify(allOrders) !== JSON.stringify(data)) {
                    allOrders = data;
                    applyFiltersAndRender();
                }
            } catch (err) {
                console.error(err);
                ordersTableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #ff4d4d;">Failed to load orders.</td></tr>';
            }
        }

        function getStatusClass(status) {
            const s = status.toLowerCase();
            if (s === 'completed') return 'status-completed';
            if (s === 'preparing') return 'status-preparing';
            if (s === 'accepted') return 'status-accepted';
            if (s === 'in transit') return 'status-transit';
            if (s === 'arrived') return 'status-arrived';
            return '';
        }

        function applyFiltersAndRender() {
            const term = orderSearch.value.toLowerCase();
            filteredOrders = allOrders.filter(o =>
                o.id.toLowerCase().includes(term) ||
                (o.customer && o.customer.name.toLowerCase().includes(term)) ||
                (o.rider && o.rider.name.toLowerCase().includes(term))
            );

            // Recalculate max pages if current page is now out of bounds
            const maxPage = Math.max(1, Math.ceil(filteredOrders.length / itemsPerPage));
            if (currentPage > maxPage) currentPage = maxPage;

            renderOrders();
            renderPagination();
        }

        function renderOrders() {
            if (filteredOrders.length === 0) {
                ordersTableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">No orders found.</td></tr>';
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedItems = filteredOrders.slice(startIndex, endIndex);

            ordersTableBody.innerHTML = paginatedItems.map(order => `
                <tr>
                    <td><strong>${order.id}</strong></td>
                    <td>${order.customer ? order.customer.name : '—'}</td>
                    <td>${order.rider ? order.rider.name : '<span style="opacity: 0.5;">Unassigned</span>'}</td>
                    <td style="color: #fce206; font-weight: bold;">₱${parseFloat(order.totalPrice).toFixed(2)}</td>
                    <td><span class="status-badge ${getStatusClass(order.status)}">${order.status}</span></td>
                    <td>
                        ${order.delivery_photo ?
                    `<img src="${order.delivery_photo}" class="delivery-photo-preview" onclick="showFullPhoto('${order.delivery_photo}')" title="Click to view full photo">` :
                    '<span style="opacity: 0.5; font-size: 0.85rem;">No photo yet</span>'
                }
                    </td>
                    <td style="font-size: 0.85rem; opacity: 0.8;">${new Date(order.createdAt).toLocaleString()}</td>
                </tr>
            `).join('');
        }

        function renderPagination() {
            const paginationControls = document.getElementById('paginationControls');
            const totalPages = Math.ceil(filteredOrders.length / itemsPerPage);

            if (totalPages <= 1) {
                paginationControls.innerHTML = '';
                return;
            }

            let html = `
                <button class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class='bx bx-chevron-left'></i> Previous
                </button>
                <div class="page-numbers">
            `;

            // Simple pagination logic: show first, last, and around current
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<div class="page-num ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</div>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<div style="color: #fff; display: flex; align-items: center;">...</div>`;
                }
            }

            html += `
                </div>
                <button class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    Next <i class='bx bx-chevron-right'></i>
                </button>
            `;

            paginationControls.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            renderOrders();
            renderPagination();
            // Scroll to top of table
            document.querySelector('.recent-orders').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function showFullPhoto(src) {
            fullPhotoImg.src = src;
            fullPhotoModal.style.display = 'flex';
        }

        orderSearch.addEventListener('input', () => {
            currentPage = 1;
            applyFiltersAndRender();
        });

        fetchOrders();
        setInterval(fetchOrders, 10000); // Refresh every 10 seconds
    </script>

    @include('partials.logout-modal')
</body>

</html>