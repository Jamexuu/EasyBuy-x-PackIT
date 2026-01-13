<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . "/../api/classes/Database.php";

// 1. Authentication Check
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

// --- Helpers ---
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function formatMoney($n): string {
    return number_format((float)$n, 2);
}

// 2. Fetch ALL Transactions (No LIMIT/OFFSET here)
// We fetch everything so JavaScript can handle pagination.
$transactions = [];
$stmt = $db->executeQuery(
    "SELECT b.id, b.created_at, b.updated_at, b.vehicle_type, b.total_amount, b.tracking_status,
            p.amount AS paid_amount, p.currency, p.status AS payment_status
        FROM bookings b
        LEFT JOIN payments p ON p.booking_id = b.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC",
    [(string)$userId]
);
$rows = $db->fetch($stmt);

foreach ($rows as $r) {
    $transactions[] = [
        'id' => (int)$r['id'],
        'date' => date('M d, Y', strtotime($r['created_at'])),
        'vehicle' => ucfirst($r['vehicle_type'] ?? ''),
        'amount' => '₱ ' . number_format((float)($r['total_amount'] ?? 0), 2),
        'status' => ucwords(str_replace('_', ' ', $r['tracking_status'] ?? 'pending')),
        'raw_status' => strtolower($r['tracking_status'] ?? 'pending')
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction History</title>
    
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
                    <h3 class="fw-bold m-0">TRANSACTION HISTORY</h3>
                    
                    <div class="w-100 w-md-auto" style="max-width: 300px;">
                        <input id="searchInput" type="text" 
                               class="form-control border-0" 
                               placeholder="🔍 Search orders..."
                               style="background-color: #f1f3f5; border-radius: 20px; padding: 10px 20px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless table-hover" style="border-collapse: separate; border-spacing: 0 10px;">
                        <thead>
                            <tr>
                                <th style="background-color: #f8e14b; padding: 14px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">Date</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Order ID</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Service</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Amount</th>
                                <th style="background-color: #f8e14b; padding: 14px;">Vehicle</th>
                                <th style="background-color: #f8e14b; padding: 14px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tx-body">
                            </tbody>
                    </table>
                </div>

                <div id="pageInfo" class="text-center text-muted small mt-3"></div>

                <nav aria-label="Transaction pages" class="mt-3">
                  <ul class="pagination justify-content-center" id="paginationControls">
                    </ul>
                </nav>

            </div>
        </div>
    </main>

    <?php include __DIR__ . "/../frontend/components/chat.php"; ?>
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // 1. Pass PHP data to JavaScript
    const rawData = <?= json_encode($transactions) ?>;
    
    // Configuration
    const itemsPerPage = 5; // Change this number to show more/less items per page
    let currentPage = 1;
    let filteredData = [...rawData]; // Copy of data for searching

    // Elements
    const tableBody = document.getElementById('tx-body');
    const paginationControls = document.getElementById('paginationControls');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    // Helper: Get Status Color Class
    function getStatusClass(status) {
        const s = status.toLowerCase();
        if (['completed', 'delivered'].includes(s)) return 'text-success';
        if (['pending'].includes(s)) return 'text-warning';
        if (['accepted', 'in_transit', 'picked_up'].includes(s)) return 'text-primary';
        if (['cancelled', 'failed'].includes(s)) return 'text-danger';
        return 'text-muted';
    }

    // 2. Render Table Function
    function renderTable() {
        tableBody.innerHTML = '';

        if (filteredData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-5 rounded-3 bg-white">No orders found.</td></tr>`;
            pageInfo.innerText = '';
            paginationControls.innerHTML = '';
            return;
        }

        // Calculate slice indices
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filteredData.slice(start, end);

        // Generate Rows
        pageItems.forEach(item => {
            const tr = document.createElement('tr');
            tr.style.boxShadow = "0 3px 8px rgba(0,0,0,0.05)";
            
            tr.innerHTML = `
                <td class="text-secondary" style="background-color: #fff; vertical-align: middle; padding: 16px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">${item.date}</td>
                <td class="fw-bold" style="background-color: #fff; vertical-align: middle; padding: 16px;">#${item.id}</td>
                <td style="background-color: #fff; vertical-align: middle; padding: 16px;">Logistics</td>
                <td class="fw-bold" style="background-color: #fff; vertical-align: middle; padding: 16px;">${item.amount}</td>
                <td style="background-color: #fff; vertical-align: middle; padding: 16px;">${item.vehicle}</td>
                <td class="fw-bold ${getStatusClass(item.raw_status)}" style="background-color: #fff; vertical-align: middle; padding: 16px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">${item.status}</td>
            `;
            tableBody.appendChild(tr);
        });

        renderPagination();
    }

    // 3. Render Pagination Controls
    // 3. Render Pagination Controls
    function renderPagination() {
        paginationControls.innerHTML = '';
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);

        // Update Info Text
        pageInfo.innerText = `Showing page ${currentPage} of ${totalPages} (${filteredData.length} total orders)`;

        if (totalPages <= 1) return; // Hide controls if only 1 page

        // Previous Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link border-0 text-dark" href="#" onclick="changePage(${currentPage - 1})">Previous</a>`;
        paginationControls.appendChild(prevLi);

        // Numbered Buttons
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item`;
            
            // Apply yellow background if active
            const activeStyle = (currentPage === i) 
                ? 'background-color: #f8e14b; color: black; font-weight: bold; border-color: #f8e14b;' 
                : 'color: black;';
            
            li.innerHTML = `<a class="page-link border-0" style="${activeStyle}" href="#" onclick="changePage(${i})">${i}</a>`;
            paginationControls.appendChild(li);
        }

        // Next Button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link border-0 text-dark" href="#" onclick="changePage(${currentPage + 1})">Next</a>`;
        paginationControls.appendChild(nextLi);
    }

    // 4. Change Page Event
    window.changePage = function(page) {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    // 5. Search Functionality
    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase().trim();
        
        filteredData = rawData.filter(item => {
            return (
                item.id.toString().includes(term) ||
                item.date.toLowerCase().includes(term) ||
                item.vehicle.toLowerCase().includes(term) ||
                item.status.toLowerCase().includes(term) ||
                item.amount.toLowerCase().includes(term)
            );
        });

        currentPage = 1; // Reset to first page on search
        renderTable();
    });

    // Initial Render
    renderTable();
    </script>
</body>
</html>