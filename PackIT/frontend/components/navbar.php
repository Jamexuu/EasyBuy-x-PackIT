<?php
// Frontend navbar for PackIT (notifications: View feedback -> myFeedback.php)

$BASE_URL = '/EasyBuy-x-PackIT/PackIT';

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

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

<div class="container">
    <nav class="navbar navbar-expand-lg my-3 mx-auto rounded-pill shadow px-4 py-2"
         style="max-width:95%; background:#f8e15b;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= htmlspecialchars(u('index.php')) ?>">
                <img src="<?= htmlspecialchars(u('assets/LOGO.svg')) ?>" alt="PackIT" height="40" class="object-fit-contain">
            </a>

            <div class="d-flex align-items-center gap-2 gap-lg-3 order-lg-3">
                <?php if ($loggedIn): ?>
                    <div class="dropdown position-relative" id="notifRoot">
                        <button id="notifToggleBtn"
                                class="btn btn-sm rounded-pill d-flex align-items-center justify-content-center position-relative border-0"
                                style="background:rgba(255,255,255,.55); width:42px; height:42px;"
                                aria-expanded="false"
                                title="Notifications"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside">
                            <i class="bi bi-bell-fill text-dark" style="font-size:1.05rem;"></i>

                            <span id="notifBadge"
                                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill d-none"
                                  style="background:#dc3545; font-size:.70rem; padding:.28rem .45rem;">
                                0
                            </span>
                        </button>

                        <div id="notifDropdown"
                             class="dropdown-menu dropdown-menu-end p-0 border-0 shadow-lg mt-2"
                             style="width:360px; max-width:calc(100vw - 24px); border-radius:18px;"
                             aria-labelledby="notifToggleBtn">

                            <div class="px-3 py-3"
                                 style="background:#f8e15b; border-top-left-radius:18px; border-top-right-radius:18px;">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                                             style="width:38px;height:38px;background:rgba(0,0,0,.10);">
                                            <i class="bi bi-bell-fill text-dark"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark lh-1">Notifications</div>
                                            <div class="small text-dark" style="opacity:.75;">Feedback replies & updates</div>
                                        </div>
                                    </div>

                                    <button id="notifMarkAllRead"
                                            class="btn btn-sm btn-dark rounded-pill px-3"
                                            type="button">
                                        Mark all
                                    </button>
                                </div>
                            </div>

                            <div id="notifList" class="p-2" style="max-height:340px; overflow:auto; background:#fff;"></div>

                            <div class="p-2 border-top bg-white"
                                 style="border-bottom-left-radius:18px; border-bottom-right-radius:18px;">
                                <a href="<?= htmlspecialchars(u('frontend/myFeedback.php')) ?>"
                                   class="btn btn-light w-100 rounded-pill fw-semibold">
                                    View feedback
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="width:42px;height:42px;"></div>
                <?php endif; ?>

                <?php if (!$loggedIn): ?>
                    <a href="<?= htmlspecialchars(u('frontend/login.php')) ?>"
                       class="text-dark text-decoration-none fw-bold text-uppercase lh-1 d-none d-sm-block"
                       style="font-size:0.8rem;">
                        Login/<br>Signup
                    </a>
                <?php else: ?>
                    <div class="d-none d-sm-flex align-items-center gap-2">
                        <a href="<?= htmlspecialchars(u('frontend/profile.php')) ?>"
                           class="text-dark text-decoration-none fw-bold text-uppercase lh-1"
                           style="font-size:0.85rem;">
                            <?= htmlspecialchars($userName ?: 'Profile') ?>
                        </a>
                    </div>
                <?php endif; ?>

                <button class="navbar-toggler border-0 p-0 ms-2" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="offcanvas offcanvas-end rounded-start-5" tabindex="-1" id="offcanvasNavbar">
                <div class="offcanvas-header" style="background:#f8e15b;">
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
                                <a class="nav-link text-dark <?= $isActive ? 'fw-bolder text-decoration-underline' : '' ?>"
                                   href="<?= htmlspecialchars($href) ?>">
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
  const goToFeedbackUrl = '<?= htmlspecialchars(u("frontend/myFeedback.php")) ?>';
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
      list.innerHTML = `
        <div class="p-3">
          <div class="text-center text-muted small py-4">
            <div class="mb-2"><i class="bi bi-inbox fs-3"></i></div>
            No new notifications
          </div>
        </div>
      `;
      return;
    }

    items.forEach((it) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'w-100 text-start border-0 bg-white p-0';

      const status = (it.status || 'open').toString();

      btn.innerHTML = `
        <div class="p-3 rounded-4" style="transition: background .15s;">
          <div class="d-flex gap-3 align-items-start">
            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                 style="width:38px;height:38px;background:#f8e15b;">
              <i class="bi bi-chat-square-text text-dark"></i>
            </div>

            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="fw-semibold text-dark">
                  ${escapeHtml(it.subject || 'Feedback update')}
                </div>
                <div class="text-muted small flex-shrink-0">
                  ${escapeHtml(it.time || '')}
                </div>
              </div>

              <div class="text-muted small mt-1">
                ${escapeHtml(it.excerpt || '')}
              </div>

              <div class="mt-2">
                <span class="badge rounded-pill text-bg-light border">
                  ${escapeHtml(status)}
                </span>
              </div>
            </div>
          </div>
        </div>
      `;

      btn.addEventListener('mouseenter', () => {
        btn.firstElementChild.style.background = '#fff7cc';
      });
      btn.addEventListener('mouseleave', () => {
        btn.firstElementChild.style.background = '#ffffff';
      });

      btn.addEventListener('click', async () => {
        try {
          const res = await fetch(notifMarkReadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
            body: new URLSearchParams({ id: it.id, csrf_token: csrfToken })
          });
          const j = await res.json().catch(() => null);
          if (!res.ok || !j?.success) console.error('Mark read failed:', j);
        } catch (e) {
          console.error('Mark read request failed:', e);
        }
        window.location.href = goToFeedbackUrl;
      });

      list.appendChild(btn);
    });
  }

  async function fetchNotifications(alsoRender = false) {
    if (!userLoggedIn) return;
    try {
      const res = await fetch(notifFetchUrl, { credentials: 'same-origin' });
      const j = await res.json().catch(() => null);
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

  fetchNotifications(false);
  setInterval(() => fetchNotifications(false), 25000);
})();
</script>