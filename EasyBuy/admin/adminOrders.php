<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Orders - EasyBuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
</head>

<body style="background-color: #f8f9fa;">
    <?php include '../frontend/components/adminNavBar.php'; ?>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="color: #333; font-weight: 600;">Orders Management</h2>
            </div>
        </div>

        <!-- Orders Header -->
        <div class="row mb-3" style="background-color: #fff; padding: 15px 20px; border-radius: 8px;">
            <div class="col-2">
                <strong style="color: #666;">Order #</strong>
            </div>
            <div class="col-10">
                <strong style="color: #666;">Email</strong>
            </div>
        </div>

        <!-- Orders Container -->
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
        // Fetch orders from API
        fetch('../api/getAllOrders.php')
            .then(response => response.json())
            .then(orders => {
                const accordion = document.getElementById('ordersAccordion');
                const loadingMessage = document.getElementById('loadingMessage');
                
                // Remove loading message
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
                
                // Build orders HTML
                let html = '';
                orders.forEach(order => {
                    // All orders as accordion with email in header
                    html += `
                        <div class="accordion-item mb-3" style="border: none; border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#order${order.orderID}" aria-expanded="false" aria-controls="order${order.orderID}"
                                    style="background-color: #e8e8e8; box-shadow: none; padding: 15px 20px;">
                                    <span style="color: #333; font-weight: 500; margin-right: 20px;">${order.orderID}</span>
                                    <span style="color: #333;">${escapeHtml(order.userEmail)}</span>
                                </button>
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
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>