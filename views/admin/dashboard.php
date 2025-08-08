<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        
        .header {
            background-color: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        .speech-bubble-welcome {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 250px;
        max-width: 280px;
        padding: 14px 22px;
        background: #eacc23ef;
        font-size: 1rem;
        color: #222;
        font-family: 'Segoe UI', Arial, sans-serif;
        font-weight: 600;
        z-index: 2000;
        box-shadow: 0 4px 18px rgba(0,0,0,0.10);
        display: flex;
        align-items: center;
        height: 40px;
        }

    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <p>Barangay Commonwealth Administration</p>
    </div>
    <div class="speech-bubble-welcome" id="welcomeBubble">
    Welcome, Admin!
</div>
    <div class="container">
        <?php
        // Include database connection for statistics
        require_once __DIR__ . '/../../includes/Database.php';
        
        try {
            $db = Database::getInstance();
            $admin_users = $db->fetch("SELECT COUNT(*) as count FROM users WHERE usertype = 'admin'")['count'];
        } catch (Exception $e) {
            $admin_users = 0;
        }
        ?>
        
        <div class="admin-info">
            <div class="user-info">
                <div class="user-details">
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Administrator'); ?>!</h2>
                    <p>Email: <?php echo htmlspecialchars($_SESSION['email'] ?? 'admin@barangay.com'); ?></p>
                    <p>Role: Administrator</p>
                </div>
                <div>
                    <a href="welcome" class="btn btn-primary">Back to Home</a>
                    <a href="logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
        
        <div class="stats-section" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h3 style="color: #333; margin-bottom: 20px; text-align: center;">System Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <h4 style="color: #dc2626; font-size: 2rem; margin-bottom: 10px;"><?php echo $admin_users; ?></h4>
                    <p style="color: #666; margin: 0;">Administrators</p>
                </div>
            </div>
        </div>
        
        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-icon">⚙️</div>
                <h3>Settings</h3>
                <p>Configure system settings, notifications, and administrative preferences.</p>
                <a href="admin/settings" class="btn btn-primary">Configure Settings</a>
            </div>
            
            <div class="admin-card">
                <div class="card-icon">📊</div>
                <h3>View Reports</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
        </div>
    </div>
    <script>
    setTimeout(() => {
    document.getElementById('welcomeBubble').style.display = 'none';
    }, 3000);
</script>
</body>
</html>