<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages - Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .messages-container {
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .message-card {
            border-bottom: 1px solid #eee;
            padding: 15px;
            position: relative;
            transition: background 0.3s;
        }

        .message-card:hover {
            background: #f9f9f9;
        }

        .message-card.unread {
            background: #fffdf0;
            border-left: 4px solid #fce206;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .sender-info h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #333;
        }

        .sender-info span {
            font-size: 0.85rem;
            color: #888;
        }

        .message-time {
            font-size: 0.8rem;
            color: #aaa;
        }

        .message-body {
            color: #555;
            line-height: 1.5;
            margin-bottom: 10px;
            white-space: pre-wrap;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-read {
            color: #188038;
        }

        .btn-read:hover {
            background: #e6f4ea;
        }

        .btn-delete {
            color: #d93025;
        }

        .btn-delete:hover {
            background: #fce8e6;
        }

        .unread-badge {
            background: #fce206;
            color: #611909;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-left: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #888;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 10px;
            display: block;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal h3 {
            margin-top: 0;
            color: #333;
        }

        .modal-body {
            margin: 1.5rem 0;
            color: #666;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-secondary {
            background: #eee;
            color: #333;
        }

        .btn-danger {
            background: #d93025;
            color: #fff;
        }

        .btn-primary {
            background: #fce206;
            color: #611909;
        }

        .btn:hover {
            opacity: 0.9;
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
            <li><a href="{{ route('admin.dashboard') }}"><i class='bx bx-trending-up'></i> Dashboard</a></li>
            <li><a href="{{ route('admin.orders') }}"><i class='bx bx-basket'></i> Orders</a></li>
            <li><a href="{{ route('admin.manage-riders') }}"><i class='bx bx-group'></i> Manage Riders</a></li>
            <li><a href="{{ route('admin.manage-customers') }}"><i class='bx bx-user-circle'></i> Manage Customers</a>
            </li>
            <li><a href="{{ route('admin.modify-menu') }}"><i class="bx bx-edit"></i> Modify Menu</a></li>
            <li><a href="{{ route('admin.messages') }}" class="active"><i class='bx bx-envelope'></i> Messages</a></li>
        </ul>

        <div class="logout">
            <a href="javascript:void(0)" class="logout-btn"
                onclick="showLogoutModal('{{ route('logout') }}')">Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="header">
            <h1>Customer Messages</h1>
            <p>Manage inquiries and feedback from your customers.</p>
        </header>

        <div class="messages-container">
            @if($messages->isEmpty())
                <div class="empty-state">
                    <i class='bx bx-message-square-detail'></i>
                    <p>No messages yet.</p>
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="message-card {{ $msg->is_read ? '' : 'unread' }}" id="message-{{ $msg->id }}">
                        <div class="message-header">
                            <div class="sender-info">
                                <h3>{{ $msg->name ?? 'Anonymous' }} @if(!$msg->is_read) <span class="unread-badge">NEW</span>
                                @endif</h3>
                                <span>{{ $msg->email }}</span>
                            </div>
                            <div class="message-time">
                                {{ $msg->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="message-body">{{ $msg->message }}</div>
                        <div class="message-actions">
                            @if(!$msg->is_read)
                                <button class="action-btn btn-read" onclick="markRead({{ $msg->id }})">
                                    <i class='bx bx-check-double'></i> Mark as Read
                                </button>
                            @endif
                            <button class="action-btn btn-delete" onclick="deleteMessage({{ $msg->id }})">
                                <i class='bx bx-trash'></i> Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </main>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal" role="dialog" aria-modal="true">
            <h3>Confirm Deletion</h3>
            <div class="modal-body">
                <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary"
                    onclick="closeModal(document.getElementById('deleteConfirmModal'))">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
                <button class="btn btn-primary" onclick="closeModal(document.getElementById('statusModal'))">OK</button>
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




        const statusModal = document.getElementById('statusModal');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const statusIcon = document.getElementById('statusIcon');
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let deleteTargetId = null;

        const openModal = modal => modal.classList.add('active');
        const closeModal = modal => modal.classList.remove('active');

        const showStatus = (title, message, isSuccess = true) => {
            statusTitle.textContent = title;
            statusMessage.innerHTML = message;
            statusIcon.innerHTML = isSuccess ? "<i class='bx bx-check-circle' style='color: #4CAF50;'></i>" : "<i class='bx bx-error-circle' style='color: #ff4d4d;'></i>";
            openModal(statusModal);
        };

        // Close modal on overlay click
        [statusModal, deleteConfirmModal].forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) closeModal(modal);
            });
        });

        async function markRead(id) {
            try {
                const response = await fetch(`/admin/messages/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    const card = document.getElementById(`message-${id}`);
                    card.classList.remove('unread');
                    const badge = card.querySelector('.unread-badge');
                    if (badge) badge.remove();
                    const readBtn = card.querySelector('.btn-read');
                    if (readBtn) readBtn.remove();
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Error', 'Failed to mark message as read.', false);
            }
        }

        function deleteMessage(id) {
            deleteTargetId = id;
            openModal(deleteConfirmModal);
        }

        confirmDeleteBtn.addEventListener('click', async () => {
            if (!deleteTargetId) return;

            try {
                const response = await fetch(`/admin/messages/${deleteTargetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    const card = document.getElementById(`message-${deleteTargetId}`);
                    if (card) card.remove();
                    closeModal(deleteConfirmModal);
                    showStatus('Success', 'Message deleted successfully.', true);

                    if (document.querySelectorAll('.message-card').length === 0) {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    showStatus('Error', 'Failed to delete message.', false);
                }
            } catch (error) {
                console.error('Error:', error);
                showStatus('Error', 'An unexpected error occurred.', false);
            } finally {
                deleteTargetId = null;
            }
        });
    </script>

    @include('partials.logout-modal')
</body>

</html>