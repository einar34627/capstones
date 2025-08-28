<?php
session_start();
require_once '../includes/facial_recognition_api.php';

$api = new FacialRecognitionAPI();
$message = '';
$message_type = '';

if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'register_face') {
        $name = trim($_POST['name']);
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        
        if (empty($name)) {
            $message = 'Please enter a name';
            $message_type = 'error';
        } else {
            // Handle file upload
            if (isset($_FILES['face_image']) && $_FILES['face_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = $api->handleFileUpload($_FILES['face_image']);
                
                if ($upload_result['success']) {
                    $registration_result = $api->registerFace($upload_result['path'], $name, $user_id);
                    
                    if ($registration_result['success']) {
                        $message = $registration_result['message'];
                        $message_type = 'success';
                    } else {
                        $message = $registration_result['message'];
                        $message_type = 'error';
                        // Clean up uploaded file if registration failed
                        unlink($upload_result['path']);
                    }
                } else {
                    $message = $upload_result['message'];
                    $message_type = 'error';
                }
            } else {
                $message = 'Please select an image file';
                $message_type = 'error';
            }
        }
    }
}

// Get registered faces for display
$registered_faces = $api->getRegisteredFaces();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Registration</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
        }
        
        h1, h2 {
            color: #333;
            text-align: center;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #f9f9f9;
            cursor: pointer;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .message.success {
            background: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        
        .faces-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .face-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .face-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        
        .face-card h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        
        .face-card p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 10px;
        }
        
        .back-btn {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 25px;
            margin-bottom: 20px;
            transition: background 0.2s;
        }
        
        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="login.php" class="back-btn">← Back to Login</a>
        
        <h1>Face Registration System</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-section">
            <h2>Register New Face</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="register_face">
                
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="user_id">User ID (Optional):</label>
                    <input type="number" id="user_id" name="user_id" placeholder="Enter user ID if applicable">
                </div>
                
                <div class="form-group">
                    <label for="face_image">Face Image:</label>
                    <input type="file" id="face_image" name="face_image" accept="image/*" required>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Upload a clear photo of the face. Only JPG and PNG files allowed (max 5MB).
                    </small>
                </div>
                
                <button type="submit" class="submit-btn">Register Face</button>
            </form>
        </div>
        
        <div class="form-section">
            <h2>Registered Faces</h2>
            <?php if ($registered_faces['success'] && !empty($registered_faces['faces'])): ?>
                <div class="faces-grid">
                    <?php foreach ($registered_faces['faces'] as $face): ?>
                        <div class="face-card">
                            <?php if ($face['image_path'] && file_exists($face['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($face['image_path']); ?>" alt="Face">
                            <?php else: ?>
                                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjZGRkIi8+Cjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gSW1hZ2U8L3RleHQ+Cjwvc3ZnPgo=" alt="No Image">
                            <?php endif; ?>
                            <h4><?php echo htmlspecialchars($face['name']); ?></h4>
                            <p>ID: <?php echo $face['id']; ?></p>
                            <p>Registered: <?php echo date('M j, Y', strtotime($face['created_at'])); ?></p>
                            <button class="delete-btn" onclick="deleteFace(<?php echo $face['id']; ?>)">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">No faces registered yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function deleteFace(faceId) {
            if (confirm('Are you sure you want to delete this face?')) {
                fetch('face_management.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=delete&face_id=' + faceId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting face: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error);
                });
            }
        }
    </script>
</body>
</html>
