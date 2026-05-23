<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krusty Krab - Cart</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
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
            <li><a href="{{ route('customer.cart') }}" class="active"><i class="bx bx-cart"></i> Cart</a></li>
            <li><a href="{{ route('customer.orders') }}"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('customer.profile') }}"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="page-header">
            <h1>Cart</h1>
            <p>Review your selected items before checkout.</p>
        </header>

        <section class="cart-layout">
            <div class="cart-items" id="cartItems"></div>

            <aside class="cart-summary">
                <h2>Order Summary</h2>
                <div class="summary-row">
                    <span>Total Items</span>
                    <span id="totalItems">0</span>
                </div>
                <div class="summary-row total">
                    <span>Total Price</span>
                    <span id="totalPrice">₱0.00</span>
                </div>
                <button type="button" class="checkout-btn" id="checkoutBtn">Checkout</button>
            </aside>
        </section>
    </main>

    @php
        $sessionCustomer = [
            'id' => optional(session('user'))->id,
            'name' => optional(session('user'))->name,
            'email' => optional(session('user'))->email,
            'phone' => optional(session('user'))->phone,
            'address' => optional(session('user'))->address,
        ];
    @endphp

    <div class="modal-overlay" id="checkoutModal" hidden>
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="checkoutTitle">
            <div class="modal-header">
                <h2 id="checkoutTitle">Checkout</h2>
                <button type="button" class="modal-close" id="closeCheckoutModal" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">
                <section class="modal-section">
                    <h3>[ Customer's Details ]</h3>
                    <div class="details-grid">
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ optional(session('user'))->name ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">{{ optional(session('user'))->email ?? '—' }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">{{ optional(session('user'))->phone ?? '—' }}</span>
                        </div>
                        <div class="detail-row detail-row--full">
                            <span class="detail-label">Address</span>
                            <span class="detail-value">{{ optional(session('user'))->address ?? '—' }}</span>
                        </div>
                    </div>
                </section>

                <hr class="modal-divider">

                <section class="modal-section">
                    <h3>[ Receipt of their Order ]</h3>
                    <div class="receipt" id="receiptBody"></div>
                </section>

                <hr class="modal-divider">

                <section class="modal-section">
                    <h3>[ Mode of Payment : Cash on Delivery ]</h3>
                </section>
            </div>

            <div class="modal-actions">
                <div id="checkoutWarning"
                    style="color: #ff5555; font-size: 0.9rem; margin-bottom: 10px; width: 100%; text-align: left; display: none;">
                    <i class='bx bx-error-circle'></i> Please complete your <strong>Phone</strong> and
                    <strong>Address</strong> in your <a href="{{ route('customer.profile') }}"
                        style="color: #fce206; text-decoration: underline;">Profile</a> before placing an order.
                </div>
                <button type="button" class="btn-secondary" id="cancelCheckout">Cancel</button>
                <button type="button" class="btn-primary" id="placeOrderBtn">Place Order</button>
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



        const cartItemsContainer = document.getElementById('cartItems');
        const totalItemsEl = document.getElementById('totalItems');
        const totalPriceEl = document.getElementById('totalPrice');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const checkoutModal = document.getElementById('checkoutModal');
        const closeCheckoutModalBtn = document.getElementById('closeCheckoutModal');
        const cancelCheckoutBtn = document.getElementById('cancelCheckout');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        const receiptBody = document.getElementById('receiptBody');
        const STORAGE_KEY = 'kk_cart_items';
        const CURRENCY = '\u20B1';
        const ORDERS_STORAGE_KEY = 'kk_orders_v1';
        const SESSION_CUSTOMER = @json($sessionCustomer);

        function readCart() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function normalizeCartItems(items) {
            if (!Array.isArray(items)) return [];

            return items
                .map((item) => {
                    if (!item || typeof item !== 'object') return null;
                    const name = String(item.name || item.id || '').trim();
                    if (!name) return null;
                    return {
                        id: String(item.id || name),
                        name,
                        price: Number(item.price) || 0,
                        image: String(item.image || ''),
                        qty: Math.max(1, parseInt(item.qty, 10) || 1),
                        selected: item.selected !== false
                    };
                })
                .filter(Boolean);
        }

        let cartItems = normalizeCartItems(readCart());

        function writeCart(items) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
            } catch (e) {
                // ignore
            }
        }

        function readOrders() {
            try {
                const raw = localStorage.getItem(ORDERS_STORAGE_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function writeOrders(orders) {
            try {
                localStorage.setItem(ORDERS_STORAGE_KEY, JSON.stringify(orders));
            } catch (e) {
                // ignore
            }
        }

        function updateTotals() {
            const selectedItems = cartItems.filter(item => item.selected !== false);
            const totalItems = selectedItems.reduce((sum, item) => sum + (item.qty || 0), 0);
            const totalPrice = selectedItems.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);
            totalItemsEl.textContent = String(totalItems);
            totalPriceEl.textContent = `${CURRENCY}${totalPrice.toFixed(2)}`;

            if (checkoutBtn) checkoutBtn.disabled = totalItems === 0;
        }

        function renderEmptyState() {
            cartItemsContainer.innerHTML = `
                <div class="cart-item" style="justify-content: center; text-align: center;">
                    <div class="item-info">
                        <h3>Your cart is empty</h3>
                        <p class="item-price">Go to Menu to add items.</p>
                    </div>
                </div>
            `;
        }

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderCart() {
            if (!cartItemsContainer) return;

            if (!cartItems.length) {
                renderEmptyState();
                updateTotals();
                return;
            }

            cartItemsContainer.innerHTML = cartItems
                .map((item) => {
                    const safeName = escapeHtml(item.name);
                    const img = item.image ? item.image : `{{ asset('images/logo.png') }}`;
                    const isChecked = item.selected !== false ? 'checked' : '';
                    return `
                        <div class="cart-item" data-id="${safeName}" data-price="${Number(item.price) || 0}">
                            <input type="checkbox" class="item-checkbox" ${isChecked} style="margin-right: 15px; transform: scale(1.5); cursor: pointer;">
                            <img src="${img}" alt="${safeName}">
                            <div class="item-info">
                                <h3>${safeName}</h3>
                                <p class="item-price">${CURRENCY}${(Number(item.price) || 0).toFixed(2)}</p>
                            </div>
                            <div class="quantity-control">
                                <button type="button" class="qty-btn minus">-</button>
                                <span class="qty">${item.qty}</span>
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="button" class="remove-btn">Remove</button>
                        </div>
                    `;
                })
                .join('');

            updateTotals();
        }

        function openCheckoutModal() {
            if (!checkoutModal) return;
            checkoutModal.hidden = false;
            checkoutModal.classList.add('is-active');
            document.body.classList.add('modal-open');
        }

        function closeCheckoutModal() {
            if (!checkoutModal) return;
            checkoutModal.classList.remove('is-active');
            checkoutModal.hidden = true;
            document.body.classList.remove('modal-open');

            // Reset warning
            const warningEl = document.getElementById('checkoutWarning');
            if (warningEl) warningEl.style.display = 'none';
            if (placeOrderBtn) {
                placeOrderBtn.disabled = false;
                placeOrderBtn.style.opacity = '1';
                placeOrderBtn.style.cursor = 'pointer';
            }
        }

        function renderReceipt() {
            if (!receiptBody) return;

            const selectedItems = cartItems.filter(item => item.selected !== false);

            if (!selectedItems.length) {
                receiptBody.innerHTML = '<p style="opacity:0.85;">No items selected.</p>';
                return;
            }

            const rows = selectedItems.map((item) => {
                const qty = Number(item.qty) || 0;
                const unit = Number(item.price) || 0;
                const lineTotal = unit * qty;
                const safeName = escapeHtml(item.name);
                return `
                    <div class="receipt-row">
                        <div class="receipt-name">${safeName}</div>
                        <div class="receipt-meta">${qty} × ${CURRENCY}${unit.toFixed(2)}</div>
                        <div class="receipt-total">${CURRENCY}${lineTotal.toFixed(2)}</div>
                    </div>
                `;
            }).join('');

            const totalItems = selectedItems.reduce((sum, item) => sum + (item.qty || 0), 0);
            const totalPrice = selectedItems.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);

            receiptBody.innerHTML = `
                <div class="receipt-table">
                    ${rows}
                </div>
                <div class="receipt-summary">
                    <div class="receipt-summary-row">
                        <span>Total Items</span>
                        <strong>${totalItems}</strong>
                    </div>
                    <div class="receipt-summary-row">
                        <span>Total Price</span>
                        <strong>${CURRENCY}${totalPrice.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        }

        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                const selectedItems = cartItems.filter(item => item.selected !== false);
                if (!selectedItems.length) return;
                renderReceipt();
                openCheckoutModal();

                // Check for profile completeness
                const hasPhone = SESSION_CUSTOMER.phone && SESSION_CUSTOMER.phone.trim() !== '';
                const hasAddress = SESSION_CUSTOMER.address && SESSION_CUSTOMER.address.trim() !== '';

                if (!hasPhone || !hasAddress) {
                    const warningEl = document.getElementById('checkoutWarning');
                    if (warningEl) warningEl.style.display = 'block';
                    if (placeOrderBtn) {
                        placeOrderBtn.disabled = true;
                        placeOrderBtn.style.opacity = '0.5';
                        placeOrderBtn.style.cursor = 'not-allowed';
                    }
                }
            });
        }

        if (closeCheckoutModalBtn) closeCheckoutModalBtn.addEventListener('click', closeCheckoutModal);
        if (cancelCheckoutBtn) cancelCheckoutBtn.addEventListener('click', closeCheckoutModal);

        if (checkoutModal) {
            checkoutModal.addEventListener('click', (e) => {
                if (e.target === checkoutModal) closeCheckoutModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && checkoutModal && !checkoutModal.hidden) closeCheckoutModal();
        });

        if (placeOrderBtn) {
            placeOrderBtn.addEventListener('click', async () => {
                const selectedItems = cartItems.filter(item => item.selected !== false);
                if (!selectedItems.length) return;
                placeOrderBtn.disabled = true;
                placeOrderBtn.innerText = 'Processing...';

                const totalPrice = selectedItems.reduce((sum, item) => sum + (item.price || 0) * (item.qty || 0), 0);
                const totalItems = selectedItems.reduce((sum, item) => sum + (item.qty || 0), 0);

                const orderData = {
                    _token: '{{ csrf_token() }}',
                    order_id: `ORD-${Date.now()}`,
                    total_items: totalItems,
                    total_price: Number(totalPrice.toFixed(2)),
                    payment_method: 'Cash on Delivery',
                    items: selectedItems.map((i) => ({
                        name: i.name,
                        price: i.price,
                        qty: i.qty,
                        image: i.image
                    }))
                };

                try {
                    const response = await fetch("{{ route('checkout') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(orderData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        cartItems = cartItems.filter(item => item.selected === false);
                        writeCart(cartItems);
                        renderCart();
                        closeCheckoutModal();
                        window.location.href = "{{ route('customer.orders') }}";
                    } else {
                        alert('Failed to place order: ' + (result.message || 'Unknown error.'));
                        placeOrderBtn.disabled = false;
                        placeOrderBtn.innerText = 'Place Order';
                    }
                } catch (error) {
                    alert('Error placing order. Check your internet connection.');
                    console.error(error);
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerText = 'Place Order';
                }
            });
        }

        cartItemsContainer.addEventListener('change', (event) => {
            if (event.target.classList.contains('item-checkbox')) {
                const itemEl = event.target.closest('.cart-item');
                if (!itemEl) return;

                const itemName = itemEl.querySelector('h3')?.textContent || '';
                const index = cartItems.findIndex((i) => String(i.name) === String(itemName));
                if (index > -1) {
                    cartItems[index].selected = event.target.checked;
                    writeCart(cartItems);
                    updateTotals();
                }
            }
        });

        cartItemsContainer.addEventListener('click', (event) => {
            const button = event.target.closest('button');
            if (!button) return;

            const itemEl = button.closest('.cart-item');
            if (!itemEl) return;

            const itemName = itemEl.querySelector('h3')?.textContent || '';
            const index = cartItems.findIndex((i) => String(i.name) === String(itemName));
            if (index < 0) return;

            if (button.classList.contains('plus')) {
                cartItems[index].qty += 1;
                const qtyEl = itemEl.querySelector('.qty');
                if (qtyEl) qtyEl.textContent = String(cartItems[index].qty);
            }

            if (button.classList.contains('minus') && cartItems[index].qty > 1) {
                cartItems[index].qty -= 1;
                const qtyEl = itemEl.querySelector('.qty');
                if (qtyEl) qtyEl.textContent = String(cartItems[index].qty);
            }

            if (button.classList.contains('remove-btn')) {
                cartItems.splice(index, 1);
                itemEl.remove();
            }

            writeCart(cartItems);
            if (!cartItems.length) renderCart();
            updateTotals();
        });

        renderCart();
        updateTotals();


    </script>
    @include('partials.logout-modal')
</body>

</html>