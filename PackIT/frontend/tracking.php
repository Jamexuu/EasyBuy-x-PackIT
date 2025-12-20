<?php
session_start();

// --- 1. SIMULATE DATABASE FETCHING ---
// In your real app, you would query your database here.
// Example: $order = $conn->query("SELECT * FROM orders WHERE user_id = ...");

$orderData = null; // Default to empty

// FOR TESTING PURPOSES ONLY:
// Check if URL has ?status=success (Simulating a finished transaction)
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $orderData = [
        'order_id' => '92472891',
        'item_name' => 'PARCEL #1',
        'price' => '1,234.00',
        'est_delivery' => '20 DEC - 26 DEC',
        'current_status' => 'To Receive',
        'timeline' => [
            [
                'date' => 'Dec 4',
                'title' => 'PARCEL HAS BEEN DELIVERED',
                'desc' => 'Package received by customer (Signed by: Juan Cruz)',
                'active' => true 
            ],
            [
                'date' => 'Dec 3',
                'title' => 'ORDER HAS DEPARTED HUB',
                'desc' => 'Manila Distribution Center',
                'active' => false 
            ],
            [
                'date' => 'Dec 2',
                'title' => 'ORDER IS SHIPPED',
                'desc' => '',
                'active' => false 
            ],
            [
                'date' => 'Dec 1',
                'title' => 'ORDER PLACED',
                'desc' => '',
                'active' => false 
            ]
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Package - PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* --- GLOBAL & VARS --- */
        :root {
            --gradient-color: linear-gradient(90deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --secondary-teal: #203a43;
        }

        body {
            background-color: #f4f6f8; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* --- MAIN CONTENT & CARD STYLES --- */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }

        .tracking-card {
            background: #ffffff;
            border-radius: 15px;
            border-top: 5px solid #203a43; /* Matches Navbar Theme */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
            min-height: 350px;
        }

        /* Badge Style */
        .bg-gradient-custom {
            background: var(--gradient-color);
            color: white;
            font-weight: 600;
        }

        /* --- TIMELINE STYLES --- */
        .timeline {
            position: relative;
            padding-left: 3.5rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 24px; top: 8px; bottom: 0; width: 2px;
            background-color: #e0e0e0;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }
        .timeline-dot {
            position: absolute;
            left: -2.05rem; top: 6px;
            width: 14px; height: 14px;
            border-radius: 50%;
            background-color: #e0e0e0;
            border: 2px solid #fff;
            z-index: 1;
        }
        .timeline-item.active .timeline-dot {
            background-color: #2c5364;
            transform: scale(1.3);
            box-shadow: 0 0 0 4px rgba(44, 83, 100, 0.2);
        }
        .timeline-date {
            position: absolute;
            left: -7rem; width: 4.5rem;
            text-align: right;
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 700;
            text-transform: uppercase;
            top: 4px;
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .timeline { padding-left: 1.5rem; border-left: 2px solid #e0e0e0; }
            .timeline::before { display: none; }
            .timeline-item { margin-bottom: 2rem; padding-left: 1rem; }
            .timeline-dot { left: -1.55rem; top: 5px; }
            .timeline-date { position: relative; left: 0; text-align: left; display: block; width: 100%; margin-bottom: 5px; }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>


    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    <div class="card tracking-card p-4 p-md-5 mx-auto <?php echo (!$orderData) ? 'd-flex justify-content-center align-items-center' : ''; ?>">

                        <?php if (!$orderData): ?>
                            
                            <div class="text-center text-muted">
                                <span class="material-symbols-outlined fs-1" style="font-size: 4rem !important; color: #e0e0e0;">
                                    inventory_2
                                </span>
                                <h5 class="mt-3 fw-bold" style="color: #203a43;">No Active Tracking</h5>
                                <p class="small">Once you complete a transaction, your tracking details will appear here.</p>
                                
                                <a href="tracking.php?status=success" class="btn btn-sm btn-outline-secondary mt-3">
                                    Simulate Finished Transaction
                                </a>
                            </div>

                        <?php else: ?>

                            <div class="w-100 text-start">
                                
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
                                    <span class="badge bg-gradient-custom rounded-pill px-4 py-2 shadow-sm">
                                        ESTIMATED DELIVERY: <?php echo $orderData['est_delivery']; ?>
                                    </span>
                                    <span class="fw-bold small text-uppercase" style="color: #2c5364;">
                                        <i class="bi bi-hourglass-split me-1"></i> <?php echo $orderData['current_status']; ?>
                                    </span>
                                </div>

                                <div class="mb-5 border-bottom pb-4">
                                    <h2 class="fw-bold mb-1" style="color: #0f2027;">₱ <?php echo $orderData['price']; ?></h2>
                                    <p class="mb-0 fw-bold text-secondary fs-5"><?php echo $orderData['item_name']; ?></p>
                                    <small class="text-muted">ORDER ID: <?php echo $orderData['order_id']; ?></small>
                                </div>

                                <div class="timeline">
                                    <?php foreach($orderData['timeline'] as $log): ?>
                                        <div class="timeline-item <?php echo ($log['active']) ? 'active' : ''; ?>">
                                            <span class="timeline-date"><?php echo $log['date']; ?></span>
                                            <div class="timeline-dot"></div>
                                            <h6 class="fw-bold mb-1" style="color: #0f2027;"><?php echo $log['title']; ?></h6>
                                            <?php if(!empty($log['desc'])): ?>
                                                <small class="text-muted"><?php echo $log['desc']; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                            <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>