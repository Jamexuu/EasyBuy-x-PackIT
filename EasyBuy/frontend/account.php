<?php
    require '../api/classes/Auth.php';
    Auth::requireAuth();

    if (Auth::isAdmin()) {
        header("Location: ../admin/adminDashboard.php");
        exit();
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container">
        <div class="row m-3">
            <div class="col">
                <div class="h1 my-3">
                    Account
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center" id="accountContentField">
            
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
    <?php include 'components/chatbot.php'; ?>

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
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Order</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/chatbot.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    
    <script>
        var accountContentField = document.getElementById('accountContentField');
        let cancelModal;
        let orderToCancel = null;

        document.addEventListener('DOMContentLoaded', function () {
            cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            document.getElementById('confirmCancelBtn').addEventListener('click', confirmCancel);
        });

        function navigateToOrders(tab) {
            window.location.href = 'userOrders.php?tab=' + tab;
        }

        async function fetchUserData() {
            try {
                const response = await fetch('../api/getUserDetails.php');

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                console.log(data);

                accountContentField.innerHTML += `
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="card mb-5 rounded-4">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="h3 mb-3" style="color: #6EC064;">Profile</div>
                                </div>
                                <div class="card-text">
                                    <p>Last Name : `+ data.last_name +`</p>
                                    <p>First Name: `+ data.first_name +`</p>
                                    <p>Email: `+ data.email +`</p>
                                    <p>Contact Number: `+ data.contact_number +`</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5  rounded-4">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="h3 mb-3" style="color: #6EC064;">Address</div>
                                </div>
                                <div class="card-text">
                                    <p>
                                        `+ data.house_number +` `+ data.street +`, `+ data.barangay +`,
                                        `+ data.city +`, `+ data.province +` `+ data.postal_code +`
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5 rounded-4">
                            <div class="card-body">
                                <div class="card-title d-flex justify-content-between align-items-center">
                                    <div class="h3 mb-3" style="color: #6EC064;">Orders</div>
                                    <a href="orderHistory.php" class="text-decoration-none" style="color: #6EC064; font-size: 14px;">View order history ›</a>
                                </div>
                                <div class="card-text d-flex justify-content-center gap-5">
                                    <button onclick="navigateToOrders('placed-orders')" class="btn border-0 bg-transparent d-flex flex-column align-items-center text-decoration-none position-relative" style="cursor: pointer;">
                                        <span class="material-symbols-rounded" style="font-size: 40px; color: #333;">shopping_cart</span>
                                        <span id="placed-orders-badge" class="position-absolute badge rounded-pill bg-danger" style="display:none; top: 2px; right: 40px; font-size: 0.75rem; padding: 0.35em 0.5em;">0</span>
                                        <span style="font-size: 14px; color: #333;">Placed Orders</span>
                                    </button>
                                    <button onclick="navigateToOrders('to-receive')" class="btn border-0 bg-transparent d-flex flex-column align-items-center text-decoration-none position-relative" style="cursor: pointer;">
                                        <span class="material-symbols-rounded" style="font-size: 40px; color: #333;">local_shipping</span>
                                        <span id="to-receive-badge" class="position-absolute badge rounded-pill bg-danger" style="display:none; top: 2px; right: 30px; font-size: 0.75rem; padding: 0.35em 0.5em;">0</span>
                                        <span style="font-size: 14px; color: #333;">To Receive</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mb-3">
                            <form action="../api/logout.php" method="POST">
                                <button type="submit" class="btn btn-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                `;

            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        }

        async function cancelOrder(orderId) {
            orderToCancel = orderId;
            cancelModal.show();
        }

        async function confirmCancel() {
            if (!orderToCancel) return;

            try {
                const response = await fetch('../api/cancelOrder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ order_id: orderToCancel })
                });

                const data = await response.json();

                if (data.success) {
                    cancelModal.hide();
                    alert('Order cancelled successfully');
                    window.location.reload();
                } else {
                    alert('Failed to cancel order: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                alert('An error occurred while cancelling the order');
            }
        }

        fetchUserData();

        // Fetch order counts and update badges
        async function updateOrderBadges() {
            try {
                const response = await fetch('../api/getUserOrder.php');
                const data = await response.json();
                if (data.success && data.orders) {
                    const placedOrders = data.orders.filter(order => 
                        order.status === 'order placed' || order.status === 'waiting for courier'
                    );
                    const toReceiveOrders = data.orders.filter(order => 
                        order.status === 'picked up' || order.status === 'in transit'
                    );
                    const placedBadge = document.getElementById('placed-orders-badge');
                    const toReceiveBadge = document.getElementById('to-receive-badge');
                    placedBadge.textContent = placedOrders.length;
                    toReceiveBadge.textContent = toReceiveOrders.length;
                    placedBadge.style.display = placedOrders.length > 0 ? 'inline-block' : 'none';
                    toReceiveBadge.style.display = toReceiveOrders.length > 0 ? 'inline-block' : 'none';
                }
            } catch (error) {
                // Silent fail
            }
        }
        updateOrderBadges();
    </script>
</body>


    <!-- No extra style needed, using Bootstrap badge classes and inline style for position -->
</html>