<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Orders - EasyBuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <style>
        .accordion-button::after {
            display: none;
        }

        .status-dropdown {
            max-width: 180px;
            width: 100%;
            border: none;
            border-radius: 20px;
            padding: 8px 28px 8px 12px;
            font-weight: 500;
            font-size: 0.875rem;
            color: #333;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        }

        .status-dropdown:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .status-dropdown {
                font-size: 0.75rem;
                padding: 5px 24px 5px 10px;
                max-width: 140px;
            }
        }
    </style>
</head>

<body style="background-color: #f8f9fa;">
    <?php include '../frontend/components/adminNavBar.php'; ?>

    <div class="container py-5">
        <div class="row mb-1">
            <div class="col-12">
                <h2 style="color: #333; font-weight: 600;">Orders Management</h2>
            </div>
        </div>
    </div>

    <div class="container mb-4">
        <div class="row mb-3" style="background-color: #fff; padding: 10px 15px; border-radius: 8px;">
            <div class="col-2">
                <strong style="color: #666;">Order #</strong>
            </div>
            <div class="col-7">
                <strong style="color: #666;">Email</strong>
            </div>
            <div class="col-3" style="text-align: center;">
                <strong style="color: #666;">Status</strong>
            </div>
        </div>

        <div class="accordion" id="ordersAccordion">
            <div class="text-center py-5" id="loadingMessage">
                <p style="color: #666;">Loading orders...</p>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        fetch('../api/getAllOrders.php')
            .then(response => response.json())
            .then(orders => {
                const accordion = document.getElementById('ordersAccordion');
                const loadingMessage = document.getElementById('loadingMessage');

                if (loadingMessage) {
                    loadingMessage.remove();
                }

                if (!orders || orders.length === 0) {
                    accordion.innerHTML = `
                        <div class="text-center py-5">
                            <p style="color: #666;">No orders found.</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                orders.forEach(order => {
                    const statusColor = getStatusColor(order.status);
                    html += `
                        <div class="accordion-item mb-3" style="border: none; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <div class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#order${order.orderID}" aria-expanded="false" aria-controls="order${order.orderID}"
                                    style="background-color: #e8e8e8; box-shadow: none; padding: 15px 20px; display: flex; align-items: center; cursor: pointer;">
                                    <span style="color: #333; font-weight: 500; width: 16.666%; flex-shrink: 0;">${order.orderID}</span>
                                    <span style="color: #333; width: 58.333%; flex-shrink: 0;">${escapeHtml(order.userEmail)}</span>
                                    <div style="width: 25%; flex-shrink: 0; display: flex; justify-content: center;" onclick="event.stopPropagation();">
                                        <select class="status-dropdown" style="background-color: ${statusColor};" 
                                                onchange="updateOrderStatus(${order.orderID}, this.value)">
                                            <option value="Order Placed" ${order.status === 'Order Placed' ? 'selected' : ''}>Order Placed</option>
                                            <option value="Waiting for Courier" ${order.status === 'Waiting for Courier' ? 'selected' : ''}>Waiting for Courier</option>
                                            <option value="Picked up" ${order.status === 'Picked up' ? 'selected' : ''}>Picked up</option>
                                        </select>
                                    </div>
                                </div>
                            </h2>
                            <div id="order${order.orderID}" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                <div class="accordion-body" style="background-color: #e8e8e8; padding: 20px;">
                    `;

                    order.items.forEach((item, index) => {
                        const marginBottom = index < order.items.length - 1 ? 'margin-bottom: 8px;' : '';
                        html += `
                            <div style="color: #333; ${marginBottom} font-weight: 500;">
                                ${escapeHtml(item.product_name)} (${item.quantity})
                            </div>
                        `;
                    });

                    html += `
                                </div>
                            </div>
                        </div>
                    `;
                });

                accordion.innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching orders:', error);
                const accordion = document.getElementById('ordersAccordion');
                accordion.innerHTML = `
                    <div class="text-center py-5">
                        <p style="color: #dc3545;">Error loading orders. Please try again.</p>
                    </div>
                `;
            });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getStatusColor(status) {
            switch (status) {
                case 'Order Placed':
                    return '#AAAAAA';
                case 'Waiting for Courier':
                    return '#f4d03f';
                case 'Picked up':
                    return '#7dcea0';
                default:
                    return '#e8e8e8';
            }
        }

        function updateOrderStatus(orderId, newStatus) {
            // TODO: Send API request to update order status
            console.log(`Updating order ${orderId} to status: ${newStatus}`);

            // Update dropdown color
            const select = event.target;
            select.style.backgroundColor = getStatusColor(newStatus);
        }
    </script>
</body>

</html>