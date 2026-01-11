<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order History - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <style>
        body {
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <?php
    include 'components/navbar.php';
    ?>

    <div class="bg-white">
        <div class="container-fluid">
            <div class="row justify-content-start">
                <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
                    <div class="d-flex align-items-center gap-3 py-3 px-3">
                        <span class="material-symbols-rounded" onclick="history.back()"
                            style="cursor: pointer; color: #28a745;">
                            arrow_back
                        </span>
                        <h1 class="mb-0" style="color: #28a745; font-weight: 600; font-size: 24px;">Order History</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white" style="border-bottom: 2px solid #e9ecef;">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8">
                    <ul class="nav nav-tabs px-3 justify-content-center" id="orderTabs" role="tablist"
                        style="border-bottom: none; gap: 40px;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="to-ship-tab" data-bs-toggle="tab"
                                data-bs-target="#to-ship" type="button" role="tab"
                                style="color: #28a745; border: none; padding: 12px 24px; font-weight: 500; background: transparent; border-bottom: 3px solid #28a745;">
                                All Orders
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-receive-tab" data-bs-toggle="tab"
                                data-bs-target="#to-receive" type="button" role="tab"
                                style="color: #6c757d; border: none; padding: 12px 24px; font-weight: 500; background: transparent;">
                                Completed
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled"
                                type="button" role="tab"
                                style="color: #6c757d; border: none; padding: 12px 24px; font-weight: 500; background: transparent;">
                                Cancelled
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-10 col-lg-9 col-xl-8 px-0">

                <div class="tab-content p-3" id="orderTabsContent">
                    <div class="tab-pane fade show active" id="to-ship" role="tabpanel">
                        <div id="allOrders">
                            <!-- Orders will be loaded here dynamically -->
                        </div>
                    </div>

                    <div class="tab-pane fade" id="to-receive" role="tabpanel">
                        <div id="completedOrders">
                            <!-- Orders will be loaded here dynamically -->
                        </div>
                    </div>

                    <div class="tab-pane fade" id="cancelled" role="tabpanel">
                        <div id="cancelledOrders">
                            <!-- Orders will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('#orderTabs button').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('#orderTabs button').forEach(btn => {
                    btn.style.color = '#6c757d';
                    btn.style.borderBottom = 'none';
                });
                this.style.color = '#28a745';
                this.style.borderBottom = '3px solid #28a745';
            });
        });

        var allOrders = [];
        var completedOrders = [];
        var cancelledOrders = [];

        async function fetchUserOrders() {
            try {
                const response = await fetch('../api/getUserOrder.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch orders');
                }

                const result = await response.json();
                
                if (result.success) {
                    allOrders = result.orders.map(order => transformOrder(order));
                    completedOrders = allOrders.filter(order => order.status === 'Delivered');
                    cancelledOrders = allOrders.filter(order => order.status === 'Cancelled');
                    
                    renderOrders(allOrders, 'allOrders');
                    renderOrders(completedOrders, 'completedOrders');
                    renderOrders(cancelledOrders, 'cancelledOrders');
                } else {
                    console.error('Failed to fetch orders:', result.error);
                    showEmptyState('allOrders');
                    showEmptyState('completedOrders');
                    showEmptyState('cancelledOrders');
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
                showEmptyState('allOrders');
                showEmptyState('completedOrders');
                showEmptyState('cancelledOrders');
            }
        }

        function transformOrder(order) {
            const statusColors = {
                'Order Placed': { color: '#ffc107', bg: '#fff3cd' },
                'In Transit': { color: '#17a2b8', bg: '#d1ecf1' },
                'Delivered': { color: '#28a745', bg: '#d4edda' },
                'Cancelled': { color: '#dc3545', bg: '#f8d7da' }
            };

            const statusInfo = statusColors[order.status] || { color: '#6c757d', bg: '#e9ecef' };
            const orderDate = new Date(order.order_date);
            const formattedDate = orderDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            return {
                id: order.id,
                date: formattedDate,
                status: order.status,
                statusColor: statusInfo.color,
                statusBg: statusInfo.bg,
                items: order.items,
                total: parseFloat(order.total_amount),
                showBuyAgain: order.status === 'Delivered'
            };
        }

        function showEmptyState(containerId) {
            const container = document.getElementById(containerId);
            container.innerHTML = `
                <div class="text-center py-5">
                    <p style="color: #6c757d;">No orders found</p>
                </div>
            `;
        }

        function renderOrders(orders, containerId) {
            const container = document.getElementById(containerId);

            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <p style="color: #6c757d;">No orders found</p>
                    </div>
                `;
                return;
            }

            let cardsHtml = '';
            orders.forEach(order => {
                cardsHtml += `
                    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom: 2px solid #e9ecef;">
                            <div>
                                <div class="text-muted small">Order #${order.id}</div>
                                <div class="text-muted small">${order.date}</div>
                            </div>
                            <span style="font-size: 14px; font-weight: 600; padding: 6px 12px; border-radius: 6px; color: ${order.statusColor}; background-color: ${order.statusBg};">${order.status}</span>
                        </div>
                `;

                order.items.forEach((item, index) => {
                    const marginClass = index < order.items.length - 1 ? 'mb-3' : '';
                    const itemPrice = parseFloat(item.product_price);
                    const imageHtml = item.image_url
                        ? `<img src="${item.image_url}" alt="${item.product_name}" 
                              style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; flex-shrink: 0;"
                              onerror="this.outerHTML='<div style=\\'width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #adb5bd;\\'>No Image</div>'">`
                        : `<div style="width: 80px; height: 80px; background: #f8f9fa; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #adb5bd;">No Image</div>`;

                    cardsHtml += `
                        <div class="d-flex align-items-center gap-4 ${marginClass}">
                            ${imageHtml}
                            <div class="flex-grow-1">
                                <div style="color: #495057; font-size: 16px; font-weight: 500;">${item.product_name}</div>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 50px;">
                                <div class="text-end">
                                    <div style="color: #6c757d; font-size: 14px; margin-bottom: 4px;">Quantity:</div>
                                    <div style="color: #212529; font-size: 16px; font-weight: 500;">x${item.quantity}</div>
                                </div>
                                <div class="text-end">
                                    <div style="color: #6c757d; font-size: 14px; margin-bottom: 4px;">Price:</div>
                                    <div style="color: #212529; font-size: 18px; font-weight: 600;">₱${itemPrice.toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                cardsHtml += `
                        <div class="d-flex justify-content-end align-items-center pt-3 mt-3" style="border-top: 2px solid #e9ecef;">
                            <div class="text-end ${order.showBuyAgain ? 'me-4' : ''}">
                                <div style="color: #6c757d; font-size: 14px; margin-bottom: 4px;">Order Total:</div>
                                <div style="color: #28a745; font-size: 24px; font-weight: 700;">₱${order.total.toFixed(2)}</div>
                            </div>
                            ${order.showBuyAgain ? '<button style="background-color: #28a745; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500;">Buy again</button>' : ''}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = cardsHtml;
        }

        document.addEventListener('DOMContentLoaded', function () {
            fetchUserOrders();
        });
    </script>
</body>

</html>