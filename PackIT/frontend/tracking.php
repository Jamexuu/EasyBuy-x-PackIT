<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . "/../api/classes/Database.php";
include __DIR__ . '/components/autorefresh.php';

// Require login
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

$db = new Database();

// ----------------------------
// Helpers
// ----------------------------
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function formatMoney($n): string {
    return number_format((float)$n, 2);
}

/**
 * Map booking.tracking_status -> timeline items
 */
function buildTimeline(string $status, string $createdAt): array {
    $steps = [
        'pending'    => ['title' => 'Booking created',     'desc' => 'We received your booking request.'],
        'accepted'   => ['title' => 'Driver accepted',     'desc' => 'A driver has accepted your booking.'],
        'picked_up'  => ['title' => 'Package picked up',   'desc' => 'Your package has been picked up.'],
        'in_transit' => ['title' => 'In transit',          'desc' => 'Your package is on the way.'],
        'delivered'  => ['title' => 'Delivered',           'desc' => 'Your package has been delivered.'],
    ];

    if ($status === 'cancelled') {
        return [
            [
                'date' => date('M d', strtotime($createdAt)),
                'title' => 'Booking created',
                'desc' => 'We received your booking request.',
                'active' => false,
            ],
            [
                'date' => date('M d', strtotime($createdAt)),
                'title' => 'Cancelled',
                'desc' => 'This booking has been cancelled.',
                'active' => true,
            ],
        ];
    }

    $order = array_keys($steps);
    $currentIndex = array_search($status, $order, true);
    if ($currentIndex === false) $currentIndex = 0;

    $timeline = [];
    foreach ($order as $i => $key) {
        $timeline[] = [
            'date' => date('M d', strtotime($createdAt)),
            'title' => $steps[$key]['title'],
            'desc' => $steps[$key]['desc'],
            'active' => $i <= $currentIndex,
            'is_last' => $i === count($order) - 1
        ];
    }
    return $timeline;
}

// ----------------------------
// Fetch list of bookings
// ----------------------------
$stmt = $db->executeQuery(
    "SELECT id, user_id, driver_id,
            pickup_municipality, pickup_province,
            drop_municipality, drop_province,
            vehicle_type, total_amount, tracking_status, payment_status, created_at
     FROM bookings
     WHERE user_id = ? AND tracking_status != 'delivered'
     ORDER BY id DESC",
    [(string)$userId]
);
$rows = $db->fetch($stmt);

$orders = [];
foreach ($rows as $r) {
    $id = (int)($r['id'] ?? 0);
    if ($id <= 0) continue;

    $createdAt = (string)($r['created_at'] ?? date('Y-m-d H:i:s'));
    $status = (string)($r['tracking_status'] ?? 'pending');

    $orders[$id] = [
        'id' => $id,
        'parcel_name' => (string)($r['vehicle_type'] ?? 'Delivery'),
        'items_count' => 1,
        'price' => (float)($r['total_amount'] ?? 0),
        'formatted_price' => number_format((float)($r['total_amount'] ?? 0), 2),
        'status' => strtoupper(str_replace('_', ' ', $status)),
        'est_delivery' => date('M d', strtotime($createdAt . ' +1 day')),
        'pickup' => trim((string)($r['pickup_municipality'] ?? '') . ', ' . (string)($r['pickup_province'] ?? '')),
        'drop' => trim((string)($r['drop_municipality'] ?? '') . ', ' . (string)($r['drop_province'] ?? '')),
        'timeline' => buildTimeline($status, $createdAt),
        'created_at' => $createdAt,
        'tracking_status' => $status,
    ];
}

// ----------------------------
// View selection
// ----------------------------
$view = 'list';
$activeOrder = null;

$trackId = isset($_GET['track_id']) ? (int)$_GET['track_id'] : 0;
if ($trackId > 0) {
    if (isset($orders[$trackId])) {
        $view = 'detail';
        $activeOrder = $orders[$trackId];
    } else {
        header('Location: transaction.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #f4f6f8; font-family: 'Segoe UI', sans-serif;">

<?php include 'components/navbar.php'; ?>

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                
                <div class="bg-white p-4 p-md-5" 
                     style="border-radius: 15px; border: 2px solid #203a43; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); min-height: 500px;">

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5 text-secondary">
                            <img src="../assets/box.png" alt="No Orders" class="mb-4" style="width: 150px; opacity: 0.8;">
                            <h4 class="fw-bold text-dark">No Active Orders</h4>
                            <p class="mb-4">It looks like you haven't booked any active deliveries yet. Delivered orders move to your Transaction History.</p>
                            <a href="booking/package.php" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase">
                                Book Now
                            </a>
                        </div>

                    <?php else: ?>

                        <?php if ($view === 'list'): ?>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                                <h4 class="fw-bold m-0" style="color: #203a43;">
                                    <span class="material-symbols-outlined align-middle me-2">list_alt</span>
                                    My Orders
                                </h4>
                                
                                <div class="w-100 w-md-auto" style="max-width: 300px;">
                                    <input id="searchInput" type="text" 
                                           class="form-control border-0 bg-light" 
                                           placeholder="🔍 Search orders..."
                                           style="border-radius: 20px; padding: 10px 20px;">
                                </div>
                            </div>

                            <div id="ordersContainer">
                                </div>

                            <nav aria-label="Order pages" class="mt-4">
                                <div id="pageInfo" class="text-center text-muted small mb-2"></div>
                                <ul class="pagination justify-content-center" id="paginationControls">
                                    </ul>
                            </nav>

                        <?php else: ?>

                            <div class="mb-4">
                                <a href="tracking.php" class="text-decoration-none text-muted small fw-bold">
                                    <i class="bi bi-arrow-left me-1"></i> BACK TO ORDERS
                                </a>
                            </div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-2">
                                <span class="badge rounded-pill fs-6 px-4 py-2" 
                                      style="background-color: #c9f29d; color: #2c5206;">
                                    EXPECTED DELIVERY: <?= h((string)$activeOrder['est_delivery']) ?>
                                </span>
                                <span class="fw-bold text-warning text-uppercase">
                                    <?= h((string)$activeOrder['status']) ?>
                                </span>
                            </div>

                            <div class="mb-5 border-bottom pb-4">
                                <h2 class="fw-bold mb-1" style="color: #203a43;">
                                    ₱ <?= h(formatMoney($activeOrder['price'])) ?>
                                </h2>
                                <p class="mb-1 fw-bold text-secondary fs-5"><?= h((string)$activeOrder['parcel_name']) ?></p>
                                <small class="text-muted">BOOKING ID: <?= (int)$activeOrder['id'] ?></small>
                                <div class="small text-muted mt-2">
                                    <div><strong>Pickup:</strong> <?= h((string)$activeOrder['pickup']) ?></div>
                                    <div><strong>Drop-off:</strong> <?= h((string)$activeOrder['drop']) ?></div>
                                </div>
                            </div>

                            <div class="ps-md-4 ps-2">
                                <?php foreach ($activeOrder['timeline'] as $log): ?>
                                    <?php 
                                        $isActive = !empty($log['active']);
                                        $dotColor = $isActive ? '#203a43' : '#e0e0e0';
                                        $scale = $isActive ? 'transform: scale(1.3);' : '';
                                        $lineDisplay = isset($log['is_last']) && $log['is_last'] ? 'display:none;' : '';
                                    ?>
                                    <div class="d-flex gap-4 position-relative pb-4">
                                        <div class="text-end d-none d-md-block pt-1" style="width: 80px; min-width: 80px;">
                                            <span class="small fw-bold text-secondary text-uppercase"><?= h((string)$log['date']) ?></span>
                                        </div>
                                        <div class="d-flex flex-column align-items-center position-relative" style="width: 24px;">
                                            <div style="width: 14px; height: 14px; border-radius: 50%; background-color: <?= $dotColor ?>; border: 2px solid #fff; z-index: 2; flex-shrink: 0; margin-top: 6px; <?= $scale ?>"></div>
                                            <div style="width: 2px; background-color: #e0e0e0; flex-grow: 1; <?= $lineDisplay ?>"></div>
                                        </div>
                                        <div class="pb-2">
                                            <div class="d-block d-md-none mb-1">
                                                <span class="small fw-bold text-secondary text-uppercase"><?= h((string)$log['date']) ?></span>
                                            </div>
                                            <h6 class="fw-bold mb-1" style="color: #0f2027;"><?= h((string)$log['title']) ?></h6>
                                            <?php if (!empty($log['desc'])): ?>
                                                <small class="text-muted"><?= h((string)$log['desc']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
<?php include("../frontend/components/chat.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($view === 'list' && !empty($orders)): ?>
<script>
    // Data from PHP
    const rawData = <?= json_encode(array_values($orders)) ?>;
    
    // --- PERSISTENCE: Get State from URL ---
    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = parseInt(urlParams.get('page')) || 1;
    const initialSearch = urlParams.get('q') || '';

    // Configuration
    const itemsPerPage = 3; 
    let currentPage = initialPage;
    let filteredData = [...rawData];

    // Elements
    const container = document.getElementById('ordersContainer');
    const paginationControls = document.getElementById('paginationControls');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    // Initialize Search Field
    if (initialSearch) {
        searchInput.value = initialSearch;
        filterData(initialSearch);
    }

    function updateUrlState() {
        const url = new URL(window.location);
        url.searchParams.set('page', currentPage);
        if (searchInput.value.trim()) {
            url.searchParams.set('q', searchInput.value.trim());
        } else {
            url.searchParams.delete('q');
        }
        window.history.replaceState({}, '', url);
    }

    function renderList() {
        container.innerHTML = '';

        if (filteredData.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-search fs-1"></i>
                    <p class="mt-3">No orders found matching your search.</p>
                </div>`;
            pageInfo.innerText = '';
            paginationControls.innerHTML = '';
            return;
        }

        // Validate Page Number
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filteredData.slice(start, end);

        pageItems.forEach(item => {
            const card = document.createElement('div');
            card.className = "p-4 mb-4";
            card.style.backgroundColor = "#f8f9fa";
            card.style.borderRadius = "15px";
            card.style.border = "1px solid #e9ecef";
            card.style.transition = "transform 0.2s, box-shadow 0.2s";

            card.onmouseover = function() { 
                this.style.transform='translateY(-2px)'; 
                this.style.boxShadow='0 4px 15px rgba(0,0,0,0.05)';
            };
            card.onmouseout = function() { 
                this.style.transform='translateY(0)'; 
                this.style.boxShadow='none';
            };

            card.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-2 text-center mb-3 mb-md-0">
                        <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                            <img src="../assets/box.png" alt="Package" style="width: 40px; height: 40px;">
                        </div>
                    </div>
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="mb-2">
                            <span class="badge rounded-pill" style="background-color: #c9f29d; color: #2c5206; font-size: 0.8rem; padding: 6px 15px;">
                                EXPECTED DELIVERY ${item.est_delivery}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">${item.parcel_name}</h6>
                        <small class="text-muted">BOOKING ID: ${item.id}</small>
                        <div class="small text-muted mt-1">
                            ${item.pickup} → ${item.drop}
                        </div>
                    </div>
                    <div class="col-md-3 text-md-end text-start">
                        <span class="fw-bold text-warning small text-uppercase" style="letter-spacing: 1px;">
                            ${item.status}
                        </span>
                    </div>
                </div>
                <hr class="my-3 opacity-25">
                <div class="row align-items-center">
                    <div class="col-md-6 text-muted small">
                        TOTAL ITEM: ${item.items_count}
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="me-3 small fw-bold">
                            TOTAL PAYMENT: ₱ ${item.formatted_price}
                        </span>
                        <a href="tracking.php?track_id=${item.id}" class="btn btn-warning fw-bold px-4 shadow-sm">
                            TRACK
                        </a>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        renderPagination();
    }

    function renderPagination() {
        paginationControls.innerHTML = '';
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        pageInfo.innerText = `Showing page ${currentPage} of ${totalPages} (${filteredData.length} total orders)`;

        if (totalPages <= 1) return;

        // Previous
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link border-0 text-dark" href="#" onclick="changePage(${currentPage - 1})">Previous</a>`;
        paginationControls.appendChild(prevLi);

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item`;
            
            const activeStyle = (currentPage === i) 
                ? 'background-color: #f8e14b; color: black; font-weight: bold; border-color: #f8e14b;' 
                : 'color: black;';

            li.innerHTML = `<a class="page-link border-0" style="${activeStyle}" href="#" onclick="changePage(${i})">${i}</a>`;
            paginationControls.appendChild(li);
        }

        // Next
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link border-0 text-dark" href="#" onclick="changePage(${currentPage + 1})">Next</a>`;
        paginationControls.appendChild(nextLi);
    }

    window.changePage = function(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        
        currentPage = page;
        updateUrlState(); // Save state
        renderList();
        
        document.querySelector('main').scrollIntoView({ behavior: 'smooth' });
    }

    function filterData(term) {
        term = term.toLowerCase().trim();
        filteredData = rawData.filter(item => {
            return (
                item.id.toString().includes(term) ||
                item.parcel_name.toLowerCase().includes(term) ||
                item.status.toLowerCase().includes(term) ||
                item.pickup.toLowerCase().includes(term) ||
                item.drop.toLowerCase().includes(term)
            );
        });
    }

    // Search Handler
    searchInput.addEventListener('input', (e) => {
        filterData(e.target.value);
        currentPage = 1;
        updateUrlState(); // Save state
        renderList();
    });

    // Initial Load
    renderList();
</script>
<?php endif; ?>

</body>
</html>