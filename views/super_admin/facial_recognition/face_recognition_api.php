<?php
session_start();
require_once __DIR__ . '/../../../includes/facial_recognition_api.php';

header('Content-Type: application/json');

// Check if user is super admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'super admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle GET request for listing faces (for dashboard)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list_faces') {
    $api = new FacialRecognitionAPI();
    $result = $api->getRegisteredFaces();
    if (!empty($result['success']) && !empty($result['faces']) && is_array($result['faces'])) {
        $projectRoot = realpath(__DIR__ . '/../../../');
        foreach ($result['faces'] as &$f) {
            if (empty($f['image_path_web']) && !empty($f['image_path'])) {
                $imgReal = realpath($f['image_path']);
                if ($imgReal && $projectRoot && stripos($imgReal, $projectRoot . DIRECTORY_SEPARATOR) === 0) {
                    $rel = substr($imgReal, strlen($projectRoot));
                    $rel = str_replace('\\', '/', $rel);
                    $f['image_path_web'] = '/capstone' . $rel;
                }
            }
        }
        unset($f);
    }
    echo json_encode($result);
    exit;
}

if ($_POST && isset($_POST['action'])) {
    $api = new FacialRecognitionAPI();
    
    if ($_POST['action'] === 'recognize' && isset($_FILES['image'])) {
        // Handle uploaded image for recognition
        $temp_dir = __DIR__ . '/uploads/temp/';
        $web_temp_dir = 'uploads/temp/';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        $upload_result = $api->handleFileUpload($_FILES['image'], $temp_dir);
        
        if ($upload_result['success']) {
            $recognition_result = $api->recognizeFace($upload_result['path'], 0.60);
            
            // Clean up temp file
            if (isset($upload_result['path']) && file_exists($upload_result['path'])) {
                unlink($upload_result['path']);
            }
            
            echo json_encode($recognition_result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to process image']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action or missing image']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
}
?>
