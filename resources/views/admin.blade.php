<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .cards .card p {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum" 1;
        }

        .inventory-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .inventory-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.25em 0;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->


    <div class="mobile-toggle" id="mobileToggle">
        <i class='bx bx-menu'></i>
    </div>

    <div class="mobile-overlay" id="mobileOverlay"></div>



    <nav class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logoImg">
            <span class="brand-title">ADMIN</span>
        </div>

        <div class="user-profile">
            @if(optional(session('user'))->profile_picture)
                <img src="{{ asset(session('user')->profile_picture) }}" alt="Profile" class="profile-img">
            @else
                <img src="{{ asset('images/admin_profile.png') }}" alt="Profile" class="profile-img">
            @endif
            <div class="user-info">
                <span class="user-name">{{ optional(session('user'))->name ?? 'Admin' }}</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>

        <ul class="menu">
            <li><a href="{{ route('admin.dashboard') }}" class="active"><i class='bx bx-trending-up'></i> Dashboard</a>
            </li>
            <li><a href="{{ route('admin.orders') }}"><i class='bx bx-basket'></i> Orders</a></li>
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
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <h1>Monthly Statistics (<span id="statsYearDisplay">Year to Date</span>)</h1>
                    <p>Sales overview from January up to the current month.</p>
                </div>
                <div class="year-filter">
                    <label for="statsYear" style="color: #fce206; font-weight: bold; margin-right: 10px;">Select
                        Year:</label>
                    <select id="statsYear" onchange="changeYear()"
                        style="background: rgba(0,0,0,0.5); color: #fce206; border: 1px solid #fce206; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        <option value="2026" selected>2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>
        </header>

        <section class="cards">
            <div class="card">
                <h3>Monthly Sales</h3>
                <p id="monthlySalesValue">₱0.00</p>
            </div>
            <div class="card">
                <h3>Total Riders</h3>
                <p id="totalRidersValue">0</p>
            </div>
            <div class="card" style="cursor: pointer;" onclick="location.href='{{ route('admin.messages') }}'">
                <h3>Unread Messages</h3>
                <p id="unreadMessagesValue">0</p>
            </div>
        </section>

        <div class="dashboard-grid">
            <section class="chart-container recent-orders">
                <h2>Sales by Month (January → Present)</h2>
                <canvas id="salesChart"></canvas>
            </section>

            <section class="top-items inventory-alerts">
                <h2>Top Selling Items (This Month)</h2>
                <ul class="inventory-list" id="topItemsList"></ul>
            </section>

        </div>
    </main>

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



        // SETTINGS & DATA
        let selectedYear = new Date().getFullYear();
        let currentMonth = new Date().getMonth();

        const getMonthLabels = (year) => {
            const isCurrentYear = year === new Date().getFullYear();
            const limit = isCurrentYear ? new Date().getMonth() + 1 : 12;
            return Array.from({ length: limit }, (_, i) =>
                new Date(year, i, 1).toLocaleString(undefined, { month: 'long' })
            );
        };

        const escapeHtml = (text) => String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        // CALCULATE TOTAL RIDERS
        function loadTotalRiders() {
            fetch("{{ url('/total-riders') }}")
                .then(res => res.ok ? res.text() : Promise.reject('Request failed'))
                .then(data => {
                    const val = document.getElementById("totalRidersValue");
                    if (val) val.innerText = Number(data).toLocaleString();
                })
                .catch(err => console.error('Error:', err));
        }
        loadTotalRiders();
        setInterval(loadTotalRiders, 60000);

        // INIT CHART INSTANCE
        let salesChart = null;
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            salesChart = new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: getMonthLabels(selectedYear),
                    datasets: [{
                        label: `Sales (${selectedYear})`,
                        data: Array(getMonthLabels(selectedYear).length).fill(0),
                        backgroundColor: 'rgba(252, 226, 6, 0.75)',
                        borderColor: 'rgba(252, 226, 6, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 44
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => `₱${c.raw.toLocaleString()}` } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: val => `₱${val.toLocaleString()}`, color: '#ccc' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                        x: { ticks: { color: '#fce206' }, grid: { display: false } }
                    }
                }
            });
        }

        // FETCH LIVE STATS FROM DB
        async function updateAdminStats() {
            try {
                const response = await fetch(`{{ route('api.admin.stats') }}?year=${selectedYear}`);
                const data = await response.json();

                // 0. Update Display
                const statsYearDisplay = document.getElementById('statsYearDisplay');
                if (statsYearDisplay) {
                    statsYearDisplay.innerText = selectedYear === new Date().getFullYear() ? 'Year to Date' : `Full Year ${selectedYear}`;
                }

                // 1. Update Monthly Sales
                const salesPerMonth = data.monthlySales || [];
                const totalSales = salesPerMonth.reduce((sum, v) => sum + v, 0);
                const slsVal = document.getElementById('monthlySalesValue');
                if (slsVal) {
                    slsVal.innerText = `₱${totalSales.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                }

                // 2. Update Chart
                if (salesChart) {
                    salesChart.data.labels = getMonthLabels(selectedYear);
                    salesChart.data.datasets[0].label = `Sales (${selectedYear})`;
                    salesChart.data.datasets[0].data = salesPerMonth;
                    salesChart.update();
                }

                // 3. Update Top Items
                const topItems = data.topItems || [];
                const ul = document.getElementById('topItemsList');
                if (ul) {
                    ul.innerHTML = topItems.map(([name, count]) =>
                        `<li><span>${escapeHtml(name)}</span><span>${count} sold</span></li>`
                    ).join('') || '<li style="padding:10px 0; color:#888;">No sales found.</li>';
                }

                const currentViewMonth = selectedYear === new Date().getFullYear() ? new Date().getMonth() : 11;
                const monthName = new Date(selectedYear, currentViewMonth, 1).toLocaleString(undefined, { month: 'long' });
                const topItemsHeader = document.querySelector('.inventory-alerts h2');
                if (topItemsHeader) {
                    topItemsHeader.textContent = `Top Selling Items (${monthName} ${selectedYear})`;
                }

                // 4. Update Unread Messages
                const unreadVal = document.getElementById('unreadMessagesValue');
                if (unreadVal) {
                    unreadVal.innerText = data.unreadMessagesCount || 0;
                }

                // 5. Update Recent Messages
                const msgUl = document.getElementById('recentMessagesList');
                if (msgUl) {
                    const msgs = data.recentMessages || [];
                    msgUl.innerHTML = msgs.map(m =>
                        `<li style="flex-direction:column; align-items:flex-start;">
                    <div style="display:flex; justify-content:space-between; width:100%;">
                        <strong>${escapeHtml(m.name || 'Anonymous')}</strong>
                        <span style="font-size:0.75rem; color:#888;">${new Date(m.created_at).toLocaleDateString()}</span>
                    </div>
                    <div style="font-size:0.85rem; color:#555; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%;">
                        ${escapeHtml(m.message)}
                    </div>
                </li>`
                    ).join('') || '<li style="padding:10px 0; color:#888;">No messages yet.</li>';
                }
            } catch (err) {
                console.error('Failed to update stats', err);
            }
        }

        function changeYear() {
            selectedYear = parseInt(document.getElementById('statsYear').value);
            updateAdminStats();
        }

        updateAdminStats();
        setInterval(updateAdminStats, 5000);
    </script>
    @include('partials.logout-modal')
</body>

</html>