<?php
// Include authentication check
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../../../includes/facial_recognition_api.php';

$message = '';
$message_type = '';
$api = new FacialRecognitionAPI();

// Check for success message from admin face registration
if (isset($_SESSION['face_registration_success'])) {
    $message = $_SESSION['face_registration_success'];
    $message_type = 'success';
    unset($_SESSION['face_registration_success']); // Clear the message after displaying
}

// Debug: Log all POST data
if ($_POST) {
    error_log("POST data received: " . print_r($_POST, true));
}

// Handle face registration
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'register_face') {     
        $name = trim($_POST['name']);
        
        if (empty($name)) {
            $message = 'Please enter a name';
            $message_type = 'error';
        } else {
            if (isset($_FILES['face_image']) && $_FILES['face_image']['error'] === UPLOAD_ERR_OK) {
                try {
                    // File upload logic
                    $upload_dir = __DIR__ . '/uploads/faces/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
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
                        $filepath = $upload_dir . $filename;

                        if (move_uploaded_file($_FILES['face_image']['tmp_name'], $filepath)) {
                            // Register the face in Python system (SQLite)
                            $absPath = realpath($filepath) ?: $filepath;
                            $register = $api->registerFace($absPath, $name, $_SESSION['user_id'] ?? null);
                            if (!empty($register['success'])) {
                                $message = 'Face registered successfully!';
                                $message_type = 'success';
                            } else {
                                $detail = isset($register['detail']) ? (' Detail: ' . (is_string($register['detail']) ? $register['detail'] : json_encode($register['detail']))) : '';
                                $attempted = isset($register['attempted']) ? (' Attempted: ' . (is_array($register['attempted']) ? implode(', ', $register['attempted']) : $register['attempted'])) : '';
                                $message = (!empty($register['message']) ? $register['message'] : 'Registration failed in recognition system') . $detail . $attempted;
                                $message_type = 'error';
                                // Clean up file on failure
                                if (file_exists($filepath)) {
                                    unlink($filepath);
                                }
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
}
    // Handle face deletion
    if ($_POST && isset($_POST['action']) && $_POST['action'] === 'delete_face') {
        $face_id = $_POST['face_id'] ?? '';
        if ($face_id) {
            $delete = $api->deleteFace($face_id);
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

    
// Get registered faces from Python system
$registered_faces = $api->getRegisteredFaces();
if (empty($registered_faces['success'])) {
    // Surface backend details to the UI when listing fails
    $detail = isset($registered_faces['detail']) ? (' Detail: ' . (is_string($registered_faces['detail']) ? $registered_faces['detail'] : json_encode($registered_faces['detail']))) : '';
    $attempted = isset($registered_faces['attempted']) ? (' Attempted: ' . (is_array($registered_faces['attempted']) ? implode(', ', $registered_faces['attempted']) : $registered_faces['attempted'])) : '';
    if (empty($message)) {
        $message = (!empty($registered_faces['message']) ? $registered_faces['message'] : 'Failed to load faces') . $detail . $attempted;
        $message_type = 'error';
    } else {
        $message .= $detail . $attempted;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Management - Super Admin</title>
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
            display: center;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
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
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
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
            color: #1e3a8a;
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
        <a href="/capstone/super_admin/dashboard" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Back to Super Admin Dashboard
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
                                 <!-- User linking statistic removed -->
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
                <h2><i class="fas fa-users"></i> Registered Faces</h2>
                <?php 
                    $faces = $registered_faces['faces'] ?? [];
                    if ($registered_faces['success'] && !empty($faces)): 
                ?>
                    <div class="faces-grid">
                        <?php foreach ($faces as $face): ?>
                            <div class="face-card">
                                <?php 
                                    $imgSrc = '';
                                    $imgReal = null;
                                    if (!empty($face['image_path_web'])) {
                                        $imgSrc = $face['image_path_web'];
                                    } elseif (!empty($face['image_path'])) {
                                        // Try to normalize any absolute path inside the project and build a web URL
                                        $imgReal = realpath($face['image_path']);
                                        $projectRoot = realpath(__DIR__ . '/../../../'); // .../capstone
                                        if ($imgReal && $projectRoot && stripos($imgReal, $projectRoot . DIRECTORY_SEPARATOR) === 0) {
                                            $rel = substr($imgReal, strlen($projectRoot));
                                            $rel = str_replace('\\', '/', $rel);
                                            $imgSrc = '/capstone' . $rel;
                                        } else {
                                            // Fallback: if it's under the known uploads dir, build URL from basename
                                            $uploadsDirFsReal = realpath(__DIR__ . '/uploads/faces/');
                                            if ($uploadsDirFsReal && $imgReal && stripos($imgReal, $uploadsDirFsReal . DIRECTORY_SEPARATOR) === 0) {
                                                $imgSrc = '/capstone/views/super_admin/facial_recognition/uploads/faces/' . basename($imgReal);
                                            }
                                        }
                                    }
                                ?>
                                <?php if (!empty($imgSrc) && $imgReal && file_exists($imgReal)): ?>
                                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Face" class="face-image">
                                <?php else: ?>
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZGRkIi8+Cjx0ZXh0IHg9IjQwIiB5PSI0MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+Cjwvc3ZnPgo=" alt="No Image" class="face-image">
                                <?php endif; ?>
                                <div class="face-info">
                                    <h3><?php echo htmlspecialchars($face['name']); ?></h3>
                                                                         <p><strong>ID:</strong> <?php echo $face['id']; ?></p>
                                    <p><strong>Registered:</strong> <?php echo date('M j, Y', strtotime($face['created_at'])); ?></p>
                                </div>
                                <div class="face-actions">
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_face">
                                        <input type="hidden" name="face_id" value="<?php echo htmlspecialchars($face['id']); ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #6b7280; font-style: italic;">
                        No faces registered yet. Start by registering a new face above.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function deleteFace(faceId) {
            console.log('Delete function called with face ID:', faceId);
            
            if (confirm('Are you sure you want to delete this face? This action cannot be undone.')) {
                console.log('User confirmed deletion');
                
                // Find the delete button and show loading state
                const deleteBtn = event.target.closest('.btn-delete');
                if (deleteBtn) {
                    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                    deleteBtn.disabled = true;
                    deleteBtn.style.opacity = '0.7';
                }
                
                // Create a form to submit the delete action
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_face';
                
                const faceIdInput = document.createElement('input');
                faceIdInput.type = 'hidden';
                faceIdInput.name = 'face_id';
                faceIdInput.value = faceId;
                
                form.appendChild(actionInput);
                form.appendChild(faceIdInput);
                document.body.appendChild(form);
                
                console.log('Form created and ready to submit');
                console.log('Action:', actionInput.value);
                console.log('Face ID:', faceIdInput.value);
                
                // Submit the form
                form.submit();
            } else {
                console.log('User cancelled deletion');
            }
        }
    </script>
</body>
</html>
