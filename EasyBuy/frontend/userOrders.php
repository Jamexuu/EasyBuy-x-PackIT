<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders - EasyBuy</title>
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
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
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
                        <h1 class="mb-0" style="color: #28a745; font-weight: 600; font-size: 24px;">My Orders</h1>
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
                                To Ship
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="to-receive-tab" data-bs-toggle="tab"
                                data-bs-target="#to-receive" type="button" role="tab"
                                style="color: #6c757d; border: none; padding: 12px 24px; font-weight: 500; background: transparent;">
                                To Receive
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
                        <div id="toShipOrders">
                            <!-- Orders will be loaded here dynamically -->
                        </div>
                    </div>

                    <div class="tab-pane fade" id="to-receive" role="tabpanel">
                        <div id="toReceiveOrders">
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
        
        async function loadOrders() {
            try {
                const response = await fetch('../api/getUserOrder.php');
                const data = await response.json();

                if (data.success && data.orders) {
                    const toShipOrders = data.orders.filter(order => 
                        order.status === 'order placed' || order.status === 'waiting for courier'
                    );
                    const toReceiveOrders = data.orders.filter(order => 
                        order.status === 'picked up' || order.status === 'in transit'
                    );

                    renderOrders(toShipOrders, 'toShipOrders');
                    renderOrders(toReceiveOrders, 'toReceiveOrders');
                }
            } catch (error) {
                console.log('API not ready yet, showing example data');
                showExampleOrders();
            }
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
                order.items.forEach(item => {
                    const totalPrice = (item.product_price * item.quantity).toFixed(2);
                    const imageHtml = item.image_url 
                        ? `<img src="${item.image_url}" alt="${item.product_name}" 
                              style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; flex-shrink: 0;"
                              onerror="this.outerHTML='<div style=\\'width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #adb5bd;\\'>No Image</div>'">` 
                        : `<div style="width: 120px; height: 120px; background: #f8f9fa; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #adb5bd;">No Image</div>`;

                    cardsHtml += `
                        <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <div class="d-flex align-items-center gap-4">
                                ${imageHtml}
                                <div class="flex-grow-1">
                                    <div style="color: #495057; font-size: 18px; font-weight: 500;">${item.product_name}</div>
                                </div>
                                <div class="d-flex align-items-center" style="gap: 50px;">
                                    <div class="text-end">
                                        <div style="color: #6c757d; font-size: 14px; margin-bottom: 4px;">Quantity:</div>
                                        <div style="color: #212529; font-size: 16px; font-weight: 500;">x${item.quantity}</div>
                                    </div>
                                    <div class="text-end">
                                        <div style="color: #6c757d; font-size: 14px; margin-bottom: 4px;">Total:</div>
                                        <div style="color: #212529; font-size: 22px; font-weight: 600;">₱${totalPrice}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            container.innerHTML = cardsHtml;
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();

            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            
            if (tab === 'to-receive') {
                const toReceiveTab = document.getElementById('to-receive-tab');
                const toShipTab = document.getElementById('to-ship-tab');
                
                const bsTab = new bootstrap.Tab(toReceiveTab);
                bsTab.show();
                
                toReceiveTab.style.color = '#28a745';
                toReceiveTab.style.borderBottom = '3px solid #28a745';
                toShipTab.style.color = '#6c757d';
                toShipTab.style.borderBottom = 'none';
            }
        });
    </script>
</body>

</html>