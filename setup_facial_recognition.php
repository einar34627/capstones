<?php
// Setup script for facial recognition system
echo "Setting up Facial Recognition System...\n";

// Create necessary directories
$directories = [
    'facial_recognition',
    'uploads',
    'uploads/faces'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Created directory: $dir\n";
        } else {
            echo "✗ Failed to create directory: $dir\n";
        }
    } else {
        echo "✓ Directory already exists: $dir\n";
    }
}

// Check Python requirements
echo "\nChecking Python requirements...\n";
$python_check = shell_exec('python3 --version 2>&1');
if (strpos($python_check, 'Python') !== false) {
    echo "✓ Python3 is available\n";
    
    // Install Python requirements
    echo "Installing Python requirements...\n";
    $install_result = shell_exec('cd facial_recognition && pip3 install -r requirements.txt 2>&1');
    echo $install_result;
} else {
    echo "✗ Python3 is not available. Please install Python 3.7+ and try again.\n";
}

echo "\nSetup complete! You can now use the facial recognition system.\n";
echo "1. Register faces at: views/face_registration.php\n";
echo "2. Login with face recognition at: views/face_login.php\n";
?>
