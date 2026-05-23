<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krusty Krab - Orders</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/orders.css') }}">
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


        <div class="user-profile">
            @if(optional(session('user'))->profile_picture)
                <img src="{{ asset(session('user')->profile_picture) }}" alt="Profile" class="profile-img">
            @else
                <img src="{{ asset('images/customer_profile.png') }}" alt="Profile" class="profile-img">
            @endif
            <div class="user-info">
                <span class="user-name">{{ optional(session('user'))->name ?? 'Guest' }}</span>
                <span class="user-role">Customer</span>
            </div>
        </div>



        <ul class="menu">
            <li><a href="{{ route('customer.dashboard') }}"><i class="bx bx-store"></i> Menu</a></li>
            <li><a href="{{ route('customer.cart') }}"><i class="bx bx-cart"></i> Cart</a></li>
            <li><a href="{{ route('customer.orders') }}" class="active"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('customer.profile') }}"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="header">
            <h1>Orders</h1>
            <p>Track your order status and rider updates in real time.</p>
        </header>

        <section class="dashboard-grid orders-grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Current Orders</h2>
                </div>

                <div class="empty-state" id="currentOrdersEmpty" hidden>
                    No current orders yet. Place an order from the Menu.
                </div>

                <div class="table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items ordered</th>
                                <th>Total price</th>
                                <th>Status</th>
                                <th>Rider</th>
                                <th>Placed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="currentOrdersBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2>Previous Orders</h2>
                    <div class="filter-controls" style="display: flex; gap: 10px; align-items: center;">
                        <label for="dateSort" style="color: #fce206; font-weight: bold; font-size: 0.9rem;">Sort
                            by:</label>
                        <select id="dateSort"
                            style="background: rgba(0,0,0,0.5); color: #fce206; border: 1px solid #fce206; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="empty-state" id="previousOrdersEmpty" hidden>
                    No previous orders yet.
                </div>

                <div class="table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items ordered</th>
                                <th>Total price</th>
                                <th>Status</th>
                                <th>Rider</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="previousOrdersBody"></tbody>
                    </table>
                </div>

                <div class="pagination-container" id="prevOrdersPagination"
                    style="display: none; justify-content: center; align-items: center; gap: 15px; margin-top: 20px;">
                    <button class="pagination-btn" id="prevOrdersPrev"
                        style="background: transparent; border: 1px solid #fce206; color: #fce206; padding: 5px 15px; border-radius: 4px; cursor: pointer;">&laquo;
                        Previous</button>
                    <span id="prevOrdersPageInfo" style="color: #fce206; font-weight: bold;">Page 1 of 1</span>
                    <button class="pagination-btn" id="prevOrdersNext"
                        style="background: transparent; border: 1px solid #fce206; color: #fce206; padding: 5px 15px; border-radius: 4px; cursor: pointer;">Next
                        &raquo;</button>
                </div>
            </div>
        </section>
    </main>

    @php
        $currentCustomer = [
            'id' => optional(session('user'))->id,
            'email' => optional(session('user'))->email,
        ];
    @endphp

    <div class="modal-overlay" id="trackModal" hidden>
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="trackTitle">
            <div class="modal-header">
                <h2 id="trackTitle">Order Tracking</h2>
                <button type="button" class="modal-close" id="closeTrackModal" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">
                <div class="track-top">
                    <div>
                        <div class="track-label">Order Number</div>
                        <div class="track-value" id="trackOrderId">—</div>
                    </div>
                    <div>
                        <div class="track-label">Status</div>
                        <div class="track-value" id="trackStatus">—</div>
                    </div>
                    <div>
                        <div class="track-label">Rider</div>
                        <div class="track-value" id="trackRider">—</div>
                    </div>
                    <div>
                        <div class="track-label">Mobile Number</div>
                        <div class="track-value" id="trackRiderPhone">—</div>
                    </div>
                </div>

                <hr class="modal-divider">

                <section class="modal-section">
                    <h3>Receipt</h3>
                    <div id="trackReceipt"></div>
                </section>

                <hr class="modal-divider">

                <section class="modal-section">
                    <h3>Tracking</h3>
                    <ol class="timeline" id="trackTimeline"></ol>
                </section>

                <hr class="modal-divider">

                <section class="modal-section">
                    <h3>Payment</h3>
                    <div class="payment-line">Mode of Payment: <strong>Cash on Delivery</strong></div>
                </section>
            </div>


        </div>
    </div>

    <div class="modal-overlay" id="cancelConfirmModal" hidden>
        <div class="modal" role="dialog" style="max-width: 400px; text-align: center;">
            <div class="modal-header" style="justify-content: center; border-bottom: 1px solid rgba(252,226,6,0.2);">
                <h2 style="color: #d32f2f;">Confirm Cancellation</h2>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <p>Are you sure you want to cancel order <strong id="cancelOrderIdText"></strong>?</p>
            </div>
            <div style="display: flex; gap: 10px; justify-content: center; padding-bottom: 20px;">
                <button type="button" class="cancel-btn" style="background: transparent; color: #030303; border: 1px solid #fce206;" onclick="closeCancelModal()">No, Keep it</button>
                <button type="button" class="cancel-btn" id="confirmCancelProceedBtn" style="background: #d32f2f; color: #fff; border: 1px solid #d32f2f;">Yes, Cancel</button>
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



        const ORDERS_STORAGE_KEY = 'kk_orders_v1';
        const CURRENT_CUSTOMER = @json($currentCustomer);
        const CURRENCY = '\u20B1';

        const currentOrdersBody = document.getElementById('currentOrdersBody');
        const previousOrdersBody = document.getElementById('previousOrdersBody');
        const currentOrdersEmpty = document.getElementById('currentOrdersEmpty');
        const previousOrdersEmpty = document.getElementById('previousOrdersEmpty');

        const trackModal = document.getElementById('trackModal');
        const closeTrackModal = document.getElementById('closeTrackModal');

        const trackOrderId = document.getElementById('trackOrderId');
        const trackStatus = document.getElementById('trackStatus');
        const trackRider = document.getElementById('trackRider');
        const trackRiderPhone = document.getElementById('trackRiderPhone');
        const trackReceipt = document.getElementById('trackReceipt');
        const trackTimeline = document.getElementById('trackTimeline');

        let activeOrderId = null;
        let refreshTimer = null;

        let currentPrevPage = 1;
        const prevItemsPerPage = 5;
        let sortedPreviousOrders = [];

        const escapeHtml = (text) => String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');

        const formatCurrency = (amount) => `${CURRENCY}${Number(amount || 0).toFixed(2)}`;

        const formatDateTime = (iso) => {
            if (!iso) return '—';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return '—';
            return d.toLocaleString();
        };

        const normalizeStatus = (status) => String(status || '').trim().toLowerCase();

        const statusBadge = (statusRaw) => {
            const s = normalizeStatus(statusRaw);
            if (s === 'completed' || s === 'done' || s === 'delivered') return { label: 'Completed', cls: 'status--completed' };
            if (s === 'arrived') return { label: 'Arrived', cls: 'status--arrived' };
            if (s === 'in transit' || s === 'in-transit' || s === 'transit') return { label: 'In Transit', cls: 'status--transit' };
            if (s === 'picked up' || s === 'picked-up') return { label: 'Picked Up', cls: 'status--accepted' };
            if (s === 'accepted') return { label: 'Accepted', cls: 'status--accepted' };
            if (s === 'cancelled' || s === 'canceled') return { label: 'Cancelled', cls: 'status--cancelled' };
            return { label: 'Preparing', cls: 'status--preparing' };
        };

        const orderBelongsToCustomer = (order) => {
            if (!CURRENT_CUSTOMER?.id && !CURRENT_CUSTOMER?.email) return true;
            const customer = order?.customer;
            if (!customer) return true; // fallback for older seeded orders
            if (CURRENT_CUSTOMER?.id && customer.id && String(customer.id) === String(CURRENT_CUSTOMER.id)) return true;
            if (CURRENT_CUSTOMER?.email && customer.email && String(customer.email).toLowerCase() === String(CURRENT_CUSTOMER.email).toLowerCase()) return true;
            return false;
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

        const normalizeOrders = (orders) => orders
            .filter((o) => o && typeof o === 'object')
            .map((o) => ({
                id: String(o.id || ''),
                createdAt: o.createdAt || o.placedAt || null,
                lastUpdatedAt: o.lastUpdatedAt || null,
                paymentMethod: o.paymentMethod || 'Cash on Delivery',
                status: o.status || 'Preparing',
                totalPrice: Number(o.totalPrice) || 0,
                totalItems: Number(o.totalItems) || 0,
                items: Array.isArray(o.items) ? o.items : [],
                customer: o.customer || null,
                rider: o.rider || null,
                statusTimestamps: o.statusTimestamps || {}
            }))
            .filter((o) => o.id);

        const summarizeItems = (items) => {
            if (!Array.isArray(items) || !items.length) return '—';
            return items
                .slice(0, 3)
                .map((i) => `${i.name} x${i.qty}`)
                .join(', ') + (items.length > 3 ? `, +${items.length - 3} more` : '');
        };

        const renderTable = (orders, tbody, isCurrent = false) => {
            tbody.innerHTML = orders.map((o) => {
                const badge = statusBadge(o.status);
                const riderName = o?.rider?.name ? String(o.rider.name) : 'Not assigned';
                
                const normalizedStatusVal = normalizeStatus(o.status);
                const showCancel = isCurrent && (normalizedStatusVal === 'preparing' || normalizedStatusVal === 'accepted');
                const cancelBtnHtml = showCancel ? `<button type="button" class="cancel-btn" data-order-dbid="${escapeHtml(o.db_id || o.id)}" data-order-id="${escapeHtml(o.id)}">Cancel</button>` : '';

                return `
                    <tr>
                        <td>${escapeHtml(o.id)}</td>
                        <td>${escapeHtml(summarizeItems(o.items))}</td>
                        <td>${escapeHtml(formatCurrency(o.totalPrice))}</td>
                        <td><span class="status ${badge.cls}">${escapeHtml(badge.label)}</span></td>
                        <td>${escapeHtml(riderName)}</td>
                        <td style="color: #fce206; font-weight: bold;">${escapeHtml(formatDateTime(o.createdAt))}</td>
                        <td>
                            <button type="button" class="track-btn" data-order-id="${escapeHtml(o.id)}">Track</button>
                            ${cancelBtnHtml}
                        </td>
                    </tr>
                `;
            }).join('');
        };

        const openTrackModal = () => {
            if (!trackModal) return;
            trackModal.hidden = false;
            trackModal.classList.add('is-active');
            document.body.classList.add('modal-open');
        };

        const closeTrackModalFn = () => {
            if (!trackModal) return;
            trackModal.classList.remove('is-active');
            trackModal.hidden = true;
            document.body.classList.remove('modal-open');
            activeOrderId = null;
        };

        const timelineSteps = [
            { key: 'placedAt', label: 'Preparing' },
            { key: 'acceptedAt', label: 'Accepted by rider' },
            { key: 'pickedUpAt', label: 'Picked up' },
            { key: 'inTransitAt', label: 'In transit' },
            { key: 'arrivedAt', label: 'Arrived' },
            { key: 'completedAt', label: 'Completed' },
        ];

        const statusToStep = (statusRaw) => {
            const s = normalizeStatus(statusRaw);
            if (s === 'completed' || s === 'done' || s === 'delivered') return 6;
            if (s === 'arrived') return 5;
            if (s === 'in transit' || s === 'in-transit' || s === 'transit') return 4;
            if (s === 'picked up' || s === 'picked-up') return 3;
            if (s === 'accepted') return 2;
            if (s === 'cancelled' || s === 'canceled') return 0;
            return 1;
        };

        const renderTrackModal = (order) => {
            if (!order) return;

            const badge = statusBadge(order.status);
            const riderName = order?.rider?.name ? String(order.rider.name) : 'Not assigned yet';
            const riderPhone = order?.rider?.phone ? ` (${order.rider.phone})` : '';

            trackOrderId.textContent = order.id;
            trackStatus.innerHTML = `<span class="status ${badge.cls}">${escapeHtml(badge.label)}</span>`;
            trackRider.textContent = riderName;
            trackRiderPhone.textContent = order?.rider?.phone || '—';

            const receiptRows = (order.items || []).map((i) => {
                const qty = Number(i.qty) || 0;
                const unit = Number(i.price) || 0;
                const line = qty * unit;
                return `
                    <div class="receipt-row">
                        <div class="receipt-name">${escapeHtml(i.name)}</div>
                        <div class="receipt-meta">${qty} × ${escapeHtml(formatCurrency(unit))}</div>
                        <div class="receipt-total">${escapeHtml(formatCurrency(line))}</div>
                    </div>
                `;
            }).join('');

            trackReceipt.innerHTML = `
                <div class="receipt-table">
                    ${receiptRows || '<div class="empty-receipt">No items.</div>'}
                </div>
                <div class="receipt-summary">
                    <div class="receipt-summary-row"><span>Total Items</span><strong>${escapeHtml(String(order.totalItems || 0))}</strong></div>
                    <div class="receipt-summary-row"><span>Total Price</span><strong>${escapeHtml(formatCurrency(order.totalPrice))}</strong></div>
                </div>
            `;

            const currentStep = statusToStep(order.status);
            const stamps = order.statusTimestamps || {};

            trackTimeline.innerHTML = timelineSteps.map((step, idx) => {
                const isDone = currentStep >= idx + 1;
                const ts = stamps[step.key] ? formatDateTime(stamps[step.key]) : '';
                return `
                    <li class="timeline-item ${isDone ? 'is-done' : ''}">
                        <div class="timeline-title">${escapeHtml(step.label)}</div>
                        <div class="timeline-time">${escapeHtml(ts)}</div>
                    </li>
                `;
            }).join('');
        };

        const refreshOrders = async () => {
            try {
                const response = await fetch("{{ route('api.orders.my') }}");
                const all = await response.json();

                window.cachedOrders = all;

                const current = all.filter((o) => {
                    const s = normalizeStatus(o.status);
                    return s !== 'completed' && s !== 'done' && s !== 'delivered' && s !== 'cancelled' && s !== 'canceled';
                });

                const previous = all.filter((o) => !current.includes(o));

                // Handle Sorting
                const sortVal = document.getElementById('dateSort').value;
                previous.sort((a, b) => {
                    const dateA = new Date(a.createdAt || 0);
                    const dateB = new Date(b.createdAt || 0);
                    return sortVal === 'newest' ? dateB - dateA : dateA - dateB;
                });

                sortedPreviousOrders = previous;

                if (currentOrdersEmpty) currentOrdersEmpty.hidden = current.length > 0;
                if (previousOrdersEmpty) previousOrdersEmpty.hidden = previous.length > 0;

                if (currentOrdersBody) renderTable(current, currentOrdersBody, true);

                renderPreviousOrdersPage();

                if (activeOrderId) {
                    const order = all.find((o) => String(o.id) === String(activeOrderId));
                    if (order) renderTrackModal(order);
                }
            } catch (err) {
                console.error('Failed to load orders', err);
            }
        };

        const renderPreviousOrdersPage = () => {
            const totalPages = Math.ceil(sortedPreviousOrders.length / prevItemsPerPage) || 1;
            if (currentPrevPage > totalPages) currentPrevPage = totalPages;

            const start = (currentPrevPage - 1) * prevItemsPerPage;
            const end = start + prevItemsPerPage;
            const paginated = sortedPreviousOrders.slice(start, end);

            renderTable(paginated, previousOrdersBody);

            const paginationContainer = document.getElementById('prevOrdersPagination');
            if (sortedPreviousOrders.length > prevItemsPerPage) {
                paginationContainer.style.display = 'flex';
                document.getElementById('prevOrdersPageInfo').textContent = `Page ${currentPrevPage} of ${totalPages}`;
                document.getElementById('prevOrdersPrev').disabled = currentPrevPage === 1;
                document.getElementById('prevOrdersNext').disabled = currentPrevPage === totalPages;

                // Apply opacity for disabled state
                document.getElementById('prevOrdersPrev').style.opacity = currentPrevPage === 1 ? '0.3' : '1';
                document.getElementById('prevOrdersNext').style.opacity = currentPrevPage === totalPages ? '0.3' : '1';
            } else {
                paginationContainer.style.display = 'none';
            }
        };

        document.getElementById('dateSort').addEventListener('change', () => {
            currentPrevPage = 1;
            refreshOrders();
        });

        document.getElementById('prevOrdersPrev').addEventListener('click', () => {
            if (currentPrevPage > 1) {
                currentPrevPage--;
                renderPreviousOrdersPage();
            }
        });

        document.getElementById('prevOrdersNext').addEventListener('click', () => {
            const totalPages = Math.ceil(sortedPreviousOrders.length / prevItemsPerPage);
            if (currentPrevPage < totalPages) {
                currentPrevPage++;
                renderPreviousOrdersPage();
            }
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.track-btn');
            if (!btn) return;
            const orderId = btn.getAttribute('data-order-id');
            if (!orderId) return;

            activeOrderId = orderId;
            const all = window.cachedOrders || [];
            const order = all.find((o) => String(o.id) === String(orderId));
            if (!order) return;

            renderTrackModal(order);
            openTrackModal();
        });

        let pendingCancelOrderDbId = null;
        let pendingCancelBtn = null;

        window.closeCancelModal = () => {
            const modal = document.getElementById('cancelConfirmModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.hidden = true;
                document.body.classList.remove('modal-open');
            }
            pendingCancelOrderDbId = null;
            pendingCancelBtn = null;
        };

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.cancel-btn');
            // Ignore the buttons inside the modal itself
            if (!btn || btn.id === 'confirmCancelProceedBtn' || btn.textContent === 'No, Keep it') return;

            const orderDbId = btn.getAttribute('data-order-dbid');
            const orderId = btn.getAttribute('data-order-id');
            if (!orderDbId) return;

            pendingCancelOrderDbId = orderDbId;
            pendingCancelBtn = btn;

            const modal = document.getElementById('cancelConfirmModal');
            const textSpan = document.getElementById('cancelOrderIdText');
            if (modal && textSpan) {
                textSpan.textContent = orderId;
                modal.hidden = false;
                modal.classList.add('is-active');
                document.body.classList.add('modal-open');
            }
        });

        const proceedBtn = document.getElementById('confirmCancelProceedBtn');
        if (proceedBtn) {
            proceedBtn.addEventListener('click', async () => {
                if (!pendingCancelOrderDbId || !pendingCancelBtn) return;
                
                const btn = pendingCancelBtn;
                const orderDbId = pendingCancelOrderDbId;
                
                closeCancelModal();

                btn.disabled = true;
                btn.textContent = 'Cancelling...';

                try {
                    const response = await fetch(`/api/orders/${orderDbId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });

                    const result = await response.json();
                    if (result.success) {
                        refreshOrders();
                    } else {
                        alert('Could not cancel order: ' + (result.message || 'Unknown error'));
                        btn.disabled = false;
                        btn.textContent = 'Cancel';
                    }
                } catch (err) {
                    alert('Connection error');
                    btn.disabled = false;
                    btn.textContent = 'Cancel';
                }
            });
        }

        if (closeTrackModal) closeTrackModal.addEventListener('click', closeTrackModalFn);
        if (trackModal) {
            trackModal.addEventListener('click', (e) => {
                if (e.target === trackModal) closeTrackModalFn();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && trackModal && !trackModal.hidden) closeTrackModalFn();
        });



        refreshOrders();
        refreshTimer = setInterval(refreshOrders, 1500);
    </script>
    @include('partials.logout-modal')
</body>

</html>