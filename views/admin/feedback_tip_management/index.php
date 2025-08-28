<?php
require_once __DIR__ . '/../auth_check.php';
// Admin-only check; adjust to your role key if necessary
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}
$projectRoot = dirname(__DIR__, 3); // .../capstone
$dataFile = $projectRoot . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'notifications.json';
$notifications = [];
if (file_exists($dataFile)) {
    $raw = file_get_contents($dataFile);
    $arr = json_decode($raw, true);
    if (is_array($arr)) $notifications = $arr;
}
function filterByType($arr, $type) {
    return array_values(array_filter($arr, function($n) use ($type) { return isset($n['type']) && $n['type'] === $type; }));
}
$tips = filterByType($notifications, 'tip');
$feedback = filterByType($notifications, 'feedback');
$initialTab = isset($_GET['type']) && in_array($_GET['type'], ['tip','feedback'], true) ? $_GET['type'] : 'tip';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback & Tip Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
 body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f3f4f6; }
 .container { max-width: 1200px; margin: 30px auto; padding: 0 16px; }
 h1 { margin: 0 0 20px; }
 .tabs { display: flex; gap: 10px; margin-bottom: 20px; }
 .tab-btn { padding: 10px 16px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; cursor: pointer; font-weight: 600; }
 .tab-btn.active { background: #1e40af; color: #fff; border-color: #1e40af; }
 .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; }
 .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
 .card h2 { margin: 0 0 12px; font-size: 1.1rem; color: #1f2937; }
 .item { border: 1px solid #f1f5f9; border-radius: 8px; padding: 12px; margin-bottom: 10px; }
 .meta { font-size: 12px; color: #6b7280; margin-top: 6px; }
 .actions { margin-top: 10px; display: flex; gap: 8px; }
 .btn { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
 .btn-approve { background: #10b981; color: #fff; }
 .btn-decline { background: #ef4444; color: #fff; }
 .status { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; margin-left: 8px; }
 .st-pending { background: #fde68a; color: #92400e; }
 .st-approved { background: #d1fae5; color: #065f46; }
 .st-declined { background: #fee2e2; color: #991b1b; }
 .back { display: inline-block; margin-bottom: 16px; color: #1e40af; text-decoration: none; font-weight: 600; }
</style>
</head>
<body>
  <div class="container">
    <a class="back" href="<?php echo '/capstone/admin/adashboard'; ?>"><i class="fas fa-arrow-left"></i> Back to Admin Dashboard</a>
    <h1>Feedback & Tip Management</h1>
    <div class="tabs">
      <button class="tab-btn" id="tabTip">Tips</button>
      <button class="tab-btn" id="tabFeedback">Feedback</button>
    </div>
    <div id="tipsPanel" class="panel">
      <div class="grid">
        <div class="card">
          <h2>Pending Tips</h2>
          <div id="tipsPending"></div>
        </div>
        <div class="card">
          <h2>Approved Tips</h2>
          <div id="tipsApproved"></div>
        </div>
        <div class="card">
          <h2>Declined Tips</h2>
          <div id="tipsDeclined"></div>
        </div>
      </div>
    </div>
    <div id="feedbackPanel" class="panel" style="display:none;">
      <div class="grid">
        <div class="card">
          <h2>Pending Feedback</h2>
          <div id="fbPending"></div>
        </div>
        <div class="card">
          <h2>Approved Feedback</h2>
          <div id="fbApproved"></div>
        </div>
        <div class="card">
          <h2>Declined Feedback</h2>
          <div id="fbDeclined"></div>
        </div>
      </div>
    </div>
  </div>
<script>
  const initialTab = '<?php echo htmlspecialchars($initialTab, ENT_QUOTES); ?>';
  const tabTip = document.getElementById('tabTip');
  const tabFeedback = document.getElementById('tabFeedback');
  const tipPanel = document.getElementById('tipsPanel');
  const fbPanel = document.getElementById('feedbackPanel');
  function setTab(tab) {
    const isTip = (tab === 'tip');
    tabTip.classList.toggle('active', isTip);
    tabFeedback.classList.toggle('active', !isTip);
    tipPanel.style.display = isTip ? 'block' : 'none';
    fbPanel.style.display = isTip ? 'none' : 'block';
  }
  tabTip.addEventListener('click', () => setTab('tip'));
  tabFeedback.addEventListener('click', () => setTab('feedback'));
  setTab(initialTab);

  async function fetchList(type, status) {
    const params = new URLSearchParams({ action: 'list' });
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    const res = await fetch('/capstone/views/api/notifications.php?' + params.toString(), { cache: 'no-store' });
    const data = await res.json();
    if (data && data.success && Array.isArray(data.notifications)) return data.notifications;
    return [];
  }
  function renderItems(container, items, type) {
    container.innerHTML = '';
    if (!items || items.length === 0) { container.innerHTML = '<div class="meta">No items</div>'; return; }
    for (const n of items) {
      const div = document.createElement('div');
      div.className = 'item';
      const status = n.status || 'pending';
      const stClass = status === 'approved' ? 'st-approved' : (status === 'declined' ? 'st-declined' : 'st-pending');
      div.innerHTML = `
        <div><strong>${(n.text||'').replace(/</g,'&lt;')}</strong> <span class="status ${stClass}">${status}</span></div>
        <div class="meta">${n.timestamp ? new Date(n.timestamp).toLocaleString() : ''}</div>
        ${type==='tip' && n.media ? `<div class=\"meta\">Picture: ${n.media.picture ? `<a href=\"${n.media.picture}\" target=\"_blank\">view</a>` : ''} | Video: ${n.media.video ? `<a href=\"${n.media.video}\" target=\"_blank\">view</a>` : ''}</div>` : ''}
        ${type==='feedback' ? `<div class=\"meta\">Rating: ${n.rating||''} ★</div>` : ''}
        <div class="actions">
          <button class="btn btn-approve" data-id="${n.id}" data-status="approved">Approve</button>
          <button class="btn btn-decline" data-id="${n.id}" data-status="declined">Decline</button>
        </div>
      `;
      container.appendChild(div);
    }
  }
  async function loadAll() {
    // Tips
    const tipsPending = await fetchList('tip','pending');
    const tipsApproved = await fetchList('tip','approved');
    const tipsDeclined = await fetchList('tip','declined');
    renderItems(document.getElementById('tipsPending'), tipsPending, 'tip');
    renderItems(document.getElementById('tipsApproved'), tipsApproved, 'tip');
    renderItems(document.getElementById('tipsDeclined'), tipsDeclined, 'tip');
    // Feedback
    const fbPending = await fetchList('feedback','pending');
    const fbApproved = await fetchList('feedback','approved');
    const fbDeclined = await fetchList('feedback','declined');
    renderItems(document.getElementById('fbPending'), fbPending, 'feedback');
    renderItems(document.getElementById('fbApproved'), fbApproved, 'feedback');
    renderItems(document.getElementById('fbDeclined'), fbDeclined, 'feedback');
  }
  async function updateStatus(id, status) {
    const params = new URLSearchParams({ action: 'update_status', id, status });
    const res = await fetch('/capstone/views/api/notifications.php?' + params.toString(), { cache: 'no-store' });
    const data = await res.json();
    return data && data.success;
  }
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('button[data-id][data-status]');
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const status = btn.getAttribute('data-status');
    btn.disabled = true; btn.textContent = 'Saving...';
    const ok = await updateStatus(id, status);
    if (!ok) { alert('Update failed'); }
    await loadAll();
  });
  loadAll();
</script>
</body>
</html>