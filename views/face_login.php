<?php
session_start();
require_once '../includes/facial_recognition_api.php';

$api = new FacialRecognitionAPI();
$message = '';
$message_type = '';

// Handle face recognition login
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'capture_and_login') {
        $result = $api->captureFace('uploads/faces/temp_capture.jpg');
        
        if ($result['success']) {
            $recognition_result = $api->recognizeFace($result['path']);
            
            if ($recognition_result['success']) {
                // Face recognized - you can integrate this with your existing user system
                $_SESSION['face_recognized'] = true;
                $_SESSION['recognized_user'] = $recognition_result['name'];
                $_SESSION['confidence'] = $recognition_result['confidence'];
                
                header('Location: welcome.php');
                exit;
            } else {
                $message = $recognition_result['message'];
                $message_type = 'error';
            }
        } else {
            $message = $result['message'];
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(255,255,255,0.95);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.18);
            width: 400px;
            max-width: 90vw;
            padding: 40px;
            text-align: center;
        }
        
        .login-container h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .camera-container {
            width: 100%;
            height: 300px;
            background: #f0f0f0;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .camera-placeholder {
            color: #666;
            font-size: 1.1rem;
        }
        
        .capture-btn {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        
        .capture-btn:hover {
            transform: translateY(-2px);
        }
        
        .back-btn {
            width: 100%;
            padding: 12px;
            border-radius: 25px;
            border: 2px solid #667eea;
            background: transparent;
            color: #667eea;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .back-btn:hover {
            background: #667eea;
            color: white;
        }
        
        .message {
            padding: 12px;
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
        
        .instructions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .instructions h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .instructions ul {
            margin: 0;
            padding-left: 20px;
            color: #666;
        }
        
        .instructions li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Face Recognition Login</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="instructions">
            <h4>Instructions:</h4>
            <ul>
                <li>Position your face in the center of the camera</li>
                <li>Ensure good lighting for better recognition</li>
                <li>Look directly at the camera</li>
                <li>Click "Capture & Login" to proceed</li>
            </ul>
        </div>
        
        <div class="camera-container">
            <div class="camera-placeholder">
                Camera will activate when you click "Capture & Login"
            </div>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="capture_and_login">
            <button type="submit" class="capture-btn">
                📷 Capture & Login
            </button>
        </form>
        
        <a href="login.php" class="back-btn">
            ← Back to Regular Login
        </a>
    </div>
    
    <script>
        // Add some interactive feedback
        document.querySelector('.capture-btn').addEventListener('click', function() {
            this.textContent = '🔄 Processing...';
            this.disabled = true;
        });
    </script>
</body>
</html>
