<?php
// Include authentication check
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background-color: #f3f4f6; }
        .header { background-image: url('/capstone/images/baramgay.jpg'); background-size: cover; background-position: center; color: white; padding: 20px; text-align: center; height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; font-size: 1.3rem; }
        .header h1,p { font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif !important; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .admin-info { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .user-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .user-details h2 { color: #333; margin: 0; }
        .user-details p { color: #666; margin: 5px 0; }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .btn-primary { background-color: #2563eb; color: white; }
        .btn-danger { background-color: #dc2626; color: white; }
        .admin-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px; }
        .admin-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .admin-card h3 { color: #333; margin-bottom: 15px; }
        .card-icon { font-size: 3rem; color: #1f2937; margin-bottom: 15px; }
        .speech-bubble-welcome { position: fixed; top: 20px; right: 20px; min-width: 250px; max-width: 280px; padding: 14px 22px; background: #eacc23ef; font-size: 1rem; color: #222; font-weight: 600; z-index: 2000; box-shadow: 0 4px 18px rgba(0,0,0,0.10); display: flex; align-items: center; height: 40px; }

        /* Surveillance Panel Styles */
        .surveillance-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #000; z-index: 9999; display: none; flex-direction: column; }
        .surveillance-header { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white; padding: 15px 20px; text-align: center; display: flex; justify-content: space-between; align-items: center; position: absolute; top: 0; left: 0; right: 0; z-index: 10; }
        .surveillance-header h2 { margin: 0; font-size: 1.5rem; }
        .surveillance-content { display: grid; grid-template-columns: 1fr 380px; gap: 0; height: 100vh; padding-top: 70px; }
        .camera-feed { background: #000; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
        .stop-surveillance-btn { position: absolute; bottom: 30px; right: 30px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 80px; height: 80px; font-size: 24px; cursor: pointer; box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4); transition: all 0.3s ease; z-index: 1000; display: none; }
        .stop-surveillance-btn:hover { background: #dc2626; transform: scale(1.1); }
        .camera-active { position: relative; width: 100%; height: 100%; }
        .video-container { width: 100%; height: 100%; position: relative; }
        .video-container video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .video-container canvas { position: absolute; left: 0; top: 0; width: 100%; height: 100%; }
        @media (max-width: 768px) {
            .surveillance-content { grid-template-columns: 1fr; }
            .recognition-panel { display: none; }
            .video-container video, .video-container canvas { object-fit: contain; }
        }
        .camera-overlay { position: absolute; top: 20px; left: 20px; background: rgba(239, 68, 68, 0.9); color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; z-index: 10; }
        .recognition-panel { background: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-left: 1px solid rgba(255,255,255,0.2); padding: 25px; overflow-y: auto; height: 100%; color: #fff; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: rgba(255,255,255,0.05); border-radius: 10px; padding: 15px; text-align: center; border: 1px solid rgba(255,255,255,0.1); }
        .stat-number { font-size: 1.5rem; font-weight: bold; color: #10b981; margin-bottom: 5px; }
        .stat-label { color: #9ca3af; font-size: 0.8rem; }
        .log { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-left: 4px solid #10b981; border-radius: 10px; padding: 10px 12px; margin-bottom: 10px; }
        .log.unknown { border-left-color: #ef4444; }
        .btn-close { background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; }
        /* Notification button/dropdown */
        .notif-wrapper { position: relative; }
        .notif-btn { position: relative; display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; }
        .notif-btn:hover { background: #f3f4f6; }
        .notif-btn i { color: #1f2937; font-size: 18px; }
        .notif-badge { position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; padding: 0 4px; border-radius: 999px; background: #ef4444; color: #fff; font-size: 12px; line-height: 18px; text-align: center; }
        .notif-dropdown { position: absolute; right: 0; top: 48px; width: 320px; max-height: 380px; overflow-y: auto; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); display: none; z-index: 2000; }
        .notif-dropdown.open { display: block; }
        .notif-header { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #111827; }
        .notif-item { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 10px; align-items: start; }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
        .notif-icon.success { background: #d1fae5; color: #065f46; }
        .notif-icon.fail { background: #fee2e2; color: #991b1b; }
        .notif-text { font-size: 13px; color: #374151; }
        .notif-time { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        <p>Barangay Commonwealth Administration</p>
    </div>
    <div class="speech-bubble-welcome" id="welcomeBubble">Welcome, Admin!</div>

    <div class="container">
        <?php
        // Include database connection for statistics (kept minimal)
        require_once __DIR__ . '/../../includes/Database.php';
        try { $db = Database::getInstance(); $admin_users = $db->fetch("SELECT COUNT(*) as count FROM users WHERE usertype = 'admin'")['count']; } catch (Exception $e) { $admin_users = 0; }
        ?>
        <div class="admin-info">
            <div class="user-info">
                <div class="user-details">
                    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Super Administrator'); ?>!</h2>
                    <p>Email: <?php echo htmlspecialchars($_SESSION['user_email'] ?? 'superadmin@barangay.com'); ?></p>
                    <p>Role: Super Administrator</p>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="notif-wrapper">
                        <button id="notifBtn" class="notif-btn" type="button" aria-haspopup="true" aria-expanded="false" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span id="notifBadge" class="notif-badge" style="display:none;">0</span>
                        </button>
                        <div id="notifDropdown" class="notif-dropdown" role="menu" aria-label="Notifications">
                            <div class="notif-header">Notifications</div>
                            <div id="notifList"></div>
                        </div>
                    </div>
                    <a href="/capstone/welcome" class="btn btn-primary" style="margin-right: 0;">Back to Home</a>
                    <a href="/capstone/logout_handler.php" class="btn btn-danger" style="margin-left: 20px;">Logout</a>
                </div>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-icon">📹</div>
                <h3>Live Surveillance Feed Panel</h3>
                <p>Real-time facial recognition monitoring and surveillance system.</p>
                <button id="startSurveillance" class="btn btn-primary">Start Access</button>
            </div>
            <div class="admin-card">
                <div class="card-icon">👥</div>
                <h3>Face Management</h3>
                <p>Register and manage facial recognition profiles and user identities.</p>
                <a href="<?php echo url('super_admin/face_management'); ?>" class="btn btn-primary">Manage Faces</a>
            </div>
             <div class="admin-card">
                <div class="card-icon">🚨</div>
                <h3>Facial Recognition Alerts</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">🖥️</div>
                <h3>YOLO Intrusion Detection Monitor</h3>
                <p>Configure system settings, notifications, and administrative preferences.</p>
                <a href="admin/settings" class="btn btn-primary">Configure Settings</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📢</div>
                <h3>Incident & Tip Management</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">🗺️</div>
                <h3>Surveillance Heatmap</h3>
                <p>Configure system settings, notifications, and administrative preferences.</p>
                <a href="admin/settings" class="btn btn-primary">Configure Settings</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">🚓</div>
                <h3>Patrol Monitoring & Scheduling</h3>
                <p>Access analytics, feedback reports, and commCLI error: Expecting property name enclosed in double quotes: line 1 column 3 (char 2)unity statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📈</div>
                <h3>Analytics Dashboard</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">⚙️</div>
                <h3>Settings</h3>
                <p>Configure system settings, notifications, and administrative preferences.</p>
                <a href="admin/settings" class="btn btn-primary">Configure Settings</a>
            </div>
            <div class="admin-card">
                <div class="card-icon">📊</div>
                <h3>View Reports</h3>
                <p>Access analytics, feedback reports, and community statistics.</p>
                <a href="admin/reports" class="btn btn-primary">View Reports</a>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Surveillance Overlay -->
    <div id="surveillanceOverlay" class="surveillance-overlay">
        <div class="surveillance-header">
            <h2><i class="fas fa-video"></i> Live Surveillance Feed</h2>
            <button id="closeSurveillance" class="btn-close">Close</button>
        </div>
        <div class="surveillance-content">
            <div class="camera-feed" id="cameraFeed">
                <div class="camera-active">
                    <div class="camera-overlay"><i class="fas fa-circle"></i> LIVE</div>
                    <div class="video-container">
                        <video id="camVideo" autoplay muted playsinline></video>
                        <canvas id="overlayCanvas"></canvas>
                    </div>
                    <button id="stopSurveillanceBtn" class="stop-surveillance-btn" title="Stop Surveillance"><i class="fas fa-stop"></i></button>
                </div>
            </div>
            <div class="recognition-panel">
                <h3><i class="fas fa-user-check"></i> Recognition Panel</h3>
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-number" id="alertsCount">0</div><div class="stat-label">Unknown Faces</div></div>
                </div>
                <div id="detectionLogs"></div>
            </div>
        </div>
    </div>

    <script>
        // Notifications (dropdown)
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifList = document.getElementById('notifList');
        const notifBadge = document.getElementById('notifBadge');
        let notifOpen = false;

        function toggleNotif(open) {
            notifOpen = (open !== undefined) ? open : !notifOpen;
            notifDropdown.classList.toggle('open', notifOpen);
            notifBtn.setAttribute('aria-expanded', notifOpen ? 'true' : 'false');
        }

        async function loadNotifications() {
            try {
                const url = '/capstone/views/super_admin/facial_recognition/face_recognition_api.php?action=list_faces';
                const facesRes = await fetch(url, { cache: 'no-store' });
                let faces = [];
                try { const payload = await facesRes.json(); faces = Array.isArray(payload.faces) ? payload.faces : []; } catch(e) { faces = []; }
                // Basic placeholder: use faces list as notifications (replace later with list_attempts when exposed for all roles)
                const items = faces.slice(-5).reverse().map(f => ({
                    text: `New face registered: ${f.name || 'Unknown'}`,
                    time: f.created_at || '',
                    success: true
                }));
                renderNotifications(items);
            } catch (e) {
                renderNotifications([]);
            }
        }
        function renderNotifications(items) {
            notifList.innerHTML = '';
            if (!items || items.length === 0) {
                const d = document.createElement('div');
                d.className = 'notif-item';
                d.innerHTML = '<div class="notif-text">No notifications</div>';
                notifList.appendChild(d);
                notifBadge.style.display = 'none';
                return;
            }
            let count = 0;
            items.forEach(it => {
                const row = document.createElement('div');
                row.className = 'notif-item';
                const iconClass = it.success ? 'success' : 'fail';
                row.innerHTML = `
                    <div class="notif-icon ${iconClass}"><i class="${it.success ? 'fas fa-check' : 'fas fa-exclamation'}"></i></div>
                    <div>
                        <div class="notif-text">${it.text}</div>
                        <div class="notif-time">${it.time ? new Date(it.time).toLocaleString() : ''}</div>
                    </div>
                `;
                notifList.appendChild(row);
                count++;
            });
            notifBadge.textContent = String(count);
            notifBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
        document.addEventListener('click', (e) => {
            if (!notifDropdown) return;
            if (notifBtn && notifBtn.contains(e.target)) { toggleNotif(); return; }
            if (notifDropdown.contains(e.target)) return;
            toggleNotif(false);
        });
        // Initial load
        loadNotifications();
        // Refresh every 30s
        setInterval(loadNotifications, 30000);

        // Config
        const modelsUrl = 'https://justadudewhohacks.github.io/face-api.js/models';
        // Pull known faces from Python-backed list
        const facesDataUrl = '/capstone/views/super_admin/facial_recognition/face_recognition_api.php?action=list_faces';
        // Recognition thresholds (slightly more permissive for real-time)
        const RECOGNITION_THRESHOLD = 0.55; // More lenient for better recognition
        const MATCH_MARGIN = 0.04;          // smaller margin requirement
        const SECONDARY_THRESHOLD = 0.60;   // allow a bit looser when track is consistent
        const CONSISTENT_FRAMES = 2;
        const DETECT_INTERVAL_MS = 150; // More responsive
        const SMALL_FACE_RATIO = 0.07;            // Faces smaller than this ratio require stricter thresholds
        const SMALL_FACE_STRICT_DELTA = 0.06;     // How much stricter to make thresholds for small faces
        // Tracking and smoothing configuration
        const TRACK_IOU_THRESHOLD = 0.4;          // IoU to associate detections to existing tracks
        const TRACK_DISTANCE_FACTOR = 0.6;        // Center distance threshold factor relative to box size
        const TRACK_ACCEPT_THRESHOLD = 0.60;      // Distance allowed to accept same-label on tracked face
        const TRACK_TTL_MS = 1500;                // Track time-to-live without updates
        let TFD_OPTIONS = null;

        // Elements
        const startBtn = document.getElementById('startSurveillance');
        const closeBtn = document.getElementById('closeSurveillance');
        const stopBtn = document.getElementById('stopSurveillanceBtn');
        const overlay = document.getElementById('surveillanceOverlay');
        const video = document.getElementById('camVideo');
        const canvas = document.getElementById('overlayCanvas');
        const ctx = canvas.getContext('2d');
        const unknownEl = document.getElementById('alertsCount');
        const logs = document.getElementById('detectionLogs');
        // Dedup tracking for logs
        let loggedKnown = new Set();
        let loggedUnknownDescriptors = [];
        const UNKNOWN_DUP_THRESHOLD = 0.5;
        let lastDetectTime = 0;
        let lastRenderData = [];
        let labelStreak = new Map();
        let tracks = [];
        let trackIdCounter = 1;

        function descriptorDistance(a, b) {
            let sum = 0;
            for (let i = 0; i < a.length; i++) { const d = a[i] - b[i]; sum += d * d; }
            return Math.sqrt(sum);
        }
        function isDuplicateUnknown(desc) {
            return loggedUnknownDescriptors.some(u => descriptorDistance(u, desc) < UNKNOWN_DUP_THRESHOLD);
        }

        // State
        let running = false;
        let rafId = null;
        let matcher = null;
        let alertCount = 0;

        function toWebPath(face) {
            if (face.image_path_web) return face.image_path_web;
            if (face.image_path) {
                const parts = face.image_path.split(/[\\/]/);
                return '/capstone/views/super_admin/facial_recognition/uploads/faces/' + parts[parts.length-1];
            }
            return null;
        }

        function addLog(name, known, distance) {
            if (known) return; // Only log unknown faces in panel
            const div = document.createElement('div');
            div.className = 'log unknown';
            div.innerHTML = `<strong>Unknown</strong>: ${name} <div style="font-size:12px;color:#9ca3af;">${new Date().toLocaleString()}${typeof distance==='number' ? ` | distance: ${distance.toFixed(3)}` : ''}</div>`;
            logs.prepend(div);
            while (logs.children.length > 20) logs.removeChild(logs.lastChild);
        }

        async function loadModels() {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(modelsUrl),
                faceapi.nets.faceLandmark68Net.loadFromUri(modelsUrl),
                faceapi.nets.faceRecognitionNet.loadFromUri(modelsUrl)
            ]);
            // Initialize detector options only after face-api is loaded (tuned for responsiveness)
            TFD_OPTIONS = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.4 });
        }

        function loadImage(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        }

        async function buildMatcher() {
                    try {
            const res = await fetch(facesDataUrl, { cache: 'no-store' });
            const payload = await res.json();
            const faces = Array.isArray(payload.faces) ? payload.faces : [];
            console.log('Loaded faces from Python API:', faces.length);
        } catch (e) {
            console.error('Failed to load faces from Python API:', e);
            const faces = [];
        }
            const byLabel = new Map();
            function flipCanvas(srcImg) {
                const c = document.createElement('canvas');
                c.width = srcImg.width;
                c.height = srcImg.height;
                const g = c.getContext('2d');
                g.translate(c.width, 0);
                g.scale(-1, 1);
                g.drawImage(srcImg, 0, 0);
                return c;
            }
            function rotateCanvas(srcImg, degrees) {
                const rad = degrees * Math.PI / 180;
                const s = Math.abs(Math.sin(rad));
                const c = Math.abs(Math.cos(rad));
                const w = srcImg.width;
                const h = srcImg.height;
                const newW = Math.floor(w * c + h * s);
                const newH = Math.floor(w * s + h * c);
                const canvas = document.createElement('canvas');
                canvas.width = newW;
                canvas.height = newH;
                const ctx2 = canvas.getContext('2d');
                ctx2.translate(newW / 2, newH / 2);
                ctx2.rotate(rad);
                ctx2.drawImage(srcImg, -w / 2, -h / 2);
                return canvas;
            }
            for (const f of faces) {
                const url = toWebPath(f);
                if (!url) continue;
                const label = String(f.name || f.id || 'unknown');
                try {
                    const img = await loadImage(url);
                    const det = await faceapi
                        .detectSingleFace(img, TFD_OPTIONS)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                    if (det && det.descriptor) {
                        if (!byLabel.has(label)) byLabel.set(label, []);
                        byLabel.get(label).push(det.descriptor);
                    }
                    // Add a horizontally flipped augmentation
                    try {
                        const flipped = flipCanvas(img);
                        const detFlip = await faceapi
                            .detectSingleFace(flipped, TFD_OPTIONS)
                            .withFaceLandmarks()
                            .withFaceDescriptor();
                        if (detFlip && detFlip.descriptor) {
                            if (!byLabel.has(label)) byLabel.set(label, []);
                            byLabel.get(label).push(detFlip.descriptor);
                        }
                    } catch (e) { /* ignore flip errors */ }
                    // Add small rotation augmentations (+/- 10 degrees)
                    try {
                        for (const deg of [10, -10]) {
                            const rotated = rotateCanvas(img, deg);
                            const detRot = await faceapi
                                .detectSingleFace(rotated, TFD_OPTIONS)
                                .withFaceLandmarks()
                                .withFaceDescriptor();
                            if (detRot && detRot.descriptor) {
                                if (!byLabel.has(label)) byLabel.set(label, []);
                                byLabel.get(label).push(detRot.descriptor);
                            }
                        }
                    } catch (e) { /* ignore rotate errors */ }
                } catch (e) { /* ignore per-image errors */ }
            }
            const labeled = [];
            for (const [label, descriptors] of byLabel.entries()) {
                if (descriptors.length > 0) {
                    labeled.push(new faceapi.LabeledFaceDescriptors(label, descriptors));
                }
            }
            matcher = new faceapi.FaceMatcher(labeled, RECOGNITION_THRESHOLD);
        }

        async function startCamera() {
            const constraints = {
                video: { 
                    facingMode: 'user',
                    width: { ideal: 1280, min: 640 },
                    height: { ideal: 720, min: 480 }
                }, 
                audio: false 
            };
            
            try {
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                video.srcObject = stream;
                await new Promise(r => video.onloadedmetadata = r);
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                console.log('Camera started:', video.videoWidth, 'x', video.videoHeight);
            } catch (e) {
                console.error('Camera access failed:', e);
                alert('Camera access failed. Please allow camera permissions and try again.');
                stopSurveillance();
            }
        }

        function stopCamera() {
            const s = video.srcObject; if (s) { s.getTracks().forEach(t => t.stop()); }
            video.srcObject = null;
        }

        function drawBox(box, color, label) {
            ctx.strokeStyle = color;
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);
            if (label) {
                const pad = 4;
                ctx.font = '16px Segoe UI';
                const w = ctx.measureText(label).width + pad * 2;
                const y = Math.max(0, box.y - 22);
                ctx.fillStyle = color;
                ctx.fillRect(box.x, y, w, 20);
                ctx.fillStyle = '#fff';
                ctx.fillText(label, box.x + pad, y + 14);
            }
        }
        function iou(a, b) {
            const x1 = Math.max(a.x, b.x);
            const y1 = Math.max(a.y, b.y);
            const x2 = Math.min(a.x + a.width, b.x + b.width);
            const y2 = Math.min(a.y + a.height, b.y + b.height);
            const inter = Math.max(0, x2 - x1) * Math.max(0, y2 - y1);
            const areaA = a.width * a.height;
            const areaB = b.width * b.height;
            const uni = areaA + areaB - inter;
            return uni > 0 ? inter / uni : 0;
        }
        function centerDistance(a, b) {
            const ax = a.x + a.width / 2, ay = a.y + a.height / 2;
            const bx = b.x + b.width / 2, by = b.y + b.height / 2;
            return Math.hypot(ax - bx, ay - by);
        }
        function matchTrack(box) {
            // Try IoU first
            let bestIdx = -1, bestScore = 0;
            for (let i = 0; i < tracks.length; i++) {
                const t = tracks[i];
                const sc = iou(box, t.box);
                if (sc > bestScore) { bestScore = sc; bestIdx = i; }
            }
            if (bestScore >= TRACK_IOU_THRESHOLD) return bestIdx;
            // Fallback: center distance relative to box size
            let minDist = Infinity, minIdx = -1;
            const thresh = Math.min(box.width, box.height) * TRACK_DISTANCE_FACTOR;
            for (let i = 0; i < tracks.length; i++) {
                const t = tracks[i];
                const d = centerDistance(box, t.box);
                if (d < minDist) { minDist = d; minIdx = i; }
            }
            return (minDist <= (isFinite(thresh) ? thresh : 0)) ? minIdx : -1;
        }

        async function loop() {
            if (!running) return;
            try {
                if (!TFD_OPTIONS) {
                    rafId = requestAnimationFrame(loop);
                    return;
                }

                const now = performance.now();
                const shouldProcess = (now - lastDetectTime) >= DETECT_INTERVAL_MS;

                if (shouldProcess) {
                    const detections = await faceapi
                        .detectAllFaces(video, TFD_OPTIONS)
                        .withFaceLandmarks()
                        .withFaceDescriptors();

                    const renderData = [];
                    for (const d of detections) {
                        const box = d.detection.box;
                        // Adaptive thresholds based on face size (smaller faces => stricter)
                        const faceArea = box.width * box.height;
                        const frameArea = canvas.width * canvas.height;
                        const faceRatio = frameArea > 0 ? (faceArea / frameArea) : 0;
                        const thr = RECOGNITION_THRESHOLD - (faceRatio < SMALL_FACE_RATIO ? SMALL_FACE_STRICT_DELTA : 0);
                        const secThr = SECONDARY_THRESHOLD - (faceRatio < SMALL_FACE_RATIO ? SMALL_FACE_STRICT_DELTA : 0);
                        let label = 'Unknown';
                        let color = '#ef4444'; // red for unknown
                        let isKnown = false;
                        let distance = null;

                        if (matcher && d.descriptor) {
                            let best = { label: 'unknown', distance: Infinity };
                            let second = { label: 'unknown', distance: Infinity };
                            if (Array.isArray(matcher.labeledDescriptors)) {
                                for (const ld of matcher.labeledDescriptors) {
                                    if (!ld || !Array.isArray(ld.descriptors)) continue;
                                    for (const desc of ld.descriptors) {
                                        const dist = descriptorDistance(desc, d.descriptor);
                                        if (dist < best.distance) {
                                            second = best;
                                            best = { label: ld.label, distance: dist };
                                        } else if (dist < second.distance) {
                                            second = { label: ld.label, distance: dist };
                                        }
                                    }
                                }
                            }
                            distance = isFinite(best.distance) ? best.distance : null;
                            const margin = (!isFinite(second.distance) ? Infinity : (second.distance - best.distance));
                            const trackIdx = matchTrack(box);
                            const hasClearWinner = distance !== null && distance <= thr && margin >= MATCH_MARGIN;
                            if (hasClearWinner && best.label && best.label !== 'unknown') {
                                label = best.label;
                                color = '#10b981'; // green for known
                                isKnown = true;
                                labelStreak.set(best.label, (labelStreak.get(best.label) || 0) + 1);
                            } else if (trackIdx !== -1) {
                                const t = tracks[trackIdx];
                                // If the tracked label matches the current best candidate and distance is acceptable for tracking, accept it
                                if (t.label && best.label && t.label === best.label && distance !== null && distance <= Math.min(TRACK_ACCEPT_THRESHOLD, thr + 0.02)) {
                                    label = best.label;
                                    color = '#10b981';
                                    isKnown = true;
                                } else if (best && best.label && best.label !== 'unknown' && distance !== null && distance <= secThr && margin >= (MATCH_MARGIN / 2) && (t.streak || 0) >= 2) {
                                    // Slightly weaker condition if the track already has some consistency
                                    label = best.label;
                                    color = '#10b981';
                                    isKnown = true;
                                } else {
                                    if (best && best.label) labelStreak.set(best.label, 0);
                                }
                            } else {
                                if (best && best.label) labelStreak.set(best.label, 0);
                            }
                        }

                        renderData.push({ box, label, color });

                        // Update / create track
                        const idx = matchTrack(box);
                        const nowMs = performance.now();
                        if (idx !== -1) {
                            const t = tracks[idx];
                            t.box = box;
                            t.lastSeen = nowMs;
                            if (isKnown) {
                                t.label = label;
                                t.streak = (t.streak || 0) + 1;
                                t.lastBestDistance = distance;
                            } else {
                                t.streak = 0;
                            }
                        } else {
                            tracks.push({ id: trackIdCounter++, box: box, label: isKnown ? label : null, streak: isKnown ? 1 : 0, lastBestDistance: distance, lastSeen: nowMs });
                        }

                        if (isKnown) {
                            if (!loggedKnown.has(label)) {
                                loggedKnown.add(label);
                            }
                        } else {
                            if (d.descriptor && !isDuplicateUnknown(d.descriptor)) {
                                loggedUnknownDescriptors.push(new Float32Array(d.descriptor));
                                alertCount++;
                                addLog(label, false, distance);
                            }
                        }
                    }

                    // Cleanup old tracks
                    const cutoff = performance.now() - TRACK_TTL_MS;
                    tracks = tracks.filter(t => (t.lastSeen || 0) >= cutoff);

                    lastRenderData = renderData;
                    unknownEl.textContent = String(alertCount);
                    lastDetectTime = now;
                }

                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (const item of lastRenderData) {
                    drawBox(item.box, item.color, item.label);
                }
            } catch (e) {
                console.error('Surveillance loop error:', e);
            }

            rafId = requestAnimationFrame(loop);
        }

        async function startSurveillance() {
            if (running) return;
            overlay.style.display = 'flex';
            document.querySelector('.stop-surveillance-btn').style.display = 'block';
            alertCount = 0;
            loggedKnown = new Set();
            loggedUnknownDescriptors = [];
            logs.innerHTML = '';
            unknownEl.textContent = '0';
            labelStreak = new Map();

            // Start camera first for immediate feedback
            try {
                await startCamera();
            } catch (e) {
                console.error('Camera start failed:', e);
                alert('Unable to access camera. Please allow camera permissions and ensure no other app is using it.');
                document.querySelector('.stop-surveillance-btn').style.display = 'none';
                overlay.style.display = 'none';
                return;
            }

            running = true;
            loop();

            // Load models then build matcher from registered faces
            await loadModels();
            await buildMatcher();
        }

        function stopSurveillance() {
            running = false;
            if (rafId) cancelAnimationFrame(rafId);
            stopCamera();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.querySelector('.stop-surveillance-btn').style.display = 'none';
        }

        // UI events
        startBtn.addEventListener('click', () => { startSurveillance(); });
        stopBtn.addEventListener('click', () => { stopSurveillance(); });
        closeBtn.addEventListener('click', () => { stopSurveillance(); overlay.style.display = 'none'; });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) { stopSurveillance(); overlay.style.display = 'none'; }
        });

        // Cleanup
        window.addEventListener('beforeunload', stopSurveillance);
        setTimeout(() => { const b = document.getElementById('welcomeBubble'); if (b) b.style.display = 'none'; }, 3000);
    </script>
</body>
</html>