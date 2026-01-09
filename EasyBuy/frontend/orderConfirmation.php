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
    <title>Order Confirmation</title>
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
                    <h3 class="mt-3" style="color: #6BBF59; font-weight: 700;">Order Successful!</h3>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-12 col-md-10 col-lg-8">
                <div
                    style="background: linear-gradient(135deg, #6BBF59 0%, #8BC97E 100%); border-radius: 1rem; padding: 2rem 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                    <div class="d-flex justify-content-between align-items-start position-relative">
                        <div style="position: relative; flex: 1; text-align: center;">
                            <div
                                style="position: absolute; top: 30px; left: 50%; right: -50%; height: 3px; background: rgba(255, 255, 255, 0.25); z-index: 1;">
                            </div>
                            <div style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; position: relative; z-index: 2; transition: all 0.3s ease; background: white; color: #6BBF59; box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);"
                                id="status-placed">
                                <span class="material-symbols-rounded" style="font-size: 32px;">
                                    shopping_cart
                                </span>
                            </div>
                            <div style="color: white; font-size: 0.85rem; font-weight: 600; line-height: 1.3;">
                                Order<br>Placed
                            </div>
                        </div>

                        <div style="position: relative; flex: 1; text-align: center;">
                            <div
                                style="position: absolute; top: 30px; left: 50%; right: -50%; height: 3px; background: rgba(255, 255, 255, 0.25); z-index: 1;">
                            </div>
                            <div style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; position: relative; z-index: 2; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.25); color: white;"
                                id="status-waiting">
                                <span class="material-symbols-rounded" style="font-size: 32px;">
                                    inventory_2
                                </span>
                            </div>
                            <div style="color: white; font-size: 0.85rem; font-weight: 600; line-height: 1.3;">
                                Waiting<br>for courier
                            </div>
                        </div>

                        <div style="position: relative; flex: 1; text-align: center;">
                            <div
                                style="position: absolute; top: 30px; left: 50%; right: -50%; height: 3px; background: rgba(255, 255, 255, 0.25); z-index: 1;">
                            </div>
                            <div style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; position: relative; z-index: 2; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.25); color: white;"
                                id="status-transit">
                                <span class="material-symbols-rounded" style="font-size: 32px;">
                                    local_shipping
                                </span>
                            </div>
                            <div style="color: white; font-size: 0.85rem; font-weight: 600; line-height: 1.3;">
                                In<br>Transit
                            </div>
                        </div>

                        <div style="position: relative; flex: 1; text-align: center;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; position: relative; z-index: 2; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.25); color: white;"
                                id="status-delivered">
                                <span class="material-symbols-rounded" style="font-size: 32px;">
                                    home
                                </span>
                            </div>
                            <div style="color: white; font-size: 0.85rem; font-weight: 600; line-height: 1.3;">
                                Order<br>Delivered
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4 mb-5">
            <div class="col-auto">
                <button type="button"
                    style="background: #6c757d; border: none; color: white; padding: 0.75rem 2.5rem; border-radius: 0.5rem; font-weight: 600; text-transform: uppercase; transition: all 0.3s ease;"
                    onmouseover="this.style.background='#5a6268'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(108, 117, 125, 0.3)'"
                    onmouseout="this.style.background='#6c757d'; this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                    onclick="cancelOrder()">
                    Cancel Order
                </button>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this order? This action cannot be undone.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancel()">Yes, Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        const orderId = <?php echo json_encode($orderId); ?>;
        let cancelModal;

        document.addEventListener('DOMContentLoaded', function () {
            cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            loadOrderStatus();
        });

        async function loadOrderStatus() {
            try {
                const response = await fetch(`../api/getOrderDetails.php?order_id=${orderId}`);
                const data = await response.json();

                if (data.success) {
                    updateStatusDisplay(data.order.status);
                }
            } catch (error) {
                console.error('Error loading order status:', error);
            }
        }

        function updateStatusDisplay(status) {
            const statusMap = {
                'order placed': ['status-placed'],
                'waiting for courier': ['status-placed', 'status-waiting'],
                'in transit': ['status-placed', 'status-waiting', 'status-transit'],
                'order arrived': ['status-placed', 'status-waiting', 'status-transit', 'status-delivered']
            };

            const activeStatuses = statusMap[status] || ['status-placed'];

            document.querySelectorAll('[id^="status-"]').forEach(icon => {
                icon.style.background = 'rgba(255, 255, 255, 0.25)';
                icon.style.color = 'white';
                icon.style.boxShadow = 'none';
            });

            activeStatuses.forEach(statusId => {
                const element = document.getElementById(statusId);
                if (element) {
                    element.style.background = 'white';
                    element.style.color = '#6BBF59';
                    element.style.boxShadow = '0 4px 12px rgba(255, 255, 255, 0.3)';
                }
            });
        }

        function cancelOrder() {
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