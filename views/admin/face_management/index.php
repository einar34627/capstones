<?php
// Include authentication check
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../../includes/facial_recognition_api.php';
$api = new FacialRecognitionAPI();

$message = '';
$message_type = '';

// Base paths for storing and serving images
$uploads_dir_fs = __DIR__ . '/../../super_admin/facial_recognition/uploads/faces/';
$uploads_dir_web = '/capstone/views/super_admin/facial_recognition/uploads/faces/';

// Debug: Log all POST data
if ($_POST) {
    error_log("POST data received: " . print_r($_POST, true));
}

// Handle face registration
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'register_face') {
    $name = trim($_POST['name']);
    
    if (empty($name)) {
        $message = 'Please enter a name';
        $message_type = 'error';
    } else {
        if (isset($_FILES['face_image']) && $_FILES['face_image']['error'] === UPLOAD_ERR_OK) {
            try {
                // File upload logic
                if (!is_dir($uploads_dir_fs)) {
                    mkdir($uploads_dir_fs, 0755, true);
                }
                
                // Check file type and size
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($_FILES['face_image']['type'], $allowed_types)) {
                    $message = 'Invalid file type. Only JPG and PNG allowed.';
                    $message_type = 'error';
                } elseif ($_FILES['face_image']['size'] > 5 * 1024 * 1024) {
                    $message = 'File too large. Maximum 5MB allowed.';
                    $message_type = 'error';
                } else {
                    $filename = uniqid() . '_' . basename($_FILES['face_image']['name']);
                    $filepath_fs = $uploads_dir_fs . $filename;

                    if (move_uploaded_file($_FILES['face_image']['tmp_name'], $filepath_fs)) {
                        // Register the face in Python system (SQLite) so Super Admin can see it
                        $register = $api->registerFace($filepath_fs, $name, $_SESSION['user_id'] ?? null);
                        if (!empty($register['success'])) {
                            // Set success message and prepare for redirect
                            $_SESSION['face_registration_success'] = 'Register Success!!';
                            $message = 'Register Success!!';
                            $message_type = 'success';
                            $redirect_to_super_admin = true;
                        } else {
                            $detail = isset($register['detail']) ? (' Detail: ' . (is_string($register['detail']) ? $register['detail'] : json_encode($register['detail']))) : '';
                            $message = (!empty($register['message']) ? $register['message'] : 'Registration failed in recognition system') . $detail;
                            $message_type = 'error';
                            // Clean up file on failure
                            if (file_exists($filepath_fs)) { @unlink($filepath_fs); }
                        }
                    } else {
                        $message = 'Failed to upload file.';
                        $message_type = 'error';
                    }
                }
            } catch (Exception $e) {
                $message = 'System error: ' . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            $message = 'Please select an image file';
            $message_type = 'error';
        }
    }
}

// Handle face deletion (Python system)
if (isset($_POST['action']) && $_POST['action'] === 'delete_face') {
    $face_id = $_POST['face_id'] ?? '';
    if ($face_id !== '') {
        $delete = $api->deleteFace((int)$face_id);
        if (!empty($delete['success'])) {
            $message = 'Face deleted successfully!';
            $message_type = 'success';
        } else {
            $message = !empty($delete['message']) ? $delete['message'] : 'Failed to delete face';
            $message_type = 'error';
        }
    } else {
        $message = 'Face not found.';
        $message_type = 'error';
    }
}


// Get registered faces from Python system (for stats)
$registered_faces = $api->getRegisteredFaces();
if (empty($registered_faces['success'])) {
    $registered_faces = ['success' => false, 'faces' => [], 'message' => $registered_faces['message'] ?? 'Failed to load faces'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Management - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            min-height: 100vh;
        }
        
        .header {
            background: rgba(0,0,0,0.3);
            color: white;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .header h1 {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            font-size: 2.5rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .content-grid {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .card {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        
        .card h2 {
            color: #1e3a8a;
            margin-bottom: 25px;
            font-size: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        
        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #1e3a8a;
            outline: none;
        }
        
        input[type="file"] {
            background: #f9fafb;
            cursor: pointer;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
        }
        
        .btn-back {
            background: #6b7280;
            color: white;
            margin-bottom: 20px;
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #dc2626;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        
        .btn-delete:active {
            transform: scale(0.95);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .message.success {
            background: #d1fae5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        
        .faces-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .face-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            transition: transform 0.3s;
        }
        
        .face-card:hover {
            transform: translateY(-5px);
        }
        
        .face-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
            border: 3px solid #e5e7eb;
        }
        
        .face-info h3 {
            color: #1f2937;
            margin: 0 0 10px 0;
            text-align: center;
        }
        
        .face-info p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 0.9rem;
            text-align: center;
        }
        
        .face-actions {
            text-align: center;
            margin-top: 15px;
        }
        
        .stats-section {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1e3a8a  ;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-user-cog"></i> Face Management System</h1>
        <p>Register and manage facial recognition profiles</p>
    </div>
    
    <div class="container">
        <a href="/capstone/admin/adashboard" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Back to Admin Dashboard
        </a>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-section">
            <h2><i class="fas fa-chart-bar"></i> System Statistics</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($registered_faces['faces'] ?? []); ?></div>
                    <div class="stat-label">Registered Faces</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php 
                        $faces = $registered_faces['faces'] ?? [];
                        if (!empty($faces)) {
                            $last_face = end($faces);
                            echo date('Y-m-d') === date('Y-m-d', strtotime($last_face['created_at'] ?? 'now')) ? 1 : 0;
                        } else {
                            echo 0;
                        }
                    ?></div>
                    <div class="stat-label">Added Today</div>
                </div>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: <?php echo $message_type === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $message_type === 'success' ? '#065f46' : '#991b1b'; ?>; border: 1px solid <?php echo $message_type === 'success' ? '#a7f3d0' : '#fecaca'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!$registered_faces['success']): ?>
            <div class="alert alert-error" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                <?php echo htmlspecialchars($registered_faces['message'] ?? 'Failed to load faces'); ?>
            </div>
        <?php endif; ?>

        <div class="content-grid">
            <div class="card">
                <h2><i class="fas fa-user-plus"></i> Register New Face</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="register_face">
                    
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required placeholder="Enter full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="face_image">Face Image:</label>
                        <input type="file" id="face_image" name="face_image" accept="image/*" required>
                        <small style="color: #6b7280; display: block; margin-top: 5px;">
                            Upload a clear photo of the face. Only JPG and PNG files allowed (max 5MB).
                        </small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Register Face
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($redirect_to_super_admin) && $redirect_to_super_admin): ?>
    <script>
        // Show success message for 2 seconds, then redirect to an allowed route for the current user
        setTimeout(function() {
            var target = '<?php echo addslashes(url((isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super_admin') ? 'super_admin/face_management' : 'admin/face_management')); ?>';
            window.location.href = target;
        }, 2000);
    </script>
    <?php endif; ?>
</body>
</html>
