<?php
session_start();




// 1. INITIALIZE VARIABLES TO PREVENT ERRORS
// We create an empty array for orders so the page doesn't crash if there is no data.
$orders = [];




// Determine View: List or Detail?
$view = 'list';
$activeOrder = null;




// Check if a specific track_id is requested AND if it exists in our data
if (isset($_GET['track_id']) && isset($orders[$_GET['track_id']])) {
    $view = 'detail';
    $activeOrder = $orders[$_GET['track_id']];
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - PackIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">




    <style>
        :root {
            --gradient-color: linear-gradient(90deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --secondary-teal: #203a43;
            --card-border: #203a43;
        }




        body {
            background-color: #f4f6f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }




        .main-content {
            flex: 1;
            padding: 3rem 1rem;
        }




        .main-container {
            background: #ffffff;
            border-radius: 15px;
            border: 2px solid var(--card-border);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-height: 500px;
        }




        .order-card-item {
            background-color: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #e9ecef;
            transition: transform 0.2s;
        }
        .order-card-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
       
        .status-badge-green {
            background-color: #c9f29d;
            color: #2c5206;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }




        /* Empty State Styles */
        .empty-state-container {
            text-align: center;
            padding: 4rem 1rem;
            color: #6c757d;
        }
        .empty-state-img {
            width: 150px;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }




        /* Detail View Styles */
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
        .timeline-item { position: relative; margin-bottom: 2.5rem; }
        .timeline-dot {
            position: absolute; left: -2.05rem; top: 6px;
            width: 14px; height: 14px;
            border-radius: 50%; background-color: #e0e0e0; border: 2px solid #fff; z-index: 1;
        }
        .timeline-item.active .timeline-dot {
            background-color: var(--secondary-teal);
            transform: scale(1.3);
        }
        .timeline-date {
            position: absolute; left: -7rem; width: 4.5rem; text-align: right;
            font-size: 0.85rem; color: #6c757d; font-weight: 700; text-transform: uppercase; top: 4px;
        }
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




    <?php include '../frontend/components/navbar.php'; ?>




    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">




                    <div class="main-container p-4 p-md-5">




                        <?php if (empty($orders)): ?>
                           
                            <div class="empty-state-container">
                                <img src="../assets/box.png" alt="No Orders" class="empty-state-img">
                                <h4 class="fw-bold text-dark">No Active Orders</h4>
                                <p class="mb-4">It looks like you haven't booked any deliveries yet.</p>
                                <a href="../" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-uppercase">
                                    Book Now
                                </a>
                            </div>




                        <?php else: ?>




                            <?php if ($view == 'list'): ?>
                               
                                <h4 class="fw-bold mb-4" style="color: var(--secondary-teal);">
                                    <span class="material-symbols-outlined align-middle me-2">list_alt</span>
                                    My Orders
                                </h4>




                                <?php foreach($orders as $order): ?>
                                    <div class="order-card-item p-4 mb-4">
                                        <div class="row align-items-center">
                                           
                                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                                <div class="bg-white rounded p-3 d-inline-block shadow-sm">
                                                    <img src="../assets/box.png" alt="Package" style="width: 40px; height: 40px;">
                                                </div>
                                            </div>




                                            <div class="col-md-7 mb-3 mb-md-0">
                                                <div class="mb-2">
                                                    <span class="status-badge-green">
                                                        EXPECTED DELIVERY <?php echo $order['est_delivery']; ?>
                                                    </span>
                                                </div>
                                                <h6 class="fw-bold mb-0 text-dark"><?php echo $order['parcel_name']; ?></h6>
                                                <small class="text-muted">ORDER ID: <?php echo $order['id']; ?></small>
                                            </div>




                                            <div class="col-md-3 text-md-end text-start">
                                                <span class="fw-bold text-warning small text-uppercase" style="letter-spacing: 1px;">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </div>
                                        </div>




                                        <hr class="my-3 text-muted opacity-25">




                                        <div class="row align-items-center">
                                            <div class="col-md-6 text-muted small">
                                                TOTAL ITEM: <?php echo $order['items_count']; ?>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <span class="me-3 small fw-bold">TOTAL PAYMENT: P <?php echo $order['price']; ?></span>
                                                <a href="tracking.php?track_id=<?php echo $order['id']; ?>" class="btn btn-warning fw-bold px-4 shadow-sm">
                                                    TRACK
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>




                            <?php else: ?>




                                <div class="mb-4">
                                    <a href="tracking.php" class="text-decoration-none text-muted small fw-bold">
                                        <i class="bi bi-arrow-left me-1"></i> BACK TO ORDERS
                                    </a>
                                </div>




                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-2">
                                    <span class="status-badge-green fs-6 px-4 py-2">
                                        EXPECTED DELIVERY: <?php echo $activeOrder['est_delivery']; ?>
                                    </span>
                                    <span class="fw-bold text-warning text-uppercase">
                                        TO RECEIVE
                                    </span>
                                </div>




                                <div class="mb-5 border-bottom pb-4">
                                    <h2 class="fw-bold mb-1" style="color: var(--secondary-teal);">P <?php echo $activeOrder['price']; ?></h2>
                                    <p class="mb-0 fw-bold text-secondary fs-5"><?php echo $activeOrder['parcel_name']; ?></p>
                                    <small class="text-muted">ORDER ID: <?php echo $activeOrder['id']; ?></small>
                                </div>




                                <div class="timeline">
                                    <?php foreach($activeOrder['timeline'] as $log): ?>
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




                            <?php endif; ?>
                       
                        <?php endif; ?>




                    </div>




                </div>
            </div>
        </div>
    </div>




    <?php include '../frontend/components/footer.php'; ?>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





