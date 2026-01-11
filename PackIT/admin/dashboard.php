<?php
require_once '../api/classes/Auth.php';
Auth::requireAdmin();
$user = Auth::getUser();

require_once '../api/classes/Database.php';

$activePage = 'dashboard';
$basePath = '../';

$db = new Database();
$pdo = $db->pdo();

$feedbackStats = ['total' => 0, 'unread_for_user' => 0, 'open' => 0];

if ($pdo instanceof PDO) {
    try {
        $feedbackStats['total'] = (int)$pdo->query("SELECT COUNT(*) FROM user_feedback")->fetchColumn();
        $feedbackStats['unread_for_user'] = (int)$pdo->query("SELECT COUNT(*) FROM user_feedback WHERE user_unread = 1")->fetchColumn();
        $feedbackStats['open'] = (int)$pdo->query("SELECT COUNT(*) FROM user_feedback WHERE status = 'open'")->fetchColumn();
    } catch (Throwable $e) {
        error_log("Dashboard error: " . $e->getMessage());
    }
}

$dashboardCards = [
    ['title' => 'Users', 'link' => 'dbTables.php?view=users', 'icon' => 'bi-people', 'desc' => 'View registered users'],
    ['title' => 'Addresses', 'link' => 'dbTables.php?view=addresses', 'icon' => 'bi-geo-alt', 'desc' => 'View saved addresses'],
    ['title' => 'Drivers', 'link' => 'dbTables.php?view=drivers', 'icon' => 'bi-person-badge', 'desc' => 'View driver profiles'],
    ['title' => 'Vehicles', 'link' => 'vehicles.php', 'icon' => 'bi-truck', 'desc' => 'Manage vehicle types'],
    ['title' => 'Driver Vehicles', 'link' => 'dbTables.php?view=driver_vehicles', 'icon' => 'bi-keys', 'desc' => 'View driver assignments'],
    ['title' => 'Payments', 'link' => 'dbTables.php?view=payments', 'icon' => 'bi-credit-card', 'desc' => 'View payment history'],
    ['title' => 'Bookings', 'link' => 'dbTables.php?view=bookings', 'icon' => 'bi-journal-text', 'desc' => 'View all bookings'],
    ['title' => 'SMS Logs', 'link' => 'dbTables.php?view=smslogs', 'icon' => 'bi-chat-left-text', 'desc' => 'View SMS system logs'],
    ['title' => 'Password Resets', 'link' => 'dbTables.php?view=password_resets', 'icon' => 'bi-shield-lock', 'desc' => 'View reset tokens'],
    ['title' => 'Chat History', 'link' => 'dbTables.php?view=chat_history', 'icon' => 'bi-chat-dots', 'desc' => 'View support chats'],
    ['title' => 'User Feedback', 'link' => 'dbTables.php?view=userFeedback', 'icon' => 'bi-inbox', 'desc' => 'Read and respond to feedback'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - PackIT</title>
</head>
<body class="bg-light">

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

    <div class="col-lg-10 col-md-9">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-lg-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">Welcome, <?= htmlspecialchars($user['name'] ?? 'Admin') ?></h4>
                        <p class="text-muted mb-0">Select a section below to manage data.</p>
                    </div>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Go Back
                    </button>
                </div>

                <div class="card border-0 bg-light mb-4 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <div class="fw-bold fs-5">User Feedback Summary</div>
                                <div class="text-muted small">Quick overview</div>
                            </div>
                            <a href="dbTables.php?view=userFeedback" class="btn btn-dark btn-sm rounded-pill px-4">
                                Manage Feedback
                            </a>
                        </div>
                        <hr class="text-secondary opacity-25">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Total feedback</div>
                                        <div class="fs-4 fw-bold"><?= (int)$feedbackStats['total'] ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Open (status = 'open')</div>
                                        <div class="fs-4 fw-bold text-warning"><?= (int)$feedbackStats['open'] ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small">Unread for users</div>
                                        <div class="fs-4 fw-bold text-primary"><?= (int)$feedbackStats['unread_for_user'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <?php foreach ($dashboardCards as $card): ?>
                    <div class="col-xl-4 col-md-6">
                        <a class="text-decoration-none" href="<?= htmlspecialchars($card['link']) ?>">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($card['title']) ?></h6>
                                        <span class="text-secondary fs-4"><i class="bi <?= htmlspecialchars($card['icon']) ?>"></i></span>
                                    </div>
                                    <div class="text-muted small"><?= htmlspecialchars($card['desc']) ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>

</div> </div> <?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>