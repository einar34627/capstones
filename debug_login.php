<?php
// Debug Login Script
// This will help us identify what's wrong with the login process

echo "<h2>🔍 Login Debug Script</h2>";
echo "<p>Let's debug the login issue step by step...</p>";

try {
    // Include database connection
    require_once __DIR__ . '/includes/Database.php';
    $db = Database::getInstance();
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Step 1: Check if users table exists and has data
    echo "<h3>Step 1: Checking users table...</h3>";
    
    $users = $db->fetchAll("SELECT * FROM users");
    echo "<p>Total users in database: " . count($users) . "</p>";
    
    if ($users) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Email</th><th>User Type</th><th>Password Hash</th>";
        echo "</tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['usertype']) . "</td>";
            echo "<td style='font-size: 10px; max-width: 200px; overflow: hidden;'>" . htmlspecialchars($user['password']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ No users found in database!</p>";
    }
    
    // Step 2: Test password verification
    echo "<h3>Step 2: Testing password verification...</h3>";
    
    $testEmails = ['superadmin@caps.com', 'secadmin@caps.com', 'admin@caps.com'];
    $testPassword = 'admin123';
    
    foreach ($testEmails as $email) {
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user) {
            echo "<p><strong>Testing {$email}:</strong></p>";
            echo "<ul>";
            echo "<li>User found: ✅</li>";
            echo "<li>User type: " . htmlspecialchars($user['usertype']) . "</li>";
            
            // Test password verification
            if (password_verify($testPassword, $user['password'])) {
                echo "<li style='color: green;'>Password verification: ✅ SUCCESS</li>";
            } else {
                echo "<li style='color: red;'>Password verification: ❌ FAILED</li>";
                
                // Let's see what the password hash looks like
                echo "<li>Current password hash: " . htmlspecialchars($user['password']) . "</li>";
                
                // Generate a new hash and test it
                $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
                echo "<li>New hash for 'admin123': " . htmlspecialchars($newHash) . "</li>";
                
                if (password_verify($testPassword, $newHash)) {
                    echo "<li style='color: green;'>New hash test: ✅ SUCCESS</li>";
                    
                    // Update the password in database
                    $db->update('users', ['password' => $newHash], 'email = ?', [$email]);
                    echo "<li style='color: blue;'>✅ Updated password in database</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>❌ User {$email} not found in database!</p>";
        }
    }
    
    // Step 3: Test the Auth class
    echo "<h3>Step 3: Testing Auth class...</h3>";
    
    require_once __DIR__ . '/includes/Auth.php';
    $auth = new Auth();
    
    foreach ($testEmails as $email) {
        echo "<p><strong>Testing Auth class with {$email}:</strong></p>";
        
        $credentials = ['email' => $email, 'password' => $testPassword];
        $result = $auth->attempt($credentials);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Auth::attempt() SUCCESS for {$email}</p>";
            
            // Check session data
            echo "<p>Session data after login:</p>";
            echo "<ul>";
            echo "<li>user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</li>";
            echo "<li>user_name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "</li>";
            echo "<li>user_email: " . ($_SESSION['user_email'] ?? 'NOT SET') . "</li>";
            echo "<li>user_type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "</li>";
            echo "<li>user_level: " . ($_SESSION['user_level'] ?? 'NOT SET') . "</li>";
            echo "</ul>";
            
            // Test role methods
            echo "<p>Role checks:</p>";
            echo "<ul>";
            echo "<li>isSuperAdmin(): " . ($auth->isSuperAdmin() ? 'TRUE' : 'FALSE') . "</li>";
            echo "<li>isSecAdmin(): " . ($auth->isSecAdmin() ? 'TRUE' : 'FALSE') . "</li>";
            echo "<li>isAdmin(): " . ($auth->isAdmin() ? 'TRUE' : 'FALSE') . "</li>";
            echo "<li>isAnyAdmin(): " . ($auth->isAnyAdmin() ? 'TRUE' : 'FALSE') . "</li>";
            echo "</ul>";
            
            // Logout for next test
            $auth->logout();
            
        } else {
            echo "<p style='color: red;'>❌ Auth::attempt() FAILED for {$email}</p>";
        }
    }
    
    // Step 4: Check database configuration
    echo "<h3>Step 4: Database configuration check...</h3>";
    
    $config = require_once __DIR__ . '/config/database.php';
    if ($config === false) {
        echo "<p style='color: red;'>❌ Failed to load database configuration</p>";
    } else {
        echo "<p>Database config:</p>";
        echo "<ul>";
        echo "<li>Host: " . htmlspecialchars($config['host']) . "</li>";
        echo "<li>Database: " . htmlspecialchars($config['dbname']) . "</li>";
        echo "<li>Username: " . htmlspecialchars($config['username']) . "</li>";
        echo "<li>Charset: " . htmlspecialchars($config['charset']) . "</li>";
        echo "</ul>";
    }
    
    echo "<hr>";
    echo "<h3>🔧 Quick Fix Options:</h3>";
    echo "<p>If you're still having issues, try these:</p>";
    echo "<ol>";
    echo "<li><a href='update_database.php' style='color: blue;'>Run the migration script again</a></li>";
    echo "<li><a href='check_admin.php' style='color: blue;'>Check admin account status</a></li>";
    echo "<li>Clear your browser cookies and try again</li>";
    echo "<li>Check if your web server supports sessions</li>";
    echo "</ol>";
    
    echo "<p><a href='login' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Again</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration.</p>";
}
?>

