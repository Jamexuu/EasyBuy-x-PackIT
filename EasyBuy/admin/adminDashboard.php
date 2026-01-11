<?php 
    require_once '../api/classes/Auth.php';
    Auth::requireAdmin();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - EasyBuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .dashboard-card {
            background-color: #d3d3d3;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            border: none;
            height: 100%;
        }

        .dashboard-number {
            font-size: 5rem;
            font-weight: bold;
            color: #4a4a4a;
            line-height: 1;
            margin: 1rem 0;
        }

        .dashboard-label {
            font-size: 0.9rem;
            color: #6a6a6a;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
    <?php include '../frontend/components/adminNavBar.php'; ?>      

    <div class="container-fluid px-5 py-4">
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="dashboard-card">
                    <div class="dashboard-label">All Products</div>
                    <div class="dashboard-number" id="allProducts">0</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="dashboard-card">
                    <div class="dashboard-label">Placed Orders</div>
                    <div class="dashboard-number" id="placedOrders">0</div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="dashboard-card">
                    <div class="dashboard-label">Picked up</div>
                    <div class="dashboard-number" id="pickedUp">0</div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="dashboard-card">
                    <div class="dashboard-label">Unread emails</div>
                    <div class="dashboard-number" id="unreadEmails">0</div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="dashboard-card">
                    <div class="dashboard-label">Unread messages</div>
                    <div class="dashboard-number" id="unreadMessages">0</div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        async function loadDashboardStats() {
            try {
                const response = await fetch('../api/getAdminDashboardStats.php');
                const data = await response.json();

                document.getElementById('allProducts').textContent = data.allProducts || 0;
                document.getElementById('placedOrders').textContent = data.placedOrderCount || 0;
                document.getElementById('pickedUp').textContent = data.pickedUpOrderCount || 0;
                document.getElementById('unreadEmails').textContent = data.unreadEmails || 0;
                document.getElementById('unreadMessages').textContent = data.unreadMessages || 0;
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadDashboardStats);
        
        // Refresh dashboard stats when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                loadDashboardStats();
            }
        });
        
        // Auto-refresh every 30 seconds
        setInterval(loadDashboardStats, 30000);
    </script>
</body>

</html>