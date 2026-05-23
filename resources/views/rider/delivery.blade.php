<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider - Delivery</title>

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
            <li><a href="{{ route('rider.dashboard') }}"><i class="bx bx-basket"></i> Orders</a></li>
            <li><a href="{{ route('rider.delivery') }}" class="active"><i class="bx bx-cycling"></i> Delivery</a></li>
            <li><a href="{{ route('rider.profile') }}"><i class="bx bx-user"></i> Profile</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="workflow">
            <div id="multi-order-selector" style="display: none; margin-bottom: 20px; padding: 10px; background: #fff; border-radius: 8px; border: 2px solid #131312;">
                <label for="activeOrderDropdown" style="font-weight: bold; margin-right: 10px; color: black;">Select Active Order:</label>
                <select id="activeOrderDropdown" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 1rem; cursor: pointer;" onchange="switchActiveOrder(this.value)">
                </select>
            </div>
            <div class="stepper">
                <div class="step active" id="s1">Pickup</div>
                <div class="step" id="s2">In Transit</div>
                <div class="step" id="s3">Arrived</div>
                <div class="step" id="s4">Done</div>
            </div>

            <section class="content-section active" id="step1">
                <h2 style="margin-top:0">Delivery Checklist</h2>
                <p class="muted">Verify all items are in the bag.</p>
                <div class="info-card">
                    <strong style="font-size: 1.1rem;">Customer Details</strong><br>
                    <p style="margin: 8px 0; line-height: 1.4;">
                        Name: <span id="deliveryCustomerName">—</span><br>
                        Mobile: <span id="deliveryCustomerPhone">—</span><br>
                        Address: <span id="deliveryCustomerAddress">—</span>
                    </p>
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 12px 0;">
                    <strong style="font-size: 1.05rem;">Order</strong><br>
                    <div id="deliveryOrderItems"></div>
                </div>
            </section>

            <section class="content-section" id="step2">
                <h2 style="margin-top:0">Heading to Customer</h2>
                <p class="muted">Deliver to the following address:</p>
                <div class="info-card">
                    <strong style="font-size: 1.1rem;" id="deliveryCustomerName2">—</strong><br>
                    <p style="margin: 8px 0; line-height: 1.4;">
                        <span id="deliveryCustomerAddress2">—</span>
                    </p>
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 15px 0;">
                    <small class="note" id="deliveryOrderIdNote">Order ID: —</small>
                </div>
            </section>

            <section class="content-section" id="step3">
                <h2 style="margin-top:0">Photo Verification & Payment</h2>
                <p class="muted">Take a photo of the delivered items and collect cash.</p>

                <div class="info-card" style="margin-bottom: 20px;">
                    <strong style="display: block; margin-bottom: 10px;">Proof of Delivery</strong>
                    <div class="photo-upload-container">
                        <label for="delivery_photo" class="photo-upload-label">
                            <i class='bx bx-camera' style="font-size: 2rem;"></i>
                            <span>Tap to Take/Upload Photo</span>
                        </label>
                        <input type="file" id="delivery_photo" accept="image/*" capture="environment"
                            style="display: none;">
                        <div id="photo-preview" style="margin-top: 10px; display: none;">
                            <img src="" alt="Preview"
                                style="max-width: 100%; border-radius: 8px; border: 2px solid #611909;">
                        </div>
                    </div>
                </div>

                <div class="info-card" style="text-align: center; border: 2px dashed #f39c12; background: #fff9f0;">
                    <p style="margin-bottom: 0; font-weight: bold; color: #611909;">COLLECT TOTAL CASH</p>
                    <span class="cash-highlight" id="deliveryCashTotal">₱0.00</span>
                    <p style="font-size: 0.8rem; color: #777;">(Cash on Delivery)</p>
                </div>
            </section>

            <section class="content-section" id="step4">
                <div style="text-align: center; padding-top: 50px;">
                    <div style="font-size: 4rem;">✅</div>
                    <h1 style="color: #4caf50; margin-top: 10px;">Delivery Done!</h1>
                    <p class="muted">The order has been successfully processed and paid.</p>
                    <a href="{{ route('rider.dashboard') }}" id="finishDeliveryBtn" class="main-btn"
                        style="display: inline-block; text-align: center; background: #611909; color: #fff; margin-top: 30px;">Go
                        to Next Order</a>
                </div>
            </section>

            <footer class="workflow-footer" id="footer-nav">
                <div style="display: flex; gap: 12px; width: 100%;">
                    <button class="main-btn" id="nextBtn" onclick="nextStep()" style="flex: 1;">Confirm Pickup</button>
                    <button class="main-btn btn-danger" id="unacceptBtn" onclick="unacceptActiveOrder()" style="flex: 1; display: none;">Unaccept Order</button>
                </div>
            </footer>
        </div>
    </main>

    <div id="unacceptConfirmModal" class="modal" style="display: none;">
        <div class="modal-card" style="max-width: 480px; text-align: center; border: 3px solid #fce206; background: rgba(20, 12, 5, 0.98); color: white; box-shadow: 0 8px 32px rgba(0,0,0,0.5);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(252, 226, 6, 0.2); justify-content: center; padding-bottom: 12px; margin-bottom: 18px;">
                <h3 style="color: #fce206; margin: 0; font-size: 1.4rem;">Confirm Cancellation</h3>
            </div>
            <div class="modal-body" style="padding: 10px 0; margin-bottom: 24px;">
                <i class='bx bx-error-circle' style="font-size: 3.5rem; color: #d32f2f; margin-bottom: 12px; display: block;"></i>
                <p style="font-size: 1.1rem; line-height: 1.5; color: #fff;">Are you sure you want to unaccept/cancel this delivery?</p>
                <p style="font-size: 0.9rem; margin-top: 8px; color: rgba(255, 255, 255, 0.75);">The order will go back to the available queue.</p>
            </div>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" id="confirmUnacceptCancelBtn" class="main-btn" style="background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;" onclick="closeUnacceptModal()">No, Keep it</button>
                <button type="button" id="confirmUnacceptProceedBtn" class="main-btn btn-danger" style="padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;" onclick="proceedWithUnaccept()">Yes, Cancel</button>
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


        let currentStep = 1;
        const MULTI_LOCK_KEY = 'rider_active_orders';
        const LOCK_KEY = 'rider_active_order';
        const ORDERS_STORAGE_KEY = 'kk_orders_v1';

        const customerNameEl = document.getElementById('deliveryCustomerName');
        const customerPhoneEl = document.getElementById('deliveryCustomerPhone');
        const customerAddressEl = document.getElementById('deliveryCustomerAddress');
        const customerName2El = document.getElementById('deliveryCustomerName2');
        const customerAddress2El = document.getElementById('deliveryCustomerAddress2');
        const orderItemsEl = document.getElementById('deliveryOrderItems');
        const cashTotalEl = document.getElementById('deliveryCashTotal');
        const orderIdNoteEl = document.getElementById('deliveryOrderIdNote');

        const nextBtn = document.getElementById('nextBtn');
        const footer = document.getElementById('footer-nav');
        const finishDeliveryBtn = document.getElementById('finishDeliveryBtn');
        const photoInput = document.getElementById('delivery_photo');
        const photoPreview = document.getElementById('photo-preview');
        const photoPreviewImg = photoPreview ? photoPreview.querySelector('img') : null;

        if (photoInput) {
            photoInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        if (photoPreviewImg) photoPreviewImg.src = event.target.result;
                        if (photoPreview) photoPreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        const escapeHtml = (text) => String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#39;');

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

        const getActiveOrderId = () => {
            const arr = getActiveOrderIds();
            const curr = localStorage.getItem('rider_current_view');
            if (curr && arr.includes(curr)) return curr;
            return arr.length > 0 ? arr[0] : null;
        };

        const removeActiveOrderId = (orderId) => {
            if (!orderId) return;
            let arr = getActiveOrderIds();
            arr = arr.filter(id => String(id) !== String(orderId));
            localStorage.setItem(MULTI_LOCK_KEY, JSON.stringify(arr));
            
            if (localStorage.getItem('rider_current_view') === String(orderId)) {
                if (arr.length > 0) {
                    localStorage.setItem('rider_current_view', String(arr[0]));
                } else {
                    localStorage.removeItem('rider_current_view');
                }
            }
        };

        const clearActiveOrder = () => {
            const current = getActiveOrderId();
            removeActiveOrderId(current);
        };

        const formatCurrency = (amount) => `₱${Number(amount || 0).toFixed(2)}`;

        const setText = (el, value) => { if (el) el.textContent = value ?? '—'; };

        const setHtml = (el, html) => { if (el) el.innerHTML = html; };

        const setStepUi = (step) => {
            for (let i = 1; i <= 4; i += 1) {
                const section = document.getElementById(`step${i}`);
                const stepper = document.getElementById(`s${i}`);
                if (section) section.classList.toggle('active', i === step);
                if (stepper) {
                    stepper.classList.toggle('active', i === step);
                    stepper.classList.toggle('completed', i < step);
                }
            }

            if (!nextBtn || !footer) return;
            nextBtn.classList.remove('btn-success');
            
            const unacceptBtn = document.getElementById('unacceptBtn');

            if (step === 1) {
                nextBtn.innerText = "Confirm Pickup";
                if (unacceptBtn) unacceptBtn.style.display = 'block';
                footer.style.display = 'flex';
            } else if (step === 2) {
                nextBtn.innerText = "Arrived at Destination";
                if (unacceptBtn) unacceptBtn.style.display = 'none';
                footer.style.display = 'flex';
            } else if (step === 3) {
                nextBtn.innerText = "Confirm Cash & Complete";
                nextBtn.classList.add('btn-success');
                if (unacceptBtn) unacceptBtn.style.display = 'none';
                footer.style.display = 'flex';
            } else {
                footer.style.display = 'none';
            }

            if (nextBtn) {
                nextBtn.disabled = false; // Re-enable the button after updating UI
            }
        };

        let activeDbId = null;

        const renderForOrder = (order) => {
            const cust = order.customer || {};
            const items = Array.isArray(order.items) ? order.items : [];

            setText(customerNameEl, cust.name || 'Customer');
            setText(customerPhoneEl, cust.phone || '—');
            setText(customerAddressEl, cust.address || '—');
            setText(customerName2El, cust.name || 'Customer');
            setText(customerAddress2El, cust.address || '—');
            if (orderIdNoteEl) setText(orderIdNoteEl, `Order ID: ${order.id || '—'}`);

            setHtml(orderItemsEl, items.map((i) => {
                const qty = Number(i.qty) || 0;
                const name = escapeHtml(i.name);
                return `<p style="margin: 6px 0;">${qty}x ${name}</p>`;
            }).join('') || '<p style="margin: 6px 0;">—</p>');

            const totalPrice = items.reduce((sum, i) => sum + (Number(i.price) || 0) * (Number(i.qty) || 0), 0);
            setText(cashTotalEl, formatCurrency(totalPrice));
        };

        const init = async () => {
            const activeIds = getActiveOrderIds();
            if (activeIds.length === 0) {
                const workflow = document.querySelector('.workflow');
                if (workflow) {
                    workflow.innerHTML = `
                    <div class="recent-orders" style="text-align:center;">
                        <h2 style="margin:0 0 8px;">No active delivery</h2>
                        <p class="muted" style="opacity:0.85;">Accept an order first from the Orders tab.</p>
                        <a href="{{ route('rider.dashboard') }}" class="main-btn" style="display:inline-block; margin-top:14px; background:#fce206; color:#611909;">Go to Orders</a>
                    </div>
                `;
                }
                if (footer) footer.style.display = 'none';
                return;
            }

            try {
                const response = await fetch("{{ route('api.orders.available') }}");
                const allOrders = await response.json();

                const multiSelector = document.getElementById('multi-order-selector');
                const dropdown = document.getElementById('activeOrderDropdown');
                
                if (activeIds.length > 1) {
                    multiSelector.style.display = 'block';
                    dropdown.innerHTML = '';
                    activeIds.forEach(id => {
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.text = 'Order #' + id;
                        dropdown.appendChild(opt);
                    });
                } else {
                    multiSelector.style.display = 'none';
                }

                let activeId = getActiveOrderId();
                if (!activeId) return;

                if (dropdown) dropdown.value = activeId;

                const order = allOrders.find((o) => String(o.id) === String(activeId));

                if (!order) {
                    removeActiveOrderId(activeId);
                    init();
                    return;
                }

                activeDbId = order.db_id;
                renderForOrder(order);
                currentStep = Math.min(4, Math.max(1, parseInt(order.deliveryStep, 10) || 1));
                setStepUi(currentStep);
            } catch (err) {
                console.error(err);
            }
        };

        async function nextStep() {
            const activeId = getActiveOrderId();
            if (!activeId || !activeDbId) return;

            let newStatus = '';
            let stepNum = 0;

            if (currentStep === 1) {
                newStatus = 'In Transit';
                stepNum = 2;
            } else if (currentStep === 2) {
                newStatus = 'Arrived';
                stepNum = 3;
            } else if (currentStep === 3) {
                newStatus = 'Completed';
                stepNum = 4;
            } else {
                return;
            }

            if (nextBtn) {
                nextBtn.disabled = true;
                nextBtn.innerText = 'Updating...';
            }

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', newStatus);
                formData.append('delivery_step', stepNum);

                if (currentStep === 3 && photoInput && photoInput.files[0]) {
                    formData.append('delivery_photo', photoInput.files[0]);
                } else if (currentStep === 3) {
                    alert('Please take a photo proof of delivery first.');
                    if (nextBtn) {
                        nextBtn.disabled = false;
                        nextBtn.innerText = 'Confirm Cash & Complete';
                    }
                    return;
                }

                const response = await fetch(`/api/orders/${activeDbId}/status`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    currentStep = stepNum;
                    setStepUi(currentStep);
                    if (currentStep === 4) {
                        clearActiveOrder();
                    }
                } else {
                    alert('Failed to update status');
                    setStepUi(currentStep);
                }
            } catch (err) {
                alert('Connection error');
                setStepUi(currentStep);
            }
        }

        function unacceptActiveOrder() {
            const modal = document.getElementById('unacceptConfirmModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeUnacceptModal() {
            const modal = document.getElementById('unacceptConfirmModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        async function proceedWithUnaccept() {
            const activeId = getActiveOrderId();
            if (!activeId || !activeDbId) return;

            const proceedBtn = document.getElementById('confirmUnacceptProceedBtn');
            const cancelBtn = document.getElementById('confirmUnacceptCancelBtn');
            if (proceedBtn) {
                proceedBtn.disabled = true;
                proceedBtn.innerText = 'Cancelling...';
            }
            if (cancelBtn) cancelBtn.disabled = true;

            try {
                const response = await fetch(`/api/orders/${activeDbId}/unaccept`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                });

                const result = await response.json();
                if (result.success) {
                    clearActiveOrder();
                    window.location.href = "{{ route('rider.dashboard') }}";
                } else {
                    alert('Failed to unaccept order: ' + (result.message || 'Unknown error'));
                    closeUnacceptModal();
                    if (proceedBtn) {
                        proceedBtn.disabled = false;
                        proceedBtn.innerText = 'Yes, Cancel';
                    }
                    if (cancelBtn) cancelBtn.disabled = false;
                }
            } catch (err) {
                alert('Connection error');
                closeUnacceptModal();
                if (proceedBtn) {
                    proceedBtn.disabled = false;
                    proceedBtn.innerText = 'Yes, Cancel';
                }
                if (cancelBtn) cancelBtn.disabled = false;
            }
        }

        if (finishDeliveryBtn) {
            finishDeliveryBtn.addEventListener('click', function (e) {
                // If there are other orders, prevent going to dashboard and just refresh
                const activeIds = getActiveOrderIds();
                if (activeIds.length > 0) {
                    e.preventDefault();
                    init();
                }
            });
        }

        window.switchActiveOrder = (orderId) => {
            localStorage.setItem('rider_current_view', orderId);
            init();
        };

        init();
    </script>

    @include('partials.logout-modal')
</body>

</html>