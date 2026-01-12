<?php
session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Feedback - PackIT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-white d-flex flex-column min-vh-100">

<?php include(__DIR__ . '/components/navbar.php'); ?>

<main class="container my-4 flex-grow-1">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h4 class="fw-bold mb-1">My Feedback</h4>
      <div class="text-muted small">All your feedback submissions and admin replies.</div>
    </div>

    <div class="d-flex gap-2">
      <a href="profile.php#feedback" class="btn btn-light rounded-pill">
        <i class="bi bi-arrow-left me-1"></i>Back to Profile
      </a>
      <button id="refreshBtn" class="btn btn-dark rounded-pill" type="button">
        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
      </button>
    </div>
  </div>

  <div class="p-3 rounded-4 border mb-3" style="background:#fff7cc;">
    <div class="d-flex gap-2 align-items-start">
      <i class="bi bi-info-circle mt-1"></i>
      <div class="small text-dark">
        Tip: If an admin replies, you’ll see it here. New replies may also appear in the bell notifications.
      </div>
    </div>
  </div>

  <div id="loading" class="text-center py-5 text-muted">
    <div class="spinner-border" role="status"></div>
    <div class="small mt-2">Loading your feedback…</div>
  </div>

  <div id="empty" class="text-center py-5 text-muted d-none">
    <div class="mb-2"><i class="bi bi-inbox fs-1"></i></div>
    <div class="fw-semibold">No feedback yet</div>
    <div class="small">Go to Profile → Feedback to create one.</div>
  </div>

  <div id="list" class="vstack gap-3 d-none"></div>
</main>

<?php include(__DIR__ . '/components/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const fetchUrl = 'myFeedbackFetch.php';
  const loadingEl = document.getElementById('loading');
  const emptyEl = document.getElementById('empty');
  const listEl = document.getElementById('list');
  const refreshBtn = document.getElementById('refreshBtn');

  function esc(s){
    if (!s) return '';
    return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function statusBadgeClass(status){
    const s = (status || 'open').toLowerCase();
    if (['resolved','completed','delivered','success'].includes(s)) return 'text-bg-success';
    if (['pending','open','processing'].includes(s)) return 'text-bg-warning';
    if (['closed','failed','cancelled','rejected','error'].includes(s)) return 'text-bg-danger';
    return 'text-bg-secondary';
  }

  function show(el){ el.classList.remove('d-none'); }
  function hide(el){ el.classList.add('d-none'); }

  function render(items){
    listEl.innerHTML = '';

    if (!items || items.length === 0) {
      hide(loadingEl);
      hide(listEl);
      show(emptyEl);
      return;
    }

    items.forEach(row => {
      const hasReply = row.admin_reply && String(row.admin_reply).trim() !== '';
      const updatedAt = row.replied_at || row.acknowledged_at || row.created_at || '';
      const unread = Number(row.user_unread || 0) === 1 && hasReply;

      const card = document.createElement('div');
      card.className = 'border rounded-4 p-4 bg-white';
      card.style.boxShadow = unread ? '0 0 0 2px rgba(248,225,91,.95)' : 'none';

      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div>
            <div class="fw-bold text-dark">${esc(row.subject || 'No subject')}</div>
            <div class="small text-muted">
              ${esc(row.category || 'other')} • #${esc(row.id)} • ${esc(row.created_at || '')}
            </div>
          </div>
          <div class="text-end">
            <span class="badge rounded-pill ${statusBadgeClass(row.status)}">${esc(row.status || 'open')}</span>
            <div class="small text-muted mt-1">${esc(updatedAt)}</div>
          </div>
        </div>

        <hr class="my-3">

        <div class="small text-secondary mb-3">
          <div class="fw-semibold text-dark mb-1">Your message</div>
          <div>${esc(row.message || '')}</div>
        </div>

        <div class="small text-secondary">
          <div class="fw-semibold text-dark mb-1">Admin reply</div>
          ${
            hasReply
              ? `<div class="p-3 rounded-4 border bg-light">${esc(row.admin_reply)}</div>`
              : `<div class="text-muted fst-italic">No reply yet.</div>`
          }
        </div>
      `;

      listEl.appendChild(card);
    });

    hide(loadingEl);
    hide(emptyEl);
    show(listEl);
  }

  async function load(){
    show(loadingEl);
    hide(emptyEl);
    hide(listEl);

    try {
      const res = await fetch(fetchUrl, { credentials: 'same-origin' });
      const data = await res.json().catch(() => null);
      if (!res.ok || !data || !data.success) {
        render([]);
        return;
      }
      render(data.items || []);
    } catch (e) {
      console.error('Load my feedback failed:', e);
      render([]);
    }
  }

  if (refreshBtn) refreshBtn.addEventListener('click', load);

  load();
})();
</script>
</body>
</html>