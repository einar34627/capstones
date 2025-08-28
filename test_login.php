<?php
// Simple Test Login Script
// This bypasses the form and tests login directly

session_start();

echo "<h2>🧪 Test Login Script</h2>";
echo "<p>Testing login functionality directly...</p>";

try {
    require_once __DIR__ . '/includes/Auth.php';
    $auth = new Auth();
    
    // Test credentials
    $testCredentials = [
        ['email' => 'superadmin@caps.com', 'password' => 'admin123'],
        ['email' => 'secadmin@caps.com', 'password' => 'admin123'],
        ['email' => 'admin@caps.com', 'password' => 'admin123']
    ];
    
    foreach ($testCredentials as $cred) {
        echo "<h3>Testing: {$cred['email']}</h3>";
        
        // Test login
        $result = $auth->attempt($cred);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Login SUCCESS!</p>";
            echo "<p>Session data:</p>";
            echo "<ul>";
            echo "<li>user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "</li>";
            echo "<li>user_name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "</li>";
            echo "<li>user_email: " . ($_SESSION['user_email'] ?? 'NOT SET') . "</li>";
            echo "<li>user_type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "</li>";
            echo "<li>user_level: " . ($_SESSION['user_level'] ?? 'NOT SET') . "</li>";
            echo "</ul>";
            
            // Test redirect
            echo "<p>Testing redirect...</p>";
            $auth->redirectBasedOnRole();
            
        } else {
            echo "<p style='color: red;'>❌ Login FAILED!</p>";
        }
        
        // Clear session for next test
        $auth->logout();
        echo "<hr>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
