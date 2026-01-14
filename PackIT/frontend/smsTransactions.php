<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../api/classes/Database.php';

$userId = null;
if (!empty($_SESSION['user']['id'])) {
    $userId = (int)$_SESSION['user']['id'];
} elseif (!empty($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
}

if (!$userId) {
    header("Location: login.php");
    exit;
}

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function toE164Ph(?string $raw): ?string {
    if ($raw === null) return null;
    $s = trim($raw);
    if ($s === '') return null;

    if (str_starts_with($s, '+')) {
        return preg_replace('/[^\d\+]/', '', $s);
    }

    $digits = preg_replace('/\D+/', '', $s);
    if ($digits === '') return null;

    if (str_starts_with($digits, '09') && strlen($digits) === 11) {
        return '+63' . substr($digits, 1);
    }
    if (str_starts_with($digits, '9') && strlen($digits) === 10) {
        return '+63' . $digits;
    }
    if (str_starts_with($digits, '63')) {
        return '+'.$digits;
    }
    return $digits;
}

$db = new Database();
$pdo = $db->pdo();

$smsRows = [];
$userE164 = null;

if ($pdo instanceof PDO) {
    // user number
    $uStmt = $pdo->prepare("SELECT contact_number FROM users WHERE id = :uid LIMIT 1");
    $uStmt->execute([':uid' => $userId]);
    $userContact = (string)($uStmt->fetchColumn() ?: '');
    $userE164 = toE164Ph($userContact);

    if ($userE164) {
        $stmt = $pdo->prepare("
            SELECT
                Id,
                BookingId,
                DriverId,
                Status,
                RecipientNumber,
                Message,
                IsSent,
                ErrorMessage,
                IsRead,
                ReadAt,
                CreatedAt
            FROM smslogs
            WHERE RecipientNumber = :num
            ORDER BY CreatedAt DESC
            LIMIT 500
        ");
        $stmt->execute([':num' => $userE164]);
        $smsRows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Mark all unread as read when user opens this page
        $markStmt = $pdo->prepare("
            UPDATE smslogs
            SET IsRead = 1, ReadAt = NOW()
            WHERE RecipientNumber = :num AND (IsRead = 0 OR IsRead IS NULL)
        ");
        $markStmt->execute([':num' => $userE164]);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SMS Transactions - PackIT</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
<?php include __DIR__ . '/components/navbar.php'; ?>

<main class="flex-grow-1 py-4">
  <div class="container">
    <div class="bg-white p-4 p-md-5"
         style="border: 3px solid #f8e14b; border-radius: 28px; box-shadow: 0 8px 20px rgba(0,0,0,0.06);">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
          <h3 class="fw-bold m-0">SMS TRANSACTIONS</h3>
          <div class="text-muted small mt-1">
            Showing SMS logs for: <span class="fw-semibold"><?= h($userE164 ?: 'Unknown number') ?></span>
          </div>
        </div>

        <div class="w-100 w-md-auto" style="max-width: 360px;">
          <input id="searchInput" type="text"
                 class="form-control border-0"
                 placeholder="🔍 Search booking id, status, message..."
                 style="background-color: #f1f3f5; border-radius: 20px; padding: 10px 20px;">
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-borderless table-hover" style="border-collapse: separate; border-spacing: 0 10px;">
          <thead>
            <tr>
              <th style="background-color: #f8e14b; padding: 14px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">Date</th>
              <th style="background-color: #f8e14b; padding: 14px;">SMS ID</th>
              <th style="background-color: #f8e14b; padding: 14px;">Booking</th>
              <th style="background-color: #f8e14b; padding: 14px;">Status</th>
              <th style="background-color: #f8e14b; padding: 14px;">Sent</th>
              <th style="background-color: #f8e14b; padding: 14px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Message</th>
            </tr>
          </thead>
          <tbody id="sms-body"></tbody>
        </table>
      </div>

      <div id="pageInfo" class="text-center text-muted small mt-3"></div>
      <nav aria-label="SMS pages" class="mt-3">
        <ul class="pagination justify-content-center" id="paginationControls"></ul>
      </nav>

      <?php if (!empty($smsRows)) : ?>
        <div class="mt-4 small text-muted">
          Tip: Opening this page marks your SMS notifications as read.
        </div>
      <?php endif; ?>

    </div>
  </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
const rawData = <?= json_encode($smsRows, JSON_UNESCAPED_UNICODE) ?>;

const itemsPerPage = 8;
let currentPage = 1;
let filteredData = [...rawData];

const tableBody = document.getElementById('sms-body');
const paginationControls = document.getElementById('paginationControls');
const pageInfo = document.getElementById('pageInfo');
const searchInput = document.getElementById('searchInput');

function esc(s){
  if (!s) return '';
  return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

function badgeClassSent(isSent){
  return Number(isSent) === 1 ? 'text-bg-success' : 'text-bg-danger';
}

function renderTable() {
  tableBody.innerHTML = '';

  if (!filteredData.length) {
    tableBody.innerHTML = `<tr>
      <td colspan="6" class="text-center text-muted py-5 rounded-3 bg-white">
        No SMS logs found.
      </td>
    </tr>`;
    pageInfo.innerText = '';
    paginationControls.innerHTML = '';
    return;
  }

  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const pageItems = filteredData.slice(start, end);

  pageItems.forEach(item => {
    const created = item.CreatedAt ? new Date(item.CreatedAt.replace(' ', 'T')) : null;
    const dateText = created ? created.toLocaleString() : (item.CreatedAt || '');

    const tr = document.createElement('tr');
    tr.style.boxShadow = "0 3px 8px rgba(0,0,0,0.05)";
    tr.innerHTML = `
      <td class="text-secondary" style="background:#fff; vertical-align:middle; padding:16px; border-top-left-radius:12px; border-bottom-left-radius:12px;">
        ${esc(dateText)}
      </td>
      <td class="fw-bold" style="background:#fff; vertical-align:middle; padding:16px;">
        #${esc(item.Id)}
      </td>
      <td style="background:#fff; vertical-align:middle; padding:16px;">
        ${item.BookingId ? ('#' + esc(item.BookingId)) : '<span class="text-muted">—</span>'}
      </td>
      <td style="background:#fff; vertical-align:middle; padding:16px;">
        <span class="badge rounded-pill text-bg-light border">${esc(item.Status || '')}</span>
      </td>
      <td style="background:#fff; vertical-align:middle; padding:16px;">
        <span class="badge rounded-pill ${badgeClassSent(item.IsSent)}">${Number(item.IsSent) === 1 ? 'YES' : 'NO'}</span>
      </td>
      <td style="background:#fff; vertical-align:middle; padding:16px; border-top-right-radius:12px; border-bottom-right-radius:12px;">
        <div class="small">${esc(item.Message || '')}</div>
        ${item.ErrorMessage ? `<div class="text-danger small mt-1">Error: ${esc(item.ErrorMessage)}</div>` : ''}
      </td>
    `;
    tableBody.appendChild(tr);
  });

  renderPagination();
  pageInfo.innerText = `Showing page ${currentPage} of ${Math.ceil(filteredData.length / itemsPerPage)} (${filteredData.length} total SMS logs)`;
}

function renderPagination() {
  paginationControls.innerHTML = '';
  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  if (totalPages <= 1) return;

  const mk = (label, page, disabled=false, active=false) => {
    const li = document.createElement('li');
    li.className = `page-item ${disabled ? 'disabled' : ''}`;

    const a = document.createElement('a');
    a.className = 'page-link border-0';
    a.href = '#';

    if (active) {
      a.style.cssText = 'background-color:#f8e14b; color:black; font-weight:bold; border-color:#f8e14b;';
    } else {
      a.classList.add('text-dark');
    }

    a.textContent = label;

    if (!disabled) {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        changePage(page);
      });
    }

    li.appendChild(a);
    return li;
  };

  paginationControls.appendChild(mk('Previous', currentPage - 1, currentPage === 1));

  for (let i = 1; i <= totalPages; i++) {
    paginationControls.appendChild(mk(String(i), i, false, currentPage === i));
  }

  paginationControls.appendChild(mk('Next', currentPage + 1, currentPage === totalPages));
}

function changePage(page) {
  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  renderTable();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

if (searchInput) {
  searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase().trim();

    filteredData = rawData.filter(item => {
      const booking = (item.BookingId ?? '').toString();
      const status = (item.Status ?? '').toString().toLowerCase();
      const msg = (item.Message ?? '').toString().toLowerCase();
      const id = (item.Id ?? '').toString();
      return (
        booking.includes(term) ||
        status.includes(term) ||
        msg.includes(term) ||
        id.includes(term)
      );
    });

    currentPage = 1;
    renderTable();
  });
}

renderTable();
</script>
</body>
</html>