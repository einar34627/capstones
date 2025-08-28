<?php
// Fix Login Script
// This will create the admin users with correct passwords

echo "<h2>🔧 Fix Login Script</h2>";
echo "<p>Creating admin users with correct passwords...</p>";

try {
    // Include database connection
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance();
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // First, let's check if the users table exists and has the right structure
    echo "<h3>Step 1: Checking database structure...</h3>";
    
    // Check if usertype column supports the new admin types
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'usertype'");
    $columnInfo = $result->fetch();
    
    if ($columnInfo) {
        echo "<p>Current usertype column: " . htmlspecialchars($columnInfo['Type']) . "</p>";
        
        // Update to support new admin types if needed
        if (strpos($columnInfo['Type'], 'ENUM') !== false && strpos($columnInfo['Type'], 'super_admin') === false) {
            $db->query("ALTER TABLE users MODIFY COLUMN usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user'");
            echo "<p style='color: green;'>✅ Updated usertype column to support new admin types</p>";
        }
    } else {
        // Add usertype column if it doesn't exist
        $db->query("ALTER TABLE users ADD COLUMN usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user'");
        echo "<p style='color: green;'>✅ Added usertype column</p>";
    }
    
    // Step 2: Create or update admin users
    echo "<h3>Step 2: Creating/Updating admin users...</h3>";
    
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Delete existing admin users to avoid duplicates
    $db->query("DELETE FROM users WHERE email IN (?, ?, ?)", ['superadmin@caps.com', 'secadmin@caps.com', 'admin@caps.com']);
    echo "<p style='color: blue;'>✅ Cleared existing admin users</p>";
    
    // Create Super Admin
    $superAdminData = [
        'name' => 'Super Admin',
        'email' => 'superadmin@caps.com',
        'password' => $adminPassword,
        'usertype' => 'super_admin',
        'created_at' => date('Y-m-d H:i:s')
    ];
    $superAdminId = $db->insert('users', $superAdminData);
    echo "<p style='color: green;'>✅ Created Super Admin (ID: {$superAdminId})</p>";
    
    // Create Secretary Admin
    $secAdminData = [
        'name' => 'Secretary Admin',
        'email' => 'secadmin@caps.com',
        'password' => $adminPassword,
        'usertype' => 'sec_admin',
        'created_at' => date('Y-m-d H:i:s')
    ];
    $secAdminId = $db->insert('users', $secAdminData);
    echo "<p style='color: green;'>✅ Created Secretary Admin (ID: {$secAdminId})</p>";
    
    // Create Admin
    $adminData = [
        'name' => 'Admin User',
        'email' => 'admin@caps.com',
        'password' => $adminPassword,
        'usertype' => 'admin',
        'created_at' => date('Y-m-d H:i:s')
    ];
    $adminId = $db->insert('users', $adminData);
    echo "<p style='color: green;'>✅ Created Admin (ID: {$adminId})</p>";
    
    // Step 3: Verify the users were created
    echo "<h3>Step 3: Verifying users...</h3>";
    
    $users = $db->fetchAll("SELECT id, name, email, usertype FROM users ORDER BY id");
    
    if ($users) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Email</th><th>User Type</th>";
        echo "</tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td style='font-weight: bold;'>" . ucfirst(str_replace('_', ' ', $user['usertype'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Step 4: Test password verification
    echo "<h3>Step 4: Testing password verification...</h3>";
    
    $testEmails = ['superadmin@caps.com', 'secadmin@caps.com', 'admin@caps.com'];
    $testPassword = 'admin123';
    
    foreach ($testEmails as $email) {
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user) {
            if (password_verify($testPassword, $user['password'])) {
                echo "<p style='color: green;'>✅ Password verification SUCCESS for {$email}</p>";
            } else {
                echo "<p style='color: red;'>❌ Password verification FAILED for {$email}</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ User {$email} not found!</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🎉 Fix Complete!</h3>";
    echo "<p style='color: green; font-weight: bold;'>Your admin users have been created successfully!</p>";
    echo "<p>You can now login with:</p>";
    echo "<ul>";
    echo "<li><strong>Super Admin:</strong> superadmin@caps.com / admin123</li>";
    echo "<li><strong>Secretary Admin:</strong> secadmin@caps.com / admin123</li>";
    echo "<li><strong>Admin:</strong> admin@caps.com / admin123</li>";
    echo "</ul>";
    
    echo "<p><a href='login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration.</p>";
}
?>
