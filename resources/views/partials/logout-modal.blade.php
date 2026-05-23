<!-- Logout Confirmation Modal -->
<style>
    .logout-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .logout-modal-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    .logout-modal-box {
        background: #fff;
        border-radius: 15px;
        width: 90%;
        max-width: 400px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        transform: translateY(-30px);
        transition: transform 0.3s ease;
    }

    .logout-modal-overlay.show .logout-modal-box {
        transform: translateY(0);
    }

    .logout-modal-icon {
        font-size: 60px;
        color: #50061e;
        margin-bottom: 20px;
        animation: pulseLogout 2s infinite;
    }

    @keyframes pulseLogout {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .logout-modal-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: #333;
        font-family: 'Poppins', sans-serif;
    }

    .logout-modal-text {
        font-size: 1.05rem;
        color: #555;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .logout-modal-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .logout-modal-btn {
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.2s;
        flex: 1;
        border: none;
        font-family: 'Poppins', sans-serif;
    }

    .logout-modal-btn--cancel {
        background: #f0f0f0;
        color: #555;
    }

    .logout-modal-btn--cancel:hover {
        background: #e5e5e5;
        color: #333;
    }

    .logout-modal-btn--confirm {
        background: #fce206;
        color: #611909;
        box-shadow: 0 4px 10px rgba(252, 226, 6, 0.3);
    }

    .logout-modal-btn--confirm:hover {
        background: #e5cc05;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(252, 226, 6, 0.4);
    }
</style>

<div class="logout-modal-overlay" id="logoutModal">
    <div class="logout-modal-box">
        <div class="logout-modal-icon">
            <i class='bx bx-log-out-circle'></i>
        </div>
        <div class="logout-modal-title">Wait! Logging out?</div>
        <div class="logout-modal-text">Are you sure you want to log out of your Krusty Krab account?</div>
        <div class="logout-modal-actions">
            <button class="logout-modal-btn logout-modal-btn--cancel" onclick="closeLogoutModal()">Cancel</button>
            <button class="logout-modal-btn logout-modal-btn--confirm" id="confirmLogoutBtn">Log Out</button>
        </div>
    </div>
</div>

<script>
    let currentLogoutUrl = "";

    function showLogoutModal(url) {
        currentLogoutUrl = url;
        const modal = document.getElementById('logoutModal');
        modal.classList.add('show');
        
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        modal.classList.remove('show');
        
        // Restore background scrolling
        document.body.style.overflow = '';
    }

    document.getElementById('confirmLogoutBtn').addEventListener('click', function() {
        if (currentLogoutUrl) {
            window.location.href = currentLogoutUrl;
        }
    });

    // Close modal if overlay is clicked
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLogoutModal();
        }
    });
</script>
