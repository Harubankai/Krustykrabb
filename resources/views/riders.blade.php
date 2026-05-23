<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard</title>
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

        <div class="user-profile">
            @if(optional(session('user'))->profile_picture)
                <img src="{{ asset(session('user')->profile_picture) }}" alt="Profile" class="profile-img">
            @else
                <img src="{{ asset('images/rider_profile.png') }}" alt="Profile" class="profile-img">
            @endif
            <div class="user-info">
                <span class="user-name">{{ optional(session('user'))->name ?? 'Rider' }}</span>
                <span class="user-role">Rider</span>
            </div>
        </div>



        <ul class="menu">
            <li><a href="{{ route('rider.dashboard') }}" class="active"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('rider.delivery') }}"><i class="bx bx-cycling"></i> Delivery</a></li>
            <li><a href="{{ route('rider.profile') }}"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <section id="orders" class="recent-orders">
            <h2>Orders</h2>
            <p id="order-lock-msg" class="warning" style="display: none; margin-bottom: 12px;">
                You have active orders. Go to Delivery to process an accepted order.
            </p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Order</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody id="riderOrdersBody"></tbody>
                </table>
            </div>
        </section>

        <div id="orderDetailsContainer"></div>

    </main>

    @php
        $sessionRider = [
            'name' => optional(session('user'))->name ?? 'Rider',
            'email' => optional(session('user'))->email ?? null,
        ];
    @endphp

    <script>

const toggle = document.getElementById("mobileToggle");
const sidebar = document.querySelector(".sidebar");
const overlay = document.getElementById("mobileOverlay");

toggle.addEventListener("click", () => {

    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");

    // MOVE BUTTON
    if (sidebar.classList.contains("active")) {
        toggle.style.left = "294px";
    } else {
        toggle.style.left = "14px";
    }
});

overlay.addEventListener("click", () => {

    sidebar.classList.remove("active");
    overlay.classList.remove("active");

    toggle.style.left = "14px";
});

        (function () {
            const RIDER = @json($sessionRider);
            const CURRENT_STATUS = "{{ optional(session('user'))->status ?? 'Offline' }}";
            const ORDERS_STORAGE_KEY = 'kk_orders_v1';
            const LOCK_KEY = 'rider_active_order';
            const MULTI_LOCK_KEY = 'rider_active_orders';

            const lockMsg = document.getElementById('order-lock-msg');
            const ordersBody = document.getElementById('riderOrdersBody');
            const orderDetailsContainer = document.getElementById('orderDetailsContainer');

            const escapeHtml = (text) => String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#39;');

            const normalizeStatus = (status) => String(status || '').trim().toLowerCase();

            const statusLabel = (statusRaw) => {
                const s = normalizeStatus(statusRaw);
                if (s === 'completed' || s === 'done' || s === 'delivered') return 'Completed';
                if (s === 'arrived') return 'Arrived';
                if (s === 'in transit' || s === 'in-transit' || s === 'transit') return 'In Transit';
                if (s === 'picked up' || s === 'picked-up') return 'Picked Up';
                if (s === 'accepted') return 'Accepted';
                return 'Preparing';
            };

            const readOrders = () => {
                try {
                    const raw = localStorage.getItem(ORDERS_STORAGE_KEY);
                    const parsed = raw ? JSON.parse(raw) : [];
                    return Array.isArray(parsed) ? parsed : [];
                } catch (_) {
                    return [];
                }
            };

            const writeOrders = (orders) => {
                try { localStorage.setItem(ORDERS_STORAGE_KEY, JSON.stringify(orders)); } catch (_) { }
            };

            const getActiveOrderIds = () => {
                try {
                    const raw = localStorage.getItem(MULTI_LOCK_KEY);
                    if (raw) return JSON.parse(raw);
                } catch (e) {}
                const val = localStorage.getItem(LOCK_KEY);
                if (val && val !== '1') {
                    const arr = [String(val)];
                    localStorage.setItem(MULTI_LOCK_KEY, JSON.stringify(arr));
                    localStorage.removeItem(LOCK_KEY);
                    return arr;
                }
                return [];
            };

            const addActiveOrderId = (orderId) => {
                if (!orderId) return;
                const arr = getActiveOrderIds();
                if (!arr.includes(String(orderId))) {
                    arr.push(String(orderId));
                }
                localStorage.setItem(MULTI_LOCK_KEY, JSON.stringify(arr));
            };

            const summarizeItems = (items) => {
                if (!Array.isArray(items) || !items.length) return '—';
                return items
                    .slice(0, 3)
                    .map((i) => `${i.name} x${i.qty}`)
                    .join(', ') + (items.length > 3 ? `, +${items.length - 3} more` : '');
            };

            const formatPrice = (price) => `₱${Number(price || 0).toFixed(2)}`;

            let lastOrdersState = '';

            const render = async () => {
                let orders = [];
                try {
                    const response = await fetch("{{ route('api.orders.available') }}");
                    orders = await response.json();
                } catch (err) {
                    console.error("Failed to fetch available orders", err);
                    return;
                }

                const activeIds = getActiveOrderIds();

                if (lockMsg) lockMsg.style.display = 'none';

                const visible = orders.filter((o) => {
                    const s = normalizeStatus(o.status);
                    if (s === 'completed' || s === 'done' || s === 'delivered' || s === 'cancelled' || s === 'canceled') return false;
                    const hasRider = o.rider && o.rider.name;
                    if (!hasRider) return true;
                    if (String(o.rider.name) === String(RIDER.name)) return true;
                    return activeIds.includes(String(o.id));
                });

                const currentState = JSON.stringify(visible);
                if (currentState === lastOrdersState) return;
                lastOrdersState = currentState;

                if (ordersBody) {
                    if (!visible.length) {
                        ordersBody.innerHTML = `
                        <tr>
                            <td colspan="6" style="padding: 14px 0; opacity: 0.85;">No available orders right now.</td>
                        </tr>
                    `;
                    } else {
                        ordersBody.innerHTML = visible.map((o) => {
                            const cust = o.customer || {};
                            const customerName = cust.name || 'Customer';
                            const address = cust.address || '—';
                            const status = statusLabel(o.status);
                            const isMine = o.rider && o.rider.name && String(o.rider.name) === String(RIDER.name);
                            const canAccept = (!o.rider || !o.rider.name) && normalizeStatus(o.status) === 'preparing';
                            const actionHtml = canAccept
                                ? `<a href="#" class="status serving accept-btn" data-order-id="${escapeHtml(o.id)}" data-db-id="${escapeHtml(o.db_id)}">Accept</a>`
                                : isMine
                                    ? `<a href="{{ route('rider.delivery') }}" onclick="let arr=[]; try{arr=JSON.parse(localStorage.getItem('rider_active_orders'))||[];}catch(e){} if(!arr.includes('${escapeHtml(o.id)}'))arr.push('${escapeHtml(o.id)}'); localStorage.setItem('rider_active_orders', JSON.stringify(arr)); localStorage.setItem('rider_current_view', '${escapeHtml(o.id)}')" class="status serving">Deliver</a>`
                                    : `<span class="status completed">${escapeHtml(status)}</span>`;

                            return `
                            <tr>
                                <td>${escapeHtml(customerName)}</td>
                                <td>${escapeHtml(summarizeItems(o.items))}</td>
                                <td>${escapeHtml(address)}</td>
                                <td>${escapeHtml(status)}</td>
                                <td>${actionHtml}</td>
                                <td><a href="#order-details-${escapeHtml(o.id)}" class="status cooking">View Details&gt;&gt;</a></td>
                            </tr>
                        `;
                        }).join('');
                    }
                }

                if (orderDetailsContainer) {
                    orderDetailsContainer.innerHTML = visible.map((o) => {
                        const cust = o.customer || {};
                        const customerName = cust.name || 'Customer';
                        const phone = cust.phone || '—';
                        const address = cust.address || '—';
                        const items = Array.isArray(o.items) ? o.items : [];
                        const total = items.reduce((sum, i) => sum + (Number(i.price) || 0) * (Number(i.qty) || 0), 0);

                        const itemRows = items.map((i) => `
                        <tr>
                            <td>${escapeHtml(i.name)}</td>
                            <td>${escapeHtml(String(i.qty || 0))}</td>
                            <td>${escapeHtml(formatPrice((Number(i.price) || 0) * (Number(i.qty) || 0)))}</td>
                        </tr>
                    `).join('');

                        return `
                        <div id="order-details-${escapeHtml(o.id)}" class="modal">
                            <div class="modal-card">
                                <div class="modal-header">
                                    <h3>Order ${escapeHtml(o.id)} Details</h3>
                                    <a href="#" class="modal-close">Close</a>
                                </div>
                                <div class="modal-grid">
                                    <div class="modal-section">
                                        <h4>Customer</h4>
                                        <p>Name: ${escapeHtml(customerName)}</p>
                                        <p>Mobile: ${escapeHtml(phone)}</p>
                                        <p>Address: ${escapeHtml(address)}</p>
                                    </div>
                                    <div class="modal-section">
                                        <h4>Receipt</h4>
                                        <div class="table-wrap">
                                            <table class="receipt">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${itemRows || ''}
                                                    <tr>
                                                        <td><strong>Total</strong></td>
                                                        <td></td>
                                                        <td><strong>${escapeHtml(formatPrice(total))}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    }).join('');
                }
            };

            const acceptOrder = async (orderId, dbId) => {

                try {
                    const response = await fetch(`/api/orders/${dbId}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });

                    const result = await response.json();
                    if (result.success) {
                        addActiveOrderId(orderId);
                        localStorage.setItem('rider_current_view', String(orderId));
                        window.location.href = "{{ route('rider.delivery') }}";
                    } else {
                        alert('Order could not be accepted: ' + (result.message || 'Unknown error'));
                        render();
                    }
                } catch (err) {
                    alert('Connection error');
                }
            };

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.accept-btn');
                if (!btn) return;
                e.preventDefault();
                const orderId = btn.getAttribute('data-order-id');
                const dbId = btn.getAttribute('data-db-id');
                if (!orderId || !dbId) return;
                acceptOrder(orderId, dbId);
            });

            // Status Toggle Logic
            const statusToggle = document.getElementById('statusToggle');
            const statusText = document.getElementById('currentStatusText');

            if (statusToggle) {
                statusToggle.addEventListener('change', async () => {
                    const newStatus = statusToggle.checked ? 'Online' : 'Offline';

                    try {
                        const res = await fetch("{{ route('api.rider.status.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });

                        const result = await res.json();
                        if (result.success) {
                            statusText.textContent = result.status;
                            statusText.className = 'status-label ' + result.status.toLowerCase();
                            render(); // Refresh orders view
                        } else {
                            statusToggle.checked = !statusToggle.checked;
                            alert('Failed to update status');
                        }
                    } catch (e) {
                        statusToggle.checked = !statusToggle.checked;
                        alert('Connection error');
                    }
                });
            }

            setInterval(render, 5000);
            render();
        })();
    </script>
    @include('partials.logout-modal')
</body>

</html>