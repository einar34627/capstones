<?php
// Simple notifications endpoint for tips and feedback
// Stores notifications in a JSON file and serves them to dashboards.

header('Content-Type: application/json');

$projectRoot = dirname(__DIR__, 2); // .../capstone
$dataDir = $projectRoot . DIRECTORY_SEPARATOR . 'data';
$uploadsDir = $projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'tips';
$file = $dataDir . DIRECTORY_SEPARATOR . 'notifications.json';

if (!is_dir($dataDir)) { @mkdir($dataDir, 0755, true); }
if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0755, true); }
if (!file_exists($file)) { file_put_contents($file, json_encode([])); }

function read_notifications($file) {
    try {
        $raw = file_get_contents($file);
        $arr = json_decode($raw, true);
        return is_array($arr) ? $arr : [];
    } catch (Exception $e) {
        return [];
    }
}

function write_notifications($file, $arr) {
    file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : ($_POST['action'] ?? null);

if ($method === 'GET') {
    if ($action === 'list') {
        $list = read_notifications($file);
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filtered = array_values(array_filter($list, function($n) use ($type, $status) {
            if ($type && (!isset($n['type']) || $n['type'] !== $type)) return false;
            if ($status && (!isset($n['status']) || $n['status'] !== $status)) return false;
            return true;
        }));
        echo json_encode(['success' => true, 'notifications' => $filtered]);
        exit;
    }
    if ($action === 'count') {
        $list = read_notifications($file);
        echo json_encode(['success' => true, 'count' => count($list)]);
        exit;
    }
    if ($action === 'update_status') {
        // Update notification status by id
        $id = isset($_GET['id']) ? $_GET['id'] : ($_POST['id'] ?? '');
        $status = isset($_GET['status']) ? $_GET['status'] : ($_POST['status'] ?? '');
        if (!$id || !in_array($status, ['approved','declined','pending'], true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid id or status']);
            exit;
        }
        $list = read_notifications($file);
        $updated = false;
        foreach ($list as &$n) {
            if (isset($n['id']) && $n['id'] === $id) { $n['status'] = $status; $updated = true; break; }
        }
        unset($n);
        if ($updated) {
            write_notifications($file, $list);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not found']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

if ($method === 'POST') {
    if ($action === 'submit_tip') {
        // Fields: name (optional), address, picture (file), video (file)
        $name = trim($_POST['name'] ?? 'Anonymous');
        $address = trim($_POST['address'] ?? '');
        $errors = [];
        if (empty($address)) { $errors[] = 'address required'; }
        if (!isset($_FILES['picture']) || $_FILES['picture']['error'] !== UPLOAD_ERR_OK) { $errors[] = 'picture required'; }
        if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) { $errors[] = 'video required'; }
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
            exit;
        }
        // Save files
        $saved = [];
        foreach (['picture','video'] as $key) {
            $orig = basename($_FILES[$key]['name']);
            $ext = pathinfo($orig, PATHINFO_EXTENSION);
            $fname = uniqid($key . '_') . ($ext ? ('.' . $ext) : '');
            $dest = $uploadsDir . DIRECTORY_SEPARATOR . $fname;
            if (!move_uploaded_file($_FILES[$key]['tmp_name'], $dest)) {
                echo json_encode(['success' => false, 'message' => 'Failed to save ' . $key]);
                exit;
            }
            $saved[$key] = '/capstone/uploads/tips/' . $fname;
        }
        // Create notification
        $list = read_notifications($file);
        $list[] = [
            'id' => uniqid('n_'),
            'type' => 'tip',
            'text' => 'Tip from ' . ($name ?: 'Anonymous') . ' at ' . $address,
            'name' => $name,
            'address' => $address,
            'media' => $saved,
            'status' => 'pending',
            'timestamp' => date('c')
        ];
        write_notifications($file, $list);
        echo json_encode(['success' => true, 'message' => 'Tip submitted', 'saved' => $saved]);
        exit;
    }
    if ($action === 'submit_feedback') {
        // Fields: rating, message, street (optional)
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $message = trim($_POST['message'] ?? '');
        $street = trim($_POST['street'] ?? '');
        if ($rating < 1 || $rating > 5 || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid rating or message']);
            exit;
        }
        $list = read_notifications($file);
        $list[] = [
            'id' => uniqid('n_'),
            'type' => 'feedback',
            'text' => 'Feedback (' . $rating . '★): ' . $message . ($street ? (' — ' . $street) : ''),
            'rating' => $rating,
            'message' => $message,
            'street' => $street,
            'status' => 'pending',
            'timestamp' => date('c')
        ];
        write_notifications($file, $list);
        echo json_encode(['success' => true, 'message' => 'Feedback submitted']);
        exit;
    }
    if ($action === 'clear_all') {
        // Clear all notifications
        write_notifications($file, []);
        echo json_encode(['success' => true, 'message' => 'All notifications cleared']);
        exit;
    }
    if ($action === 'delete') {
        // Delete a single notification by id
        $id = isset($_POST['id']) ? $_POST['id'] : ($_GET['id'] ?? '');
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing id']);
            exit;
        }
        $list = read_notifications($file);
        $new = array_values(array_filter($list, function($n) use ($id) {
            return !isset($n['id']) || $n['id'] !== $id;
        }));
        write_notifications($file, $new);
        echo json_encode(['success' => true, 'message' => 'Deleted', 'remaining' => count($new)]);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unsupported method']);
