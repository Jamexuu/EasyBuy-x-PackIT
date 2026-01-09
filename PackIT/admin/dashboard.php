<?php
require_once '../api/classes/Auth.php';
Auth::requireAdmin();
$user = Auth::getUser();

$activePage = 'dashboard';
$basePath = '../';

// Define all dashboard cards here for easy management
$dashboardCards = [
    [
        'title' => 'Users',
        'link'  => 'dbTables.php?view=users',
        'icon'  => 'bi-people',
        'desc'  => 'View registered users'
    ],
    [
        'title' => 'Addresses',
        'link'  => 'dbTables.php?view=addresses',
        'icon'  => 'bi-geo-alt',
        'desc'  => 'View saved addresses'
    ],
    [
        'title' => 'Drivers',
        'link'  => 'dbTables.php?view=drivers',
        'icon'  => 'bi-person-badge',
        'desc'  => 'View driver profiles'
    ],
    [
        'title' => 'Vehicles',
        'link'  => 'vehicles.php', // Matches your navbar link
        'icon'  => 'bi-truck',
        'desc'  => 'Manage vehicle types'
    ],
    [
        'title' => 'Driver Vehicles',
        'link'  => 'dbTables.php?view=driver_vehicles',
        'icon'  => 'bi-keys',
        'desc'  => 'View driver assignments'
    ],
    [
        'title' => 'Payments',
        'link'  => 'dbTables.php?view=payments',
        'icon'  => 'bi-credit-card',
        'desc'  => 'View payment history'
    ],
    [
        'title' => 'Bookings',
        'link'  => 'dbTables.php?view=bookings',
        'icon'  => 'bi-journal-text',
        'desc'  => 'View all bookings'
    ],
    [
        'title' => 'SMS Logs',
        'link'  => 'dbTables.php?view=smslogs',
        'icon'  => 'bi-chat-left-text',
        'desc'  => 'View SMS system logs'
    ],
    [
        'title' => 'Password Resets',
        'link'  => 'dbTables.php?view=password_resets',
        'icon'  => 'bi-shield-lock',
        'desc'  => 'View reset tokens'
    ],
    [
        'title' => 'Chat History',
        'link'  => 'dbTables.php?view=chat_history',
        'icon'  => 'bi-chat-dots',
        'desc'  => 'View support chats'
    ]
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - PackIT</title>
    
    <style>
        .hover-outline {
            transition: all 0.3s ease; /* Smooth animation */
        }
        
        .hover-outline:hover {
            /* This creates a 2px yellow outline that follows the rounded corners */
            box-shadow: 0 0 0 2px #ffc107 !important; 
            
            /* Optional: If you want the border itself to change color instead, use this: */
            /* border-color: #ffc107 !important; */
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

        <div class="col-lg-9 col-md-8">
            <div class="content-area shadow-sm p-5">
                <h4 class="fw-bold mb-2">Welcome, <?= htmlspecialchars($user['name'] ?? 'Admin') ?></h4>
                <p class="text-muted mb-4">Select a section below to manage data.</p>

                <div class="row g-3">
                    <?php foreach ($dashboardCards as $card): ?>
                    <div class="col-xl-4 col-md-6">
                        <a class="text-decoration-none" href="<?= $card['link'] ?>">
                            <div class="p-4 border rounded-4 h-100 hover-shadow transition-all hover-outline" style="background-color: #fff;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><?= $card['title'] ?></h6>
                                    <span class="text-secondary fs-4"><i class="bi <?= $card['icon'] ?>"></i></span>
                                </div>
                                <div class="text-muted small"><?= $card['desc'] ?></div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>