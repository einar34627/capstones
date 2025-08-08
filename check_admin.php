<?php
session_start();

// Include database connection
require_once __DIR__ . '/includes/Database.php';

echo "<h2>Admin Account Check</h2>";

try {
    $db = Database::getInstance();
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Check admin user
    $user = $db->fetch("SELECT * FROM users WHERE email = ?", ['admin@caps.com']);
    
    if ($user) {
        echo "<p style='color: blue;'>ℹ️ Found admin user:</p>";
        echo "<p><strong>ID:</strong> {$user['id']}</p>";
        echo "<p><strong>Name:</strong> {$user['name']}</p>";
        echo "<p><strong>Email:</strong> {$user['email']}</p>";
        echo "<p><strong>User Type:</strong> {$user['usertype']}</p>";
        echo "<p><strong>Password Hash:</strong> {$user['password']}</p>";
        
        // Test password verification
        $test_password = 'admin123';
        if (password_verify($test_password, $user['password'])) {
            echo "<p style='color: green;'>✅ Password verification successful for 'admin123'</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification failed for 'admin123'</p>";
            
            // Fix the password
            echo "<p>Fixing password...</p>";
            $correct_hash = password_hash('admin123', PASSWORD_DEFAULT);
            
            $result = $db->update('users', 
                ['password' => $correct_hash], 
                'email = ?', 
                ['admin@caps.com']
            );
            
            if ($result > 0) {
                echo "<p style='color: green;'>✅ Password updated successfully!</p>";
                
                // Verify the fix
                $updated_user = $db->fetch("SELECT * FROM users WHERE email = ?", ['admin@caps.com']);
                if (password_verify('admin123', $updated_user['password'])) {
                    echo "<p style='color: green;'>✅ Password verification now works!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Password verification still fails</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ Failed to update password</p>";
            }
        }
        
    } else {
        echo "<p style='color: red;'>❌ Admin user not found</p>";
        echo "<p>Creating admin user...</p>";
        
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $user_data = [
            'name' => 'Admin User',
            'email' => 'admin@caps.com',
            'password' => $admin_password,
            'usertype' => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $user_id = $db->insert('users', $user_data);
        
        if ($user_id) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
            echo "<p><strong>User ID:</strong> {$user_id}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    }
    
    // Show all users
    echo "<h3>All Users:</h3>";
    $all_users = $db->fetchAll("SELECT id, name, email, usertype FROM users");
    if ($all_users) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>User Type</th></tr>";
        foreach ($all_users as $u) {
            echo "<tr>";
            echo "<td>{$u['id']}</td>";
            echo "<td>{$u['name']}</td>";
            echo "<td>{$u['email']}</td>";
            echo "<td>{$u['usertype']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p><a href='login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Again</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
