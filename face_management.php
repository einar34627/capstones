<?php
session_start();
require_once 'includes/facial_recognition_api.php';

header('Content-Type: application/json');

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
