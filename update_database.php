<?php
// Database Migration Script
// This script will update your existing database to support the new admin types

echo "<h2>Database Migration Script</h2>";
echo "<p>Updating database schema and creating admin users...</p>";

try {
    // Include database connection
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance();
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Step 1: Update the users table to support new admin types
    echo "<h3>Step 1: Updating users table schema...</h3>";
    
    // First, let's check the current table structure
    $result = $db->query("DESCRIBE users");
    $columns = [];
    while ($row = $result->fetch()) {
        $columns[] = $row['Field'];
    }
    
    // Check if usertype column exists and what type it is
    $usertypeExists = in_array('usertype', $columns);
    
    if ($usertypeExists) {
        // Get current usertype column definition
        $result = $db->query("SHOW COLUMNS FROM users LIKE 'usertype'");
        $columnInfo = $result->fetch();
        
        if (strpos($columnInfo['Type'], 'ENUM') !== false) {
            // Update existing ENUM to include new values
            $db->query("ALTER TABLE users MODIFY COLUMN usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user'");
            echo "<p style='color: green;'>✅ Updated usertype column to support new admin types</p>";
        } else {
            // Convert to ENUM if it's not already
            $db->query("ALTER TABLE users MODIFY COLUMN usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user'");
            echo "<p style='color: green;'>✅ Converted usertype column to ENUM with new admin types</p>";
        }
    } else {
        // Add usertype column if it doesn't exist
        $db->query("ALTER TABLE users ADD COLUMN usertype ENUM('user', 'admin', 'sec_admin', 'super_admin') DEFAULT 'user'");
        echo "<p style='color: green;'>✅ Added usertype column with new admin types</p>";
    }
    
    // Step 2: Create or update admin users
    echo "<h3>Step 2: Creating/Updating admin users...</h3>";
    
    // Check if admin users already exist
    $superAdmin = $db->fetch("SELECT * FROM users WHERE email = ?", ['superadmin@caps.com']);
    $secAdmin = $db->fetch("SELECT * FROM users WHERE email = ?", ['secadmin@caps.com']);
    $admin = $db->fetch("SELECT * FROM users WHERE email = ?", ['admin@caps.com']);
    
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Create or update Super Admin
    if (!$superAdmin) {
        $userData = [
            'name' => 'Super Admin',
            'email' => 'superadmin@caps.com',
            'password' => $adminPassword,
            'usertype' => 'super_admin',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $userId = $db->insert('users', $userData);
        echo "<p style='color: green;'>✅ Created Super Admin user (ID: {$userId})</p>";
    } else {
        // Update existing user to super_admin type
        $db->update('users', 
            ['usertype' => 'super_admin', 'password' => $adminPassword], 
            'email = ?', 
            ['superadmin@caps.com']
        );
        echo "<p style='color: green;'>✅ Updated existing user to Super Admin</p>";
    }
    
    // Create or update Secretary Admin
    if (!$secAdmin) {
        $userData = [
            'name' => 'Secretary Admin',
            'email' => 'secadmin@caps.com',
            'password' => $adminPassword,
            'usertype' => 'sec_admin',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $userId = $db->insert('users', $userData);
        echo "<p style='color: green;'>✅ Created Secretary Admin user (ID: {$userId})</p>";
    } else {
        // Update existing user to sec_admin type
        $db->update('users', 
            ['usertype' => 'sec_admin', 'password' => $adminPassword], 
            'email = ?', 
            ['secadmin@caps.com']
        );
        echo "<p style='color: green;'>✅ Updated existing user to Secretary Admin</p>";
    }
    
    // Create or update Admin
    if (!$admin) {
        $userData = [
            'name' => 'Admin User',
            'email' => 'admin@caps.com',
            'password' => $adminPassword,
            'usertype' => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $userId = $db->insert('users', $userData);
        echo "<p style='color: green;'>✅ Created Admin user (ID: {$userId})</p>";
    } else {
        // Update existing user to admin type
        $db->update('users', 
            ['usertype' => 'admin', 'password' => $adminPassword], 
            'email = ?', 
            ['admin@caps.com']
        );
        echo "<p style='color: green;'>✅ Updated existing user to Admin</p>";
    }
    
    // Step 3: Show all users for verification
    echo "<h3>Step 3: Current users in database:</h3>";
    $allUsers = $db->fetchAll("SELECT id, name, email, usertype, created_at FROM users ORDER BY id");
    
    if ($allUsers) {
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>ID</th><th>Name</th><th>Email</th><th>User Type</th><th>Created</th></tr>";
        foreach ($allUsers as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td style='font-weight: bold;'>" . ucfirst(str_replace('_', ' ', $user['usertype'])) . "</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>Migration Complete! 🎉</h3>";
    echo "<p style='color: green; font-weight: bold;'>Your database has been successfully updated!</p>";
    echo "<p>You can now login with any of these accounts:</p>";
    echo "<ul>";
    echo "<li><strong>Super Admin:</strong> superadmin@caps.com / admin123</li>";
    echo "<li><strong>Secretary Admin:</strong> secadmin@caps.com / admin123</li>";
    echo "<li><strong>Admin:</strong> admin@caps.com / admin123</li>";
    echo "</ul>";
    
    echo "<p><a href='login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration and try again.</p>";
}
?>
