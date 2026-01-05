<?php
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-yellow: #f8e14b;
            --bg-light:  #f8f9fa;
            --text-muted: #6c757d;
        }

        /* --- UPDATED BODY CSS --- */
        body {
            background-color: var(--bg-light);
            padding-top: 30px;    /* Changed from padding: 30px 0 to handle footer overlap better */
            padding-bottom: 0;    
            
            /* These lines force the footer to the bottom */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- NEW MAIN CSS --- */
        main {
            flex: 1; /* This makes the content grow to push footer down */
            width: 100%; /* Ensures Bootstrap container centers correctly */
            margin-bottom: 30px; /* specific spacing before footer */
        }

        .custom-header {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .custom-header img {
            height:  40px;
        }

        .transaction-container {
            background-color: #ffffff;
            border:  3px solid var(--brand-yellow);
            border-radius: 28px;
            padding: 40px;
            box-shadow:  0 8px 20px rgba(0,0,0,0.06);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .search-bar {
            background-color: #f1f3f5;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            width: 240px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table thead th {
            background-color:  var(--brand-yellow);
            border: none;
            padding: 14px;
            font-weight: 600;
        }

        .table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
            border-radius: 12px;
        }

        .table tbody td {
            padding: 14px;
            border: none;
        }

        .table tbody tr:hover {
            background-color: #fffdf0;
        }

        .status-completed {
            color: #198754;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php include("components/navbar.php"); ?>

    <main class="container transaction-container">
        <div class="page-header">
            <h3>TRANSACTION HISTORY</h3>
            <input type="text" class="search-bar form-control" placeholder="🔍 Search">
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>12/20/2025</td>
                        <td>#12345</td>
                        <td>Courier Service</td>
                        <td>$50.00</td>
                        <td>Motorcycle</td>
                        <td class="status-completed">Completed</td>
                    </tr>

                    <tr>
                        <td>12/18/2025</td>
                        <td>#12312</td>
                        <td>Package Delivery</td>
                        <td>$30.00</td>
                        <td>Van</td>
                        <td class="status-completed">Completed</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>      
        
 <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>