<?php
require '../api/classes/Auth.php';
Auth::requireAuth();

if (!isset($_GET['order_id'])) {
    header('Location: /EasyBuy-x-PackIT/EasyBuy/index.php');
    exit();
}

$orderId = $_GET['order_id'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container" style="min-height: calc(100vh - 200px); padding-top: 2rem; padding-bottom: 5rem;">
        <div class="row">
            <div class="col-12 text-center">
                <p
                    style="color: #6c757d; font-size: 0.9rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 2rem;">
                    Order #<span id="orderNumber"><?php echo htmlspecialchars($orderId); ?></span>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                <div class="text-center">
                    <div style="width: 200px; height: 200px; margin: 0 auto; position: relative;">
                        <div
                            style="width: 100%; height: 100%; background: radial-gradient(circle, rgba(107, 191, 89, 0.15) 0%, rgba(107, 191, 89, 0) 70%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <img src="../assets/order successfully.svg" alt="Order Successful" style="width: 250px; height: 250px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <p style="color: #6c757d; font-size: 0.9rem;">
                    Redirecting to home page in <span id="countdown">5</span> seconds...
                </p>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const orderId = <?php echo json_encode($orderId); ?>;
        let cancelModal;
        let currentOrderStatus = 'order placed';
        let countdown = 5;

        document.addEventListener('DOMContentLoaded', function () {
            loadOrderStatus();
            startCountdown();
        });

        function startCountdown() {
            const countdownElement = document.getElementById('countdown');
            const interval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = '/EasyBuy-x-PackIT/EasyBuy/index.php';
                }
            }, 1000);
        }

        async function loadOrderStatus() {
            try {
                const response = await fetch(`../api/getOrderDetails.php?order_id=${orderId}`);
                const data = await response.json();

                if (data.success && data.order) {
                    currentOrderStatus = data.order.status;
                    updateStatusDisplay(data.order.status);
                    
                    if (data.order.status !== 'order placed') {
                        const cancelBtn = document.getElementById('cancelOrderBtn');
                        if (cancelBtn) {
                            cancelBtn.style.display = 'none';
                        }
                    }
                } else {
                    console.error('Failed to load order details:', data.error);
                }
            } catch (error) {
                console.error('Error loading order status:', error);
            }
        }

        function cancelOrder() {
            if (currentOrderStatus !== 'order placed') {
                alert('This order cannot be cancelled as it has already been processed.');
                return;
            }
            cancelModal.show();
        }

        async function confirmCancel() {
            try {
                const response = await fetch('../api/cancelOrder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order_id: orderId })
                });

                const data = await response.json();

                if (data.success) {
                    cancelModal.hide();
                    alert('Order cancelled successfully');
                    window.location.href = '/EasyBuy-x-PackIT/EasyBuy/index.php';
                } else {
                    alert('Failed to cancel order: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('An error occurred while cancelling the order');
            }
        }
    </script>
</body>

</html>