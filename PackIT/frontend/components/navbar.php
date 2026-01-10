<?php
// Frontend navbar for PackIT (updated with notifications bell - uses camelCase endpoints)

$BASE_URL = '/EasyBuy-x-PackIT/PackIT';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Ensure CSRF token exists (needed by notificationsMarkRead.php)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page = basename($_SERVER['PHP_SELF']);

function u($path)
{
    global $BASE_URL;
    return rtrim($BASE_URL, '/') . '/' . ltrim($path, '/');
}

$loggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
$userName = $loggedIn ? trim(($_SESSION['user']['firstName'] ?? '') . ' ' . ($_SESSION['user']['lastName'] ?? '')) : '';
$csrfToken = $_SESSION['csrf_token'];
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root { --brand-yellow: #f8e15b; --brand-dark: #111; }
    .bg-brand { background-color: var(--brand-yellow) !important; }

    .notif-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #d63384;
        color: #fff;
        font-size: 10px;
        padding: 2px 5px;
        border-radius: 999px;
        line-height: 1;
        box-shadow: 0 0 0 2px rgba(255,255,255,0.6);
    }
    .notif-dropdown { width: 320px; max-width: calc(100vw - 40px); }
    .notif-item { cursor: pointer; }
    .notif-item .small-excerpt { font-size: 0.85rem; color: #444; }
    .notif-empty { padding: 12px; color: #666; }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars(u('index.php')) ?>">
                <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center gap-2 gap-lg-3 order-lg-3">
                <?php if ($loggedIn): ?>
                    <div class="position-relative" id="notifRoot">
                        <button id="notifToggleBtn" class="btn btn-sm btn-white text-dark p-0 border-0"
                                aria-expanded="false" title="Notifications"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="bi bi-bell" style="font-size:1.4rem;"></i>
                        </button>

                        <span id="notifBadge" class="notif-badge d-none">0</span>

                        <div id="notifDropdown" class="dropdown-menu dropdown-menu-end notif-dropdown shadow-lg mt-2" aria-labelledby="notifToggleBtn">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <strong>Notifications</strong>
                                <button id="notifMarkAllRead" class="btn btn-link btn-sm" type="button">Mark all read</button>
                            </div>

                            <div id="notifList" style="max-height: 320px; overflow:auto;"></div>

                            <div class="border-top text-center">
                                <a href="<?= htmlspecialchars(u('frontend/profile.php#feedback')) ?>" class="d-block text-decoration-none py-2">
                                    View feedback
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="width:34px;height:34px;"></div>
                <?php endif; ?>

                <?php if (! $loggedIn): ?>
                    <a href="<?= htmlspecialchars(u('frontend/login.php')) ?>" class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-sm-block" style="font-size: 0.8rem;">
                        Login/<br>Signup
                    </a>
                <?php else: ?>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <a href="<?= htmlspecialchars(u('frontend/profile.php')) ?>" class="text-dark text-decoration-none fw-bold text-uppercase lh-1" style="font-size:0.85rem;">
                            <?= htmlspecialchars($userName ?: 'Profile') ?>
                        </a>
                    </div>
                <?php endif; ?>

                <button class="navbar-toggler border-0 p-0 ms-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="offcanvas offcanvas-end rounded-start-5" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header bg-brand">
                    <h5 class="offcanvas-title fw-bold">MENU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-center flex-grow-1 gap-3 text-uppercase small fw-bold">
                        <?php
                        $navItems = [
                            'index.php' => 'Home',
                            'frontend/vehicle.php' => 'Vehicles',
                            'frontend/transaction.php' => 'Transactions',
                        ];

                        foreach ($navItems as $path => $label):
                            $isActive = ($page === basename($path));
                            $href = u($path);
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-dark <?= $isActive ? 'fw-bolder text-decoration-underline' : '' ?>" href="<?= htmlspecialchars($href) ?>">
                                    <?= htmlspecialchars($label) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <li class="nav-item d-sm-none border-top pt-3 mt-2">
                            <?php if ($loggedIn): ?>
                                <a href="<?= htmlspecialchars(u('frontend/profile.php')) ?>" class="nav-link text-dark">
                                    <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($userName) ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars(u('frontend/login.php')) ?>" class="nav-link text-dark">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login / Signup
                                </a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </nav>
</div>

<script>
(function(){
  const notifFetchUrl = '<?= htmlspecialchars(u("frontend/notificationsFetch.php")) ?>';
  const notifMarkReadUrl = '<?= htmlspecialchars(u("frontend/notificationsMarkRead.php")) ?>';
  const csrfToken = <?= json_encode($csrfToken) ?>;
  const userLoggedIn = <?= $loggedIn ? 'true' : 'false' ?>;

  const badge = document.getElementById('notifBadge');
  const list = document.getElementById('notifList');
  const markAllBtn = document.getElementById('notifMarkAllRead');
  const toggleBtn = document.getElementById('notifToggleBtn');

  function setBadge(count) {
    if (!badge) return;
    if (!count || count <= 0) {
      badge.classList.add('d-none');
    } else {
      badge.textContent = count > 99 ? '99+' : String(count);
      badge.classList.remove('d-none');
    }
  }

  function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function renderNotifications(items) {
    if (!list) return;
    list.innerHTML = '';

    if (!items || items.length === 0) {
      const empty = document.createElement('div');
      empty.className = 'notif-empty';
      empty.textContent = 'No new notifications';
      list.appendChild(empty);
      return;
    }

    items.forEach((it) => {
      const row = document.createElement('div');
      row.className = 'dropdown-item notif-item';
      row.dataset.id = it.id;

      row.innerHTML =
        '<div class="d-flex justify-content-between align-items-start">' +
          '<div>' +
            '<strong>' + escapeHtml(it.subject || 'Feedback update') + '</strong>' +
            '<div class="small-excerpt">' + escapeHtml(it.excerpt || '') + '</div>' +
          '</div>' +
          '<div class="text-muted small ms-2">' + escapeHtml(it.time || '') + '</div>' +
        '</div>';

      row.addEventListener('click', async () => {
        try {
          const res = await fetch(notifMarkReadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
            body: new URLSearchParams({ id: it.id, csrf_token: csrfToken })
          });
          const j = await res.json().catch(() => null);
          if (!res.ok || !j?.success) {
            console.error('Mark read failed:', j);
          }
        } catch (e) {
          console.error('Mark read request failed:', e);
        }
        window.location.href = '<?= htmlspecialchars(u("frontend/profile.php#feedback")) ?>';
      });

      list.appendChild(row);
    });
  }

  async function fetchNotifications(alsoRender = false) {
    if (!userLoggedIn) return;
    try {
      const res = await fetch(notifFetchUrl, { credentials: 'same-origin' });
      const j = await res.json();
      if (!res.ok || !j) return;

      setBadge(j.count || 0);
      if (alsoRender) renderNotifications(j.items || []);
    } catch (err) {
      console.error('Failed to fetch notifications', err);
    }
  }

  async function markAllRead() {
    if (!userLoggedIn) return;
    try {
      const res = await fetch(notifMarkReadUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
        body: new URLSearchParams({ csrf_token: csrfToken })
      });
      const j = await res.json().catch(() => null);

      if (!res.ok || !j || !j.success) {
        console.error('Mark all read failed:', j);
        alert(j?.message || 'Mark all read failed');
        return;
      }

      setBadge(0);
      renderNotifications([]);
    } catch (err) {
      console.error('Failed to mark notifications read', err);
    }
  }

  // When dropdown opens, fetch & render
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      setTimeout(() => fetchNotifications(true), 50);
    });
  }

  if (markAllBtn) {
    markAllBtn.addEventListener('click', (e) => {
      e.preventDefault();
      markAllRead();
    });
  }

  // Poll
  fetchNotifications(false);
  setInterval(() => fetchNotifications(false), 25000);
})();
</script>