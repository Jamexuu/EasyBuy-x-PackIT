<?php
// Frontend navbar for PackIT (updated with notifications bell - uses camelCase endpoints)
// Place at: frontend/components/navbar.php

// Base URL for your project on localhost — update if your folder/location changes
$BASE_URL = '/EasyBuy-x-PackIT/PackIT';

// Ensure session is started (some pages may have started it already)
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Current page filename (used to mark active link)
$page = basename($_SERVER['PHP_SELF']);

// Helper to build full URL
function u($path)
{
    global $BASE_URL;
    return rtrim($BASE_URL, '/') . '/' . ltrim($path, '/');
}

// Simple user detection (set by your login logic)
$loggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
$userName = $loggedIn ? trim(($_SESSION['user']['firstName'] ?? '') . ' ' . ($_SESSION['user']['lastName'] ?? '')) : '';
$userId = $loggedIn ? (int)($_SESSION['user']['id']) : null;

// Provide CSRF token to JS if present
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --brand-yellow: #f8e15b;
        --brand-dark: #111;
    }

    .bg-brand {
        background-color: var(--brand-yellow) !important;
    }

    /* Notification badge small red dot */
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

    .notif-dropdown {
        width: 320px;
        max-width: calc(100vw - 40px);
    }

    .notif-item {
        cursor: pointer;
    }

    .notif-item .small-excerpt {
        font-size: 0.85rem;
        color: #444;
    }

    .notif-empty {
        padding: 12px;
        color: #666;
    }
</style>

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2 bg-brand" style="max-width: 95%;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars(u('index.php')) ?>">
                <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center gap-2 gap-lg-3 order-lg-3">
                <!-- Notifications bell -->
                <?php if ($loggedIn): ?>
                    <div class="position-relative" id="notifRoot">
                        <button id="notifToggleBtn" class="btn btn-sm btn-white text-dark p-0 border-0" aria-expanded="false" title="Notifications" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="bi bi-bell" style="font-size:1.4rem;"></i>
                        </button>

                        <!-- badge -->
                        <span id="notifBadge" class="notif-badge d-none">0</span>

                        <!-- dropdown -->
                        <div id="notifDropdown" class="dropdown-menu dropdown-menu-end notif-dropdown shadow-lg mt-2" aria-labelledby="notifToggleBtn">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <strong>Notifications</strong>
                                <button id="notifMarkAllRead" class="btn btn-link btn-sm">Mark all read</button>
                            </div>
                            <div id="notifList" style="max-height: 320px; overflow:auto;"></div>
                            <div class="border-top text-center">
                                <a href="<?= htmlspecialchars(u('frontend/profile.php')) ?>" class="d-block text-decoration-none py-2">View feedback</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- empty placeholder to keep alignment -->
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
                        // Standard Nav Items
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
  // Configured backend endpoints (PHP will fill URLs)
  const notifFetchUrl = '<?= htmlspecialchars(u("frontend/notificationsFetch.php")) ?>';
  const notifMarkReadUrl = '<?= htmlspecialchars(u("frontend/notificationsMarkRead.php")) ?>';
  const csrfToken = <?= json_encode($csrfToken) ?>;
  const userLoggedIn = <?= $loggedIn ? 'true' : 'false' ?>;

  // UI elements
  const badge = document.getElementById('notifBadge');
  const list = document.getElementById('notifList');
  const markAllBtn = document.getElementById('notifMarkAllRead');
  const dropdownEl = document.getElementById('notifDropdown');
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
    items.forEach(function(it) {
      const a = document.createElement('div');
      a.className = 'dropdown-item notif-item';
      a.dataset.id = it.id;
      a.innerHTML = '<div class="d-flex justify-content-between align-items-start"><div><strong>' + escapeHtml(it.subject || 'Feedback update') + '</strong><div class="small-excerpt">' + escapeHtml(it.excerpt || '') + '</div></div><div class="text-muted small ms-2">' + escapeHtml(it.time) + '</div></div>';
      a.addEventListener('click', function(){
        // On click, mark this notification read and navigate to profile/feedback area
        fetch(notifMarkReadUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' },
          body: new URLSearchParams({ id: it.id, csrf_token: csrfToken })
        }).then(() => {
          window.location.href = '<?= htmlspecialchars(u("frontend/profile.php")) ?>';
        }).catch(() => {
          window.location.href = '<?= htmlspecialchars(u("frontend/profile.php")) ?>';
        });
      });
      list.appendChild(a);
    });
  }

  function escapeHtml(s) {
    if (!s) return '';
    return s.replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
  }

  async function fetchNotifications() {
    if (!userLoggedIn) return;
    try {
      const res = await fetch(notifFetchUrl, { credentials: 'same-origin' });
      const j = await res.json();
      if (!res.ok || !j) return;
      setBadge(j.count || 0);
      if (document.getElementById('notifDropdown').classList.contains('show')) {
        renderNotifications((j.items || []).map(it => ({
          id: it.id,
          subject: it.subject,
          excerpt: it.excerpt,
          time: it.time,
        })));
      }
    } catch (err) {
      console.error('Failed to fetch notifications', err);
    }
  }

  // Mark all unread as read
  async function markAllRead() {
    if (!userLoggedIn) return;
    try {
      const res = await fetch(notifMarkReadUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' },
        body: new URLSearchParams({ csrf_token: csrfToken }) // no id => mark all
      });
      const j = await res.json();
      if (res.ok && j && j.success) {
        setBadge(0);
        renderNotifications([]);
      }
    } catch (err) {
      console.error('Failed to mark notifications read', err);
    }
  }

  // When dropdown opens, fetch and render and then mark them read after a short delay
  if (toggleBtn) {
    toggleBtn.addEventListener('click', async function(){
      setTimeout(async function(){
        try {
          const res = await fetch(notifFetchUrl, { credentials: 'same-origin' });
          const j = await res.json();
          if (res.ok && j) {
            renderNotifications((j.items || []).map(it => ({
              id: it.id,
              subject: it.subject,
              excerpt: it.excerpt,
              time: it.time,
            })));
            // Mark all read on open (you may remove this behaviour if you want explicit user action)
            await fetch(notifMarkReadUrl, {
              method: 'POST',
              credentials: 'same-origin',
              headers: { 'Accept': 'application/json' },
              body: new URLSearchParams({ csrf_token: csrfToken })
            });
            setBadge(0);
          }
        } catch (err) {
          console.error(err);
        }
      }, 150);
    });
  }

  if (markAllBtn) {
    markAllBtn.addEventListener('click', function(e){
      e.preventDefault();
      markAllRead();
    });
  }

  // Poll every 25 seconds
  fetchNotifications();
  setInterval(fetchNotifications, 25000);

})();
</script>