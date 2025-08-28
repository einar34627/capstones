<?php
class FacialRecognitionAPI {
    private $python_script_path;
    private $face_db_path;
    private $python_bin;
    
    public function __construct() {
        $this->python_script_path = __DIR__ . '/../facial_recognition/face_recognition_system.py';
        $this->face_db_path = __DIR__ . '/../facial_recognition/face_database.db';
        // Allow configuration via env or constant or config.php
        $this->python_bin = getenv('PYTHON_PATH') ?: (defined('PYTHON_BIN') ? PYTHON_BIN : null);
        if (!$this->python_bin) {
            $config_path = __DIR__ . '/../config.php';
            if (file_exists($config_path)) {
                $cfg = include $config_path;
                if (is_array($cfg) && !empty($cfg['python_path'])) {
                    $this->python_bin = $cfg['python_path'];
                }
            }
        }
    }
    
    /**
     * Call Python facial recognition system
     */
    private function callPythonAPI($data) {
        $json_data = json_encode($data);
        $script = $this->python_script_path;
        $is_windows = stripos(PHP_OS, 'WIN') === 0;

        // Determine python binary
        $candidates = [];
        if ($this->python_bin) {
            $candidates[] = $this->python_bin;
        }
        if ($is_windows) {
            $candidates = array_merge($candidates, ['python', 'py', 'py -3']);
        } else {
            $candidates = array_merge($candidates, ['python3', 'python']);
        }

        $last_error = null;
        $attempted = [];
        foreach ($candidates as $python) {
            // Build command
            $cmd_parts = [];
            // When candidate contains a space (e.g., 'py -3'), don't escape as a single token
            $python_parts = preg_split('/\s+/', $python);
            foreach ($python_parts as $pp) { $cmd_parts[] = escapeshellcmd($pp); }
            $cmd_parts[] = escapeshellarg($script);
            // Write JSON to a temp file to avoid Windows CLI quoting issues
            $tmpfile = tempnam(sys_get_temp_dir(), 'frq_');
            $arg_to_pass = $json_data;
            if ($tmpfile !== false) {
                file_put_contents($tmpfile, $json_data);
                $arg_to_pass = $tmpfile;
            }
            $cmd_parts[] = escapeshellarg($arg_to_pass);
            $command = implode(' ', $cmd_parts);
            $attempted[] = $python;

            // Use proc_open to capture stdout/stderr and exit code
            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            $cwd = dirname($script);
            $process = proc_open($command, $descriptorspec, $pipes, $cwd);
            if (!is_resource($process)) {
                $last_error = 'Failed to start Python process';
                continue;
            }
            fclose($pipes[0]);
            stream_set_blocking($pipes[1], true);
            stream_set_blocking($pipes[2], true);
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $status = proc_close($process);

            // Cleanup temp file
            if (isset($tmpfile) && $tmpfile && file_exists($tmpfile)) {
                @unlink($tmpfile);
            }

            if ($stdout) {
                $decoded = json_decode($stdout, true);
                if (is_array($decoded)) {
                    return $decoded; // Success
                }
            }
            // If here, capture error and try next candidate
            $last_error = "exit=$status stdout=" . substr($stdout ?? '', 0, 4000) . " stderr=" . substr($stderr ?? '', 0, 4000);
        }

        return [
            'success' => false,
            'message' => 'Python did not return output. Configure PYTHON_PATH or PYTHON_BIN.',
            'detail' => $last_error,
            'attempted' => $attempted,
        ];
    }
    
    /**
     * Register a new face
     */
    public function registerFace($image_path, $name, $user_id = null) {
        $data = [
            'action' => 'register',
            'image_path' => $image_path,
            'name' => $name,
            'user_id' => $user_id
        ];
        
        return $this->callPythonAPI($data);
    }
    
    /**
     * Recognize a face
     */
    public function recognizeFace($image_path, $confidence_threshold = 0.6) {
        // Ensure absolute path for Python
        $abs_path = realpath($image_path) ?: $image_path;
        $data = [
            'action' => 'recognize',
            'image_path' => $abs_path,
            // Send a slightly more permissive default tolerance (~0.55) for better real-time matches
            'confidence_threshold' => $confidence_threshold ?: 0.55
        ];
        
        return $this->callPythonAPI($data);
    }
    
    /**
     * Capture face from camera
     */
    public function captureFace($output_path = 'captured_face.jpg') {
        $abs_output = $output_path;
        if (!preg_match('/^([a-zA-Z]:\\\\|\\\\|\/)/', $output_path)) {
            $abs_output = __DIR__ . '/../facial_recognition/' . $output_path;
        }
        $data = [
            'action' => 'capture',
            'output_path' => $abs_output
        ];
        
        return $this->callPythonAPI($data);
    }
    
    /**
     * Get all registered faces
     */
    public function getRegisteredFaces() {
        $data = ['action' => 'list_faces'];
        return $this->callPythonAPI($data);
    }
    
    /**
     * Delete a registered face
     */
    public function deleteFace($face_id) {
        $data = [
            'action' => 'delete_face',
            'face_id' => $face_id
        ];
        
        return $this->callPythonAPI($data);
    }
    
    /**
     * Handle file upload for face registration
     */
    public function handleFileUpload($file, $upload_dir = 'uploads/faces/') {
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        
        if (!in_array($file['type'], $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG and PNG allowed.'];
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            return ['success' => false, 'message' => 'File too large. Maximum 5MB allowed.'];
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'path' => $filepath];
        } else {
            return ['success' => false, 'message' => 'Failed to upload file.'];
        }
    }
}
?>
