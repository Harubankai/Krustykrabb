<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krusty Krab - Menu</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/menu.css') }}">
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
            <li><a href="{{ route('customer.dashboard') }}" class="active"><i class="bx bx-store"></i> Menu</a></li>
            <li><a href="{{ route('customer.cart') }}" id="cartLink"><i class="bx bx-cart"></i> Cart <span
                        class="cart-badge" id="cartBadge" hidden></span></a></li>
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
            <h1>Krusty Krab Menu</h1>
            <p>Browse and order your favorite food from Bikini Bottom.</p>
        </header>

        <div id="cartToast" class="cart-toast" role="status" aria-live="polite" aria-atomic="true" hidden>
            <div class="cart-toast__content">
                <div class="cart-toast__text">
                    <strong id="cartToastTitle">Added to cart</strong>
                    <span id="cartToastMsg">Check your cart to checkout.</span>
                </div>
                <a class="cart-toast__link" href="{{ route('customer.cart') }}">View Cart</a>
                <button type="button" class="cart-toast__close" aria-label="Close">&times;</button>
            </div>
        </div>

        <section class="menu-controls">
            <input type="text" id="searchInput" class="search-input" placeholder="Search food name..."
                aria-label="Search food">
            <select id="categoryFilter" class="category-filter" aria-label="Filter by category">
                <option value="all">All Categories</option>
                <option value="meal">Meal</option>
                <option value="drinks">Drinks</option>
            </select>
        </section>

        <section class="food-grid" id="foodGrid"></section>
    </main>

    <!-- Item Detail Modal -->
    <div id="itemDetailModal" class="modal-overlay" hidden>
        <div class="modal-content">
            <div class="modal-header">Menu Item Details</div>
            <button type="button" class="modal-close" id="closeDetailModal" aria-label="Close">&times;</button>
            <div class="modal-body" style="overflow-y: auto; flex: 1;">
                <div class="modal-image">
                    <img id="modalItemImage" src="" alt="Item Image">
                </div>
                <div class="modal-info">
                    <span id="modalItemCategory" class="food-category"></span>
                    <h2 id="modalItemName"></h2>
                    <p id="modalItemDescription" class="food-description"></p>
                    <div class="modal-footer">
                        <span id="modalItemPrice" class="food-price"></span>
                        <button type="button" id="modalAddToCartBtn" class="add-btn">Add to Cart</button>
                    </div>
                </div>
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



        const MENU_STORAGE_KEY = 'kk_menu_items_v1';
        const DEFAULT_IMAGE_URL = "{{ asset('images/coralbits.jpg') }}";

        const DEFAULT_MENU_ITEMS = [
            {
                id: 'krabby-patty',
                name: 'Krabby Patty',
                category: 'meal',
                description: 'The legendary signature burger of the Krusty Krab',
                price: 45,
                image: "{{ asset('images/krabpat.jpg') }}",
            },
            {
                id: 'double-krabby-patty',
                name: 'Double Krabby Patty',
                category: 'meal',
                description: 'Two juicy patties stacked with sea-fresh toppings.',
                price: 55,
                image: "{{ asset('images/double_kp.jpg') }}",
            },
            {
                id: 'triple-krabby-patty',
                name: 'Triple Krabby Patty',
                category: 'meal',
                description: 'Three layers of the legendary Krabby Patty flavor.',
                price: 65,
                image: "{{ asset('images/triple_kp.jpg') }}",
            },
            {
                id: 'coral-bits-small',
                name: 'Coral Bits (Small)',
                category: 'meal',
                description: 'Crispy bite-sized coral bits, perfect snack size.',
                price: 30,
                image: "{{ asset('images/small_cb.png') }}",
            },
            {
                id: 'coral-bits-medium',
                name: 'Coral Bits (Medium)',
                category: 'meal',
                description: 'Golden coral bits in a medium sharing-size basket.',
                price: 40,
                image: "{{ asset('images/medium_cb.png') }}",
            },
            {
                id: 'coral-bits-large',
                name: 'Coral Bits (Large)',
                category: 'meal',
                description: 'Large crispy coral bits for the hungriest customers.',
                price: 50,
                image: "{{ asset('images/large_cb.png') }}",
            },
            {
                id: 'kelp-shake-small',
                name: 'Kelp Shake (Small)',
                category: 'drinks',
                description: 'Small-size kelp shake for quick refreshment.',
                price: 45,
                image: "{{ asset('images/kelpshake.jpg') }}",
            },
            {
                id: 'kelp-shake-medium',
                name: 'Kelp Shake (Medium)',
                category: 'drinks',
                description: 'Medium-size kelp shake to keep you going.',
                price: 50,
                image: "{{ asset('images/kelpshake.jpg') }}",
            },
            {
                id: 'kelp-shake-large',
                name: 'Kelp Shake (Large)',
                category: 'drinks',
                description: 'Big-size kelp shake for maximum refreshment.',
                price: 55,
                image: "{{ asset('images/kelpshake.jpg') }}",
            },
            {
                id: 'seafoam-soda-small',
                name: 'Seafoam Soda (Small)',
                category: 'drinks',
                description: 'Fizzing seafoam soda in a small chilled cup.',
                price: 35,
                image: "{{ asset('images/seafoam.jpg') }}",
            },
            {
                id: 'seafoam-soda-medium',
                name: 'Seafoam Soda (Medium)',
                category: 'drinks',
                description: 'Classic bubbly seafoam soda for everyday meals.',
                price: 40,
                image: "{{ asset('images/seafoam.jpg') }}",
            },
            {
                id: 'seafoam-soda-large',
                name: 'Seafoam Soda (Large)',
                category: 'drinks',
                description: 'Large sparkling seafoam soda to complete your order.',
                price: 45,
                image: "{{ asset('images/seafoam.jpg') }}",
            },
        ];

        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const foodGrid = document.getElementById('foodGrid');
        const cartToast = document.getElementById('cartToast');
        const cartToastTitle = document.getElementById('cartToastTitle');
        const cartToastMsg = document.getElementById('cartToastMsg');
        const cartBadge = document.getElementById('cartBadge');
        const cartLink = document.getElementById('cartLink');
        let toastTimer = null;
        let inMemoryCart = [];
        let menuItems = [];
        let cards = [];

        function parsePriceToNumber(priceText) {
            if (!priceText) return 0;
            const match = String(priceText).replace(',', '').match(/(\d+(\.\d+)?)/);
            return match ? parseFloat(match[1]) : 0;
        }

        function formatPrice(price) {
            return `\u20B1${Number(price || 0).toFixed(2)}`;
        }

        function renderMenu(items) {
            foodGrid.innerHTML = '';
            if (!items || items.length === 0) {
                foodGrid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; padding: 2em; color: #888;">No menu items currently available.</p>';
                return;
            }

            items.forEach((item) => {
                const card = document.createElement('div');
                card.className = 'food-card';
                card.dataset.name = item.name;
                card.dataset.category = item.category;

                const imageHtml = item.image ? `<img src="${item.image}" alt="${item.name}">` : '';

                card.innerHTML = `
                    ${imageHtml}
                    <div class="food-content">
                        <p class="food-category">${item.category}</p>
                        <h3>${item.name}</h3>
                        <p class="food-description">${item.description}</p>
                        <p class="food-price">${formatPrice(item.price)}</p>
                        <button type="button" class="add-btn">Add to Cart</button>
                    </div>
                `;
                foodGrid.appendChild(card);
            });

            cards = Array.from(document.querySelectorAll('.food-card'));
        }

        const itemDetailModal = document.getElementById('itemDetailModal');
        const closeDetailModal = document.getElementById('closeDetailModal');
        const modalItemImage = document.getElementById('modalItemImage');
        const modalItemCategory = document.getElementById('modalItemCategory');
        const modalItemName = document.getElementById('modalItemName');
        const modalItemDescription = document.getElementById('modalItemDescription');
        const modalItemPrice = document.getElementById('modalItemPrice');
        const modalAddToCartBtn = document.getElementById('modalAddToCartBtn');

        function openItemDetail(itemName) {
            const item = menuItems.find(i => i.name === itemName);
            if (!item) return;

            modalItemImage.src = item.image || DEFAULT_IMAGE_URL;
            modalItemCategory.textContent = item.category;
            modalItemName.textContent = item.name;
            modalItemDescription.textContent = item.description;
            modalItemPrice.textContent = formatPrice(item.price);

            // Set data for the add to cart button in modal
            modalAddToCartBtn.dataset.name = item.name;

            const cartItems = normalizeCartItems(readCart());
            const isInCart = cartItems.some(i => i.name === item.name);
            resetAddButton(modalAddToCartBtn);
            if (isInCart) {
                // optional: show different state if already in cart
            }

            itemDetailModal.hidden = false;
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeItemDetail() {
            itemDetailModal.hidden = true;
            document.body.style.overflow = '';
        }

        closeDetailModal.addEventListener('click', closeItemDetail);
        itemDetailModal.addEventListener('click', (e) => {
            if (e.target === itemDetailModal) closeItemDetail();
        });

        function cardToCartItem(card) {
            const name = card?.dataset?.name || '';
            const imgEl = card?.querySelector('img');
            const priceEl = card?.querySelector('.food-price');
            const price = parsePriceToNumber(priceEl?.textContent);

            return {
                id: name,
                name,
                price,
                image: imgEl?.getAttribute('src') || '',
                qty: 1,
            };
        }

        function readCart() {
            try {
                const raw = localStorage.getItem('kk_cart_items');
                const parsed = raw ? JSON.parse(raw) : [];
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return inMemoryCart;
            }
        }

        function normalizeCartItems(items) {
            if (!Array.isArray(items)) return [];

            if (items.length > 0 && typeof items[0] === 'string') {
                return items
                    .map((name) => {
                        const safeName = String(name || '').trim();
                        if (!safeName) return null;
                        const card = document.querySelector(`.food-card[data-name="${CSS.escape(safeName)}"]`);
                        return card ? cardToCartItem(card) : { id: safeName, name: safeName, price: 0, image: '', qty: 1 };
                    })
                    .filter(Boolean);
            }

            return items
                .map((item) => {
                    if (!item || typeof item !== 'object') return null;
                    const name = String(item.name || item.id || '').trim();
                    if (!name) return null;
                    return {
                        id: name,
                        name,
                        price: Number(item.price) || 0,
                        image: String(item.image || ''),
                        qty: Math.max(1, parseInt(item.qty, 10) || 1),
                    };
                })
                .filter(Boolean);
        }

        function writeCart(items) {
            try {
                localStorage.setItem('kk_cart_items', JSON.stringify(items));
            } catch (e) {
                inMemoryCart = items;
            }
        }

        function updateCartBadge() {
            const items = normalizeCartItems(readCart());
            const count = items.reduce((sum, item) => sum + (item.qty || 0), 0);
            if (!cartBadge) return;

            if (count > 0) {
                cartBadge.hidden = false;
                cartBadge.textContent = String(count);
                if (cartLink) cartLink.classList.add('has-items');
            } else {
                cartBadge.hidden = true;
                cartBadge.textContent = '';
                if (cartLink) cartLink.classList.remove('has-items');
            }
        }

        const buttonTimers = new WeakMap();

        function resetAddButton(button) {
            if (!button) return;
            button.classList.remove('is-added');
            button.textContent = 'Add to Cart';
            button.setAttribute('aria-pressed', 'false');
        }

        function flashAlreadyAdded(button, durationMs = 650) {
            if (!button) return;
            const existingTimer = buttonTimers.get(button);
            if (existingTimer) clearTimeout(existingTimer);

            button.classList.add('is-added');
            button.textContent = 'Already Added!';
            button.setAttribute('aria-pressed', 'true');

            const timer = setTimeout(() => {
                resetAddButton(button);
                buttonTimers.delete(button);
            }, durationMs);
            buttonTimers.set(button, timer);
        }

        function showToast(title, message) {
            if (!cartToast) return;
            cartToastTitle.textContent = title;
            cartToastMsg.textContent = message;
            cartToast.hidden = false;
            cartToast.classList.add('is-visible');

            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                cartToast.classList.remove('is-visible');
                cartToast.hidden = true;
            }, 4500);
        }

        function applyFilters() {
            const query = searchInput.value.toLowerCase().trim();
            const category = categoryFilter.value;

            cards.forEach((card) => {
                const itemName = card.dataset.name.toLowerCase();
                const itemCategory = card.dataset.category;
                const matchesSearch = itemName.includes(query);
                const matchesCategory = category === 'all' || itemCategory === category;

                card.style.display = matchesSearch && matchesCategory ? 'flex' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);
        categoryFilter.addEventListener('change', applyFilters);

        document.addEventListener('click', (event) => {
            const closeBtn = event.target.closest('.cart-toast__close');
            if (closeBtn && cartToast) {
                cartToast.classList.remove('is-visible');
                cartToast.hidden = true;
                if (toastTimer) clearTimeout(toastTimer);
            }
        });

        foodGrid.addEventListener('click', (event) => {
            const btn = event.target.closest('.add-btn');
            const card = event.target.closest('.food-card');

            if (btn) {
                const itemName = card?.dataset?.name;
                if (!itemName) return;
                addToCart(itemName, btn);
                return;
            }

            if (card) {
                const itemName = card.dataset.name;
                openItemDetail(itemName);
            }
        });

        modalAddToCartBtn.addEventListener('click', () => {
            const itemName = modalAddToCartBtn.dataset.name;
            if (!itemName) return;
            addToCart(itemName, modalAddToCartBtn);

            // Also find the button in the grid and flash it if it's visible
            const gridCard = document.querySelector(`.food-card[data-name="${CSS.escape(itemName)}"]`);
            if (gridCard) {
                const gridBtn = gridCard.querySelector('.add-btn');
                if (gridBtn) flashAlreadyAdded(gridBtn);
            }
        });

        function addToCart(itemName, btnElement) {
            const items = normalizeCartItems(readCart());
            const itemData = menuItems.find(i => i.name === itemName);

            if (!itemData) return;

            const existing = items.find((i) => i.name === itemName);
            if (existing) {
                existing.qty += 1;
            } else {
                items.push({
                    id: itemData.name,
                    name: itemData.name,
                    price: itemData.price,
                    image: itemData.image || '',
                    qty: 1
                });
            }

            writeCart(items);
            if (btnElement) flashAlreadyAdded(btnElement);
            updateCartBadge();

            showToast(
                existing ? 'Quantity updated' : 'Added to cart',
                `${itemName} is in your cart. Check the cart page to checkout.`
            );
        }

        async function fetchCustomerMenu() {
            try {
                const res = await fetch('/api/menu');
                menuItems = await res.json();
                renderMenu(menuItems);
                applyFilters();
            } catch (e) {
                console.error("Failed to load menu", e);
                foodGrid.innerHTML = '<p>Failed to load menu. Please refresh.</p>';
            }
        }

        fetchCustomerMenu();
        updateCartBadge();
        applyFilters();
    </script>
    @include('partials.logout-modal')
</body>

</html>