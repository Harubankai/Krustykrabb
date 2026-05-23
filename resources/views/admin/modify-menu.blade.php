<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Menu</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-color: #fce206;
        }

        .menu-card-image {
            width: 100%;
            height: 220px;
            overflow: hidden;
            background: #eee;
            position: relative;
        }

        .menu-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-grow: 1;
        }

        .menu-card-tag {
            font-size: 11px;
            font-weight: 800;
            color: #0b4f8a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-card h3 {
            font-size: 1.4rem;
            color: #333;
            margin: 0;
        }

        .menu-card-description {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.8em;
        }

        .menu-card-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: #611909;
        }

        .menu-card-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: auto;
            pointer-events: auto !important;
        }

        .btn-edit {
            background: #fce206;
            color: #611909;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-delete {
            background: #d32f2f;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-edit:hover,
        .btn-delete:hover {
            opacity: 0.9;
        }

        .toolbar {
            background: rgba(0, 0, 0, 0.4);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid rgba(252, 226, 6, 0.3);
        }

        .form-row label {
            color: #fce206;
            font-weight: bold;
            text-shadow: 1px 1px #000;
            margin-bottom: 8px;
            display: block;
        }

        .modal .form-row label {
            color: #611909 !important;
            text-shadow: none;
        }

        .form-row input,
        .form-row select {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-row input:focus,
        .form-row select:focus {
            border-color: #fce206;
            box-shadow: 0 0 8px rgba(252, 226, 6, 0.4);
            outline: none;
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
            <li><a href="{{ route('admin.manage-customers') }}"><i class='bx bx-user-circle'></i> Manage Customers </a>
            </li>
            <li><a href="{{ route('admin.modify-menu') }}" class="active"><i class="bx bx-edit"></i> Modify Menu </a>
            </li>
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
                    <h1>Modify Menu</h1>
                    <p>Update menu items, pricing, and availability in one place.</p>
                </div>
                <div class="toolbar-actions">
                    <button class="btn btn-primary" id="addMenuBtn">+ Add Menu Item</button>
                </div>
            </div>
        </header>

        <section class="cards">
            <div class="card">
                <h3>Total Items</h3>
                <p id="totalCount">0</p>
            </div>
            <div class="card">
                <h3>Meals</h3>
                <p id="mealCount">0</p>
            </div>
            <div class="card">
                <h3>Drinks</h3>
                <p id="drinkCount">0</p>
            </div>
        </section>

        <section class="toolbar">
            <div class="filters-row" style="width: 100%;">
                <div class="form-row">
                    <label for="adminSearch">Search Menu</label>
                    <input type="text" id="adminSearch" placeholder="Search by food name">
                </div>
                <div class="form-row">
                    <label for="adminCategory">Category</label>
                    <select id="adminCategory">
                        <option value="all">All</option>
                        <option value="meal">Meal</option>
                        <option value="drinks">Drinks</option>
                    </select>
                </div>
            </div>
        </section>

        <p class="empty-state" id="emptyState" hidden>No menu items yet. Click \"+ Add Menu Item\" to start.</p>
        <section class="menu-grid" id="adminMenuGrid"></section>
    </main>

    <div class="modal-overlay" id="addMenuModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="addMenuTitle">
            <h3 id="addMenuTitle">Add Menu Item</h3>
            <form id="addMenuForm">
                <div class="modal-body">
                    <div class="form-row">
                        <label for="addName">Food Name</label>
                        <input id="addName" type="text" oninput="this.value = this.value.replace(/[0-9]/g, '');"
                            required>
                    </div>
                    <div class="form-row">
                        <label for="addCategory">Category</label>
                        <select id="addCategory" required>
                            <option value="meal">Meal</option>
                            <option value="drinks">Drinks</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="addDescription">Description</label>
                        <input id="addDescription" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="addPrice">Price</label>
                        <input id="addPrice" class="no-spinner" type="number" step="0.01" min="0" required>
                    </div>
                    <div class="form-row">
                        <label for="addImageFile">Upload Photo</label>
                        <input id="addImageFile" type="file" accept="image/*" required>
                        <small class="muted">Optional. PNG/JPG recommended.</small>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" data-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editMenuModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="editMenuTitle">
            <h3 id="editMenuTitle">Edit Menu Item</h3>
            <form id="editMenuForm">
                <input type="hidden" id="editId">
                <div class="modal-body">
                    <div class="form-row">
                        <label for="editName">Food Name</label>
                        <input id="editName" type="text" oninput="this.value = this.value.replace(/[0-9]/g, '');"
                            required>
                    </div>
                    <div class="form-row">
                        <label for="editCategory">Category</label>
                        <select id="editCategory" required>
                            <option value="meal">Meal</option>
                            <option value="drinks">Drinks</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="editDescription">Description</label>
                        <input id="editDescription" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="editPrice">Price</label>
                        <input id="editPrice" class="no-spinner" type="number" step="0.01" min="0" required>
                    </div>
                    <div class="form-row">
                        <label>Recent Photo</label>
                        <img id="editImagePreview" class="image-preview" alt="Menu item photo preview">
                    </div>
                    <div class="form-row">
                        <label for="editImageFile">Upload New Photo</label>
                        <input id="editImageFile" type="file" accept="image/*">
                        <small class="muted">Leave empty to keep the current photo.</small>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" data-close>Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteMenuModal" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="deleteMenuTitle">
            <h3 id="deleteMenuTitle">Delete Menu Item</h3>
            <div class="modal-body">
                <p id="deleteMenuText">Are you sure you want to delete this menu item?</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" data-close>Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>

    <!-- Item Detail Modal (Admin Preview) -->
    <div id="itemDetailModal" class="item-detail-overlay" hidden>
        <div class="item-detail-content">
            <div class="item-detail-header">Menu Item Preview</div>
            <button type="button" class="item-detail-close" id="closeDetailModal" aria-label="Close">&times;</button>
            <div class="item-detail-body" style="overflow-y: auto; flex: 1;">
                <div class="item-detail-image">
                    <img id="modalItemImage" src="" alt="Item Image">
                </div>
                <div class="item-detail-info">
                    <span id="modalItemCategory" class="tag"></span>
                    <h2 id="modalItemName"></h2>
                    <p id="modalItemDescription" class="description"></p>
                    <div class="item-detail-footer">
                        <span id="modalItemPrice" class="price"></span>
                    </div>
                </div>
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
                description: 'Smooth and cool kelp shake in a small cup.',
                price: 35,
                image: "{{ asset('images/kelpshake.jpg') }}",
            },
            {
                id: 'kelp-shake-medium',
                name: 'Kelp Shake (Medium)',
                category: 'drinks',
                description: 'Refreshing kelp shake for a satisfying sip.',
                price: 45,
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

        const menuGrid = document.getElementById('adminMenuGrid');
        const emptyState = document.getElementById('emptyState');
        const searchInput = document.getElementById('adminSearch');
        const categoryFilter = document.getElementById('adminCategory');
        const totalCount = document.getElementById('totalCount');
        const mealCount = document.getElementById('mealCount');
        const drinkCount = document.getElementById('drinkCount');

        const addModal = document.getElementById('addMenuModal');
        const editModal = document.getElementById('editMenuModal');
        const deleteModal = document.getElementById('deleteMenuModal');
        const deleteMenuText = document.getElementById('deleteMenuText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        const itemDetailModal = document.getElementById('itemDetailModal');
        const closeDetailModal = document.getElementById('closeDetailModal');
        const modalItemImage = document.getElementById('modalItemImage');
        const modalItemCategory = document.getElementById('modalItemCategory');
        const modalItemName = document.getElementById('modalItemName');
        const modalItemDescription = document.getElementById('modalItemDescription');
        const modalItemPrice = document.getElementById('modalItemPrice');

        const statusModal = document.getElementById('statusModal');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const statusIcon = document.getElementById('statusIcon');

        const showStatus = (title, message, isSuccess = true) => {
            statusTitle.textContent = title;
            statusMessage.innerHTML = message;
            statusIcon.innerHTML = isSuccess
                ? "<i class='bx bx-check-circle' style='color: #4CAF50;'></i>"
                : "<i class='bx bx-error-circle' style='color: #ff4d4d;'></i>";
            openModal(statusModal);
        };

        let menuItems = [];
        let deleteTargetId = null;

        function openItemDetail(itemId) {
            const item = menuItems.find(i => String(i.id) === String(itemId));
            if (!item) return;

            modalItemImage.src = item.image || DEFAULT_IMAGE_URL;
            modalItemCategory.textContent = item.category;
            modalItemName.textContent = item.name;
            modalItemDescription.textContent = item.description;
            modalItemPrice.textContent = formatPrice(item.price);

            itemDetailModal.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function closeItemDetail() {
            itemDetailModal.hidden = true;
            document.body.style.overflow = '';
        }

        closeDetailModal.addEventListener('click', closeItemDetail);
        itemDetailModal.addEventListener('click', (e) => {
            if (e.target === itemDetailModal) closeItemDetail();
        });

        function formatPrice(value) {
            const price = Number(value) || 0;
            return `\u20B1${price.toFixed(2)}`;
        }

        function updateCounts(items) {
            const total = items.length;
            const meals = items.filter((item) => item.category === 'meal').length;
            const drinks = items.filter((item) => item.category === 'drinks').length;
            totalCount.textContent = String(total);
            mealCount.textContent = String(meals);
            drinkCount.textContent = String(drinks);
        }

        function openModal(modal) {
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal(modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
        }

        function closeAllModals() {
            [addModal, editModal, deleteModal].forEach(closeModal);
        }

        function readFileAsDataUrl(file) {
            return new Promise((resolve) => {
                if (!file) return resolve('');
                const reader = new FileReader();
                reader.onload = () => resolve(String(reader.result || ''));
                reader.onerror = () => resolve('');
                reader.readAsDataURL(file);
            });
        }

        function setImagePreview(imgEl, src) {
            if (!imgEl) return;
            if (src) {
                imgEl.src = src;
                imgEl.style.display = 'block';
            } else {
                imgEl.src = '';
                imgEl.style.display = 'none';
            }
        }

        function getFilteredItems() {
            const query = searchInput.value.toLowerCase().trim();
            const category = categoryFilter.value;
            return menuItems.filter((item) => {
                const matchesSearch = item.name.toLowerCase().includes(query);
                const matchesCategory = category === 'all' || item.category === category;
                return matchesSearch && matchesCategory;
            });
        }

        function renderMenu(items) {
            menuGrid.innerHTML = '';
            if (!items.length) {
                emptyState.hidden = false;
                return;
            }
            emptyState.hidden = true;

            items.forEach((item) => {
                const card = document.createElement('article');
                card.className = 'menu-card';
                card.dataset.id = item.id;

                const imageSrc = item.image || DEFAULT_IMAGE_URL;

                card.innerHTML = `
                    <div class="menu-card-image">
                        <img src="${imageSrc}" alt="${item.name}">
                    </div>
                    <div class="menu-card-content">
                        <div class="menu-card-tag">${item.category}</div>
                        <h3>${item.name}</h3>
                        <p class="menu-card-description">${item.description}</p>
                        <div class="menu-card-price">${formatPrice(item.price)}</div>
                        <div class="menu-card-actions">
                            <button class="btn-edit" data-action="edit">Edit</button>
                            <button class="btn-delete" data-action="delete">Delete</button>
                        </div>
                    </div>
                `;

                menuGrid.appendChild(card);
            });
        }

        function refreshView() {
            const filtered = getFilteredItems();
            renderMenu(filtered);
            updateCounts(menuItems);
        }

        document.getElementById('addMenuBtn').addEventListener('click', () => {
            document.getElementById('addMenuForm').reset();
            openModal(addModal);
        });

        // ADD ITEM API
        document.getElementById('addMenuForm').addEventListener('submit', async (event) => {
            event.preventDefault();

            const name = document.getElementById('addName').value.trim();
            const category = document.getElementById('addCategory').value;
            const description = document.getElementById('addDescription').value.trim();
            const price = Number(document.getElementById('addPrice').value) || 0;
            const addImageFile = document.getElementById('addImageFile')?.files?.[0] || null;
            const image = await readFileAsDataUrl(addImageFile);

            if (!name || !description) return;

            const submitBtn = event.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const res = await fetch('/api/menu', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, category, description, price, image })
                });
                const data = await res.json();
                if (data.success && data.item) {
                    menuItems.push(data.item);
                    closeModal(addModal);
                    refreshView();
                    showStatus('Success', 'Menu item added successfully!', true);
                } else {
                    showStatus('Error', data.message || "Unknown error", false);
                }
            } catch (e) {
                console.error('Failed to save', e);
                showStatus('Error', 'Failed to connect to the server. Please try again.', false);
            }

            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Item';
        });

        // EDIT ITEM API
        document.getElementById('editMenuForm').addEventListener('submit', async (event) => {
            event.preventDefault();

            const id = document.getElementById('editId').value;
            const name = document.getElementById('editName').value.trim();
            const category = document.getElementById('editCategory').value;
            const description = document.getElementById('editDescription').value.trim();
            const price = Number(document.getElementById('editPrice').value) || 0;
            const editImageFile = document.getElementById('editImageFile')?.files?.[0] || null;

            const index = menuItems.findIndex((item) => String(item.id) === String(id));
            if (index === -1) return;

            const submitBtn = event.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const uploadedImage = await readFileAsDataUrl(editImageFile);
                const nextImage = uploadedImage || menuItems[index].image || '';

                const res = await fetch(`/api/menu/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, category, description, price, image: nextImage })
                });

                const data = await res.json();
                if (data.success && data.item) {
                    menuItems[index] = data.item;
                    closeModal(editModal);
                    refreshView();
                    showStatus('Success', 'Menu item updated successfully!', true);
                } else {
                    showStatus('Error', data.message || "Unknown error", false);
                }
            } catch (e) {
                console.error('Failed to update', e);
                showStatus('Error', 'An error occurred while updating the item.', false);
            }

            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Changes';
        });

        menuGrid.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-action]');
            const card = event.target.closest('.menu-card');

            if (!card) return;
            const itemId = card.dataset.id;
            const item = menuItems.find((entry) => String(entry.id) === String(itemId));
            if (!item) return;

            if (button) {
                event.stopPropagation(); // STOP the card click from firing!
                if (button.dataset.action === 'edit') {
                    document.getElementById('editId').value = item.id;
                    document.getElementById('editName').value = item.name;
                    document.getElementById('editCategory').value = item.category;
                    document.getElementById('editDescription').value = item.description;
                    document.getElementById('editPrice').value = item.price;

                    const editFile = document.getElementById('editImageFile');
                    if (editFile) editFile.value = '';
                    setImagePreview(document.getElementById('editImagePreview'), item.image);
                    openModal(editModal);
                }

                if (button.dataset.action === 'delete') {
                    deleteTargetId = item.id;
                    deleteMenuText.textContent = `Delete "${item.name}" from the menu?`;
                    openModal(deleteModal);
                }
                return;
            }

            // If card clicked but not buttons, open preview
            openItemDetail(itemId);
        });

        // DELETE ITEM API
        confirmDeleteBtn.addEventListener('click', async () => {
            if (!deleteTargetId) return;

            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.textContent = 'Deleting...';

            try {
                const res = await fetch(`/api/menu/${deleteTargetId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });

                const data = await res.json();
                if (data.success) {
                    menuItems = menuItems.filter((item) => String(item.id) !== String(deleteTargetId));
                    closeModal(deleteModal);
                    refreshView();
                    showStatus('Success', 'Item deleted successfully.', true);
                } else {
                    showStatus('Error', data.message || "Unknown error", false);
                }
            } catch (e) {
                console.error('Failed to delete', e);
                showStatus('Error', 'An error occurred while trying to delete the item.', false);
            }

            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.textContent = 'Delete';
            deleteTargetId = null;
        });

        [addModal, editModal, deleteModal, statusModal].forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal(modal);
                const closeBtn = event.target.closest('[data-close]');
                if (closeBtn) {
                    const modalId = closeBtn.getAttribute('data-close');
                    if (modalId) {
                        const targetModal = document.getElementById(modalId);
                        if (targetModal) closeModal(targetModal);
                    } else {
                        closeModal(modal);
                    }
                }
            });
        });

        [searchInput, categoryFilter].forEach((input) => {
            input.addEventListener('input', refreshView);
            input.addEventListener('change', refreshView);
        });

        const editImageFileInput = document.getElementById('editImageFile');
        if (editImageFileInput) {
            editImageFileInput.addEventListener('change', async () => {
                const file = editImageFileInput.files?.[0] || null;
                const preview = document.getElementById('editImagePreview');
                if (!file) return;
                const dataUrl = await readFileAsDataUrl(file);
                setImagePreview(preview, dataUrl);
            });
        }

        // LOAD MENU ITEMS FROM DB
        async function fetchMenuItems() {
            try {
                const res = await fetch('/api/menu');
                let items = await res.json();

                // Auto Seed if blank database
                if (items.length === 0) {
                    menuGrid.innerHTML = '<p style="padding: 2em; color: #888;">Migrating local storage to database...</p>';
                    let seededItems = [];

                    // Recover old local storage items!
                    let oldLocalItems = [];
                    try {
                        const raw = localStorage.getItem('kk_menu_items_v1');
                        if (raw) oldLocalItems = JSON.parse(raw);
                    } catch (e) { }

                    const sourceItems = (oldLocalItems && oldLocalItems.length > 0) ? oldLocalItems : DEFAULT_MENU_ITEMS;

                    for (const item of sourceItems) {
                        try {
                            const postRes = await fetch('/api/menu', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    name: item.name,
                                    category: item.category === 'drinks' ? 'drinks' : 'meal',
                                    description: item.description || '',
                                    price: Number(item.price) || 0,
                                    image: item.image || ''
                                })
                            });
                            const postData = await postRes.json();
                            if (postData.item) seededItems.push(postData.item);
                        } catch (e) { }
                    }
                    items = seededItems;
                }

                menuItems = items;
                refreshView();
            } catch (err) {
                console.error('DB Fetch Error:', err);
            }
        }

        fetchMenuItems();
    </script>
    @include('partials.logout-modal')
</body>

</html>