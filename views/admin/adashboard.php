<?php
// Include authentication check
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        
        .header {
            background-image: url('/capstone/images/baramgay.jpg');
            background-size: cover;
            background-position: center;
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
        }
        .header h1,p {
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif !important;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .user-details h2 {
            color: #333;
            margin: 0;
        }
        
        .user-details p {
            color: #666;
            margin: 5px 0;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        
        .admin-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px; 
            margin-top: 20px;
        }
        
        .admin-card {
            background: white;
            padding: 25px;  
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .admin-card h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .admin-card p {
            color: #666;
            line-height: 1.6;
        }
        
        .card-icon {
            font-size: 3rem;
            color: #1f2937;
            margin-bottom: 15px;
        }
    /* Notification button/dropdown */
        .notif-wrapper { position: relative; }
        .notif-btn { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
        .notif-btn:hover { background: #f3f4f6; }
        .notif-btn i { color: #1f2937; font-size: 18px; }
        .notif-badge { position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; padding: 0 4px; border-radius: 999px; background: #ef4444; color: #fff; font-size: 12px; line-height: 18px; text-align: center; }
        .notif-dropdown { position: absolute; right: 0; top: 48px; width: 320px; max-height: 380px; overflow-y: auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: none; z-index: 2000; }
        .notif-dropdown.open { display: block; }
        .notif-header { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #111827; }
        .notif-item { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 10px; align-items: start; }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
        .notif-icon.success { background: #d1fae5; color: #065f46; }
        .notif-icon.fail { background: #fee2e2; color: #991b1b; }
        .notif-text { font-size: 13px; color: #374151; }
        .notif-time { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <p>Barangay Commonwealth Administration</p>
    </div>
    
    <div class="container">
        <div class="admin-info">
            <div class="user-info">
                <div class="user-details">
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?>!</h2>
                    <p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'admin@barangay.com'); ?></p>
                    <p>Role: Administrator</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="notif-wrapper">
                        <button id="notifBtn" class="notif-btn" type="button" aria-haspopup="true" aria-expanded="false" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
                        </button>
                        <div id="notifDropdown" class="notif-dropdown" role="menu" aria-label="Notifications">
                            <div class="notif-header" style="display:flex;align-items:center;justify-content:space-between;">
                                <span>Notifications</span>
                                <button id="notifClearBtn" class="btn" style="background:#f1f5f9;color:#111827;padding:6px 10px;border-radius:6px;font-weight:600;border:none;cursor:pointer;">Clear</button>
                            </div>
                            <div id="notifList"></div>
                        </div>
                    </div>
                    <a href="/capstone/welcome" class="btn btn-primary" style="margin-right: 0;">Back to Home</a>
                    <a href="/capstone/logout_handler.php" class="btn btn-danger" style="margin-left: 20px;">Logout</a>
                </div>
            </div>
        </div>

        <div class="admin-grid">
        <div class="admin-card">
                <div class="card-icon">📹</div>
                <h3>Live Surveillance Feed Panel</h3>
                <p>Real-time facial recognition monitoring and surveillance system.</p>
                <button id="startSurveillance" class="btn btn-primary">Start Access</button>
            </div>
        <div class="admin-card">
                <div class="card-icon">👥</div>
                <h3>Face Management</h3>
                <p>Register and manage facial recognition profiles and user identities.</p>
                <a href="<?php echo url('admin/face_management'); ?>" class="btn btn-primary">Manage Faces</a>
            </div>

            <div class="admin-card">
                <div class="card-icon">🚨</div>
                <h3>Facial Recognition Alerts</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">🖥️</div>
                <h3>YOLO Intrusion Detection Monitor</h3>
                <p>Configure system settings, notifications, and administrative preferences.</p>
                <a href="admin/settings" class="btn btn-primary">Configure Settings</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📢</div>
                <h3>Feedback & Tip Management</h3>
                <p>Review, approve, or decline citizen tips and feedback.</p>
                <a href="feedback_tip_management/" class="btn btn-primary">Open</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📊</div>
                <h3>Basic Reports</h3>
                <p>View basic community reports and statistics.</p>
                <a href="#" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📝</div>
                <h3>Content Management</h3>
                <p>Manage basic content and announcements.</p>
                <a href="#" class="btn btn-primary">Manage Content</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">⚙️</div>
                <h3>Settings</h3>
                <p>Configure basic system settings.</p>
                <a href="#" class="btn btn-primary">Configure Settings</a>
            </div>
        </div>
    </div>
<script>
        // Notifications (dropdown)
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifList = document.getElementById('notifList');
        const notifBadge = document.getElementById('notifBadge');
        const notifClearBtn = document.getElementById('notifClearBtn');
        let notifOpen = false;

        function toggleNotif(open) {
            notifOpen = (open !== undefined) ? open : !notifOpen;
            notifDropdown.classList.toggle('open', notifOpen);
            notifBtn.setAttribute('aria-expanded', notifOpen ? 'true' : 'false');
        }

        if (notifClearBtn) {
            notifClearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                clearAllNotifications();
            });
        }

        async function loadNotifications() {
            try {
                // Fetch count and list from notifications API
                const [countRes, listRes] = await Promise.all([
                    fetch('/capstone/views/api/notifications.php?action=count', { cache: 'no-store' }),
                    fetch('/capstone/views/api/notifications.php?action=list', { cache: 'no-store' })
                ]);
                let count = 0; let items = [];
                try { const c = await countRes.json(); count = (c && c.success) ? (c.count||0) : 0; } catch(e) { count = 0; }
                try { const l = await listRes.json(); const arr = (l && l.success && Array.isArray(l.notifications)) ? l.notifications : []; items = arr.slice(-10).reverse().map(n => ({
                    id: n.id,
                    text: n.text || (n.type==='tip'?'New tip submitted':'New feedback submitted'),
                    time: n.timestamp || '',
                    success: n.type !== 'tip' ? true : false,
                    type: n.type
                })); } catch(e) { items = []; }
                notifBadge.textContent = String(count);
                notifBadge.style.display = count > 0 ? 'inline-block' : 'none';
                renderNotifications(items);
                // Turn dropdown rows into links to management page
                setTimeout(() => {
                    const listEl = document.getElementById('notifList');
                    if (!listEl) return;
                    listEl.querySelectorAll('.notif-item').forEach((row) => {
                        row.style.cursor = 'pointer';
                        row.addEventListener('click', () => {
                            window.location.href = 'feedback_tip_management/';
                        });
                    });
                }, 0);
            } catch (e) {
                renderNotifications([]);
            }
        }
        function renderNotifications(items) {
            notifList.innerHTML = '';
            if (!items || items.length === 0) {
                const d = document.createElement('div');
                d.className = 'notif-item';
                d.innerHTML = '<div class="notif-text">No notifications</div>';
                notifList.appendChild(d);
                notifBadge.style.display = 'none';
                return;
            }
            let count = 0;
            items.forEach(it => {
                const row = document.createElement('div');
                row.className = 'notif-item';
                const iconClass = it.success ? 'success' : 'fail';
                row.innerHTML = `
                    <div class="notif-icon ${iconClass}"><i class="${it.success ? 'fas fa-check' : 'fas fa-exclamation'}"></i></div>
                    <div style="flex:1;">
                        <div class="notif-text">${it.text}</div>
                        <div class="notif-time">${it.time ? new Date(it.time).toLocaleString() : ''}</div>
                    </div>
                    <button class="notif-del" title="Delete" style="background:transparent;border:none;color:#9ca3af;cursor:pointer;padding:4px;"><i class="fas fa-trash"></i></button>
                `;
                const delBtn = row.querySelector('.notif-del');
                if (delBtn) {
                    delBtn.addEventListener('click', (ev) => { ev.stopPropagation(); deleteNotification(it.id); });
                }
                notifList.appendChild(row);
                count++;
            });
            notifBadge.textContent = String(count);
            notifBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }

        async function deleteNotification(id) {
            try {
                const params = new URLSearchParams({ action: 'delete' });
                const body = new URLSearchParams({ id });
                await fetch('/capstone/views/api/notifications.php?' + params.toString(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body
                });
                await loadNotifications();
            } catch (e) { /* ignore */ }
        }

        async function clearAllNotifications() {
            try {
                const params = new URLSearchParams({ action: 'clear_all' });
                await fetch('/capstone/views/api/notifications.php?' + params.toString(), { method: 'POST' });
                await loadNotifications();
            } catch (e) { /* ignore */ }
        }
        document.addEventListener('click', (e) => {
            if (!notifDropdown) return;
            if (notifBtn && notifBtn.contains(e.target)) { toggleNotif(); return; }
            if (notifDropdown.contains(e.target)) return;
            toggleNotif(false);
        });
        // Initial load
        loadNotifications();
        // Refresh every 30s
        setInterval(loadNotifications, 30000);
    </script>
</body>
</html>
