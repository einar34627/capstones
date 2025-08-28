<?php
session_start();
require_once __DIR__ . '/../../../includes/facial_recognition_api.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_POST && isset($_POST['action'])) {
    $api = new FacialRecognitionAPI();
    
    if ($_POST['action'] === 'delete' && isset($_POST['face_id'])) {
        $result = $api->deleteFace((int)$_POST['face_id']);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
}
?>
