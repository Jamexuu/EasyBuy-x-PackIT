<?php
require_once '../api/classes/Auth.php';
Auth::requireAdmin();
$user = Auth::getUser();

$activePage = 'dashboard';
$basePath = '../';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - PackIT</title>
</head>
<body>

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

        <div class="col-lg-9 col-md-8">
            <div class="content-area shadow-sm p-5">
                <h4 class="fw-bold mb-2">Welcome, <?= htmlspecialchars($user['name'] ?? 'Admin') ?></h4>
                <p class="text-muted mb-4">Select a section from the left menu.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a class="text-decoration-none" href="dbTables.php?view=users">
                            <div class="p-4 border rounded-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-1">Users</h6>
                                    <span class="text-muted"><i class="bi bi-people"></i></span>
                                </div>
                                <div class="text-muted">View users table</div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a class="text-decoration-none" href="dbTables.php?view=bookings">
                            <div class="p-4 border rounded-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-1">Bookings</h6>
                                    <span class="text-muted"><i class="bi bi-journal-text"></i></span>
                                </div>
                                <div class="text-muted">View bookings table</div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- row -->
</div><!-- container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>