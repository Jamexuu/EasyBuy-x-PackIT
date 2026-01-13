<?php 
require_once '../api/classes/Auth.php';

Auth::requireAuth();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($_ENV['PAYPAL_CLIENT_ID']); ?>&currency=USD"></script>
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container-fluid mb-5">
        <div class="row mb-3 mb-md-0">
            <div class="col p-3 px-md-5 py-md-4 bg-secondary-subtle border-2"
                style="border-bottom-style: dashed; border-color: #6EC064;">
                <div v class="d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined">
                        distance
                    </span>
                    <div class="h6 fw-bold mb-0">Denmar Redondo</div>
                    <div class="h6 mb-0">+639098765432</div>
                </div>
                <p class="fw-normal mt-3 p-0">
                    1234 Sample St., Barangay Example, City Test, Country Demo
                </p>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row mb-3 mb-md-0">
                <div class="col-12 p-md-5 p-0">
                    <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                        <table class="table mb-0">
                            <thead>
                                <tr class="table-secondary text-center">
                                    <th scope="col" class="w-50">Item</th>
                                    <th scope="col" class="w-auto">Quantity</th>
                                    <th scope="col" class="w-auto">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="checkoutItems">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row p-md-5">
                <div class="col-12 col-md-6 p-0 pe-md-2 mb-3 mb-md-0">
                    <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                        <div class="card-header bg-secondary-subtle">
                            <div class="h6 m-0 p-2">Order Summary</div>
                        </div>
                        <div class="card-body p-5" id="orderSummary">
                            
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 p-0 ps-md-2 mb-5 mb-md-0">
                    <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                        <div class="card-header bg-secondary-subtle">
                            <div class="h6 m-0 p-2">Payment Method</div>
                        </div>
                        <div class="card-body px-5 py-4">
                            <div id="payment-options">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="radioDefault" id="cashOnDelivery" checked>
                                    <label class="form-check-label" for="cashOnDelivery">
                                        Cash on Delivery
                                    </label>
                                    <p class="fw-normal">Your order will be delivered to your default address</p>
                                </div>
                                <hr>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="radioDefault" id="paypal">
                                    <label class="form-check-label" for="paypal">
                                        PayPal
                                    </label>
                                    <p class="fw-normal">Pay online using your PayPal e-wallet and receive your order at
                                        your default address</p>
                                </div>
                            </div>
                            <div id="paypal-button-container" style="display: none;"></div>
                            <button id="back-to-payment" class="btn btn-outline-secondary mt-3" style="display: none;">← Back to Payment Options</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row p-md-5">
                <div class="col p-0">
                    <p class="fw-normal">By placing an order, you agree to the <strong>EasyBuy Online Grocery Store Terms of Use and Sale</strong> and
                        acknowledge that you have read the <strong>Privacy Policy</strong>.</p>
                </div>
            </div>
            <div class="row p-md-5">
                <div class="col p-0 d-flex justify-content-end align-items-center gap-4" id="placeOrderSection">
                    
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>

        var checkoutItems = document.getElementById('checkoutItems');
        var placeOrderButton = document.getElementById('placeOrder');
        var orderSummary = document.getElementById('orderSummary');
        var placeOrderSection = document.getElementById('placeOrderSection');
        var orderData = null;
        var paypalButtonsRendered = false;

        async function getUserCartItems(){

            try{    

                const response = await fetch('../api/getCheckoutItems.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                if (!data || data.length === 0) {
                    checkoutItems.innerHTML = '<tr><td colspan="3" class="text-center py-4">No items to checkout</td></tr>';
                    return;
                }
                
                var totalAmount = 0;
                var shippingFee = 50;
                var totalWeight = 0;
                var subTotal = 0;

                data.forEach(item => {
                    subTotal += item.final_price * item.quantity;
                    totalWeight += item.weight_grams * item.quantity;
                    checkoutItems.innerHTML += `
                        <tr>
                            <td>
                                <div class="row align-items-center text-start">
                                    <div class="col-auto px-5 py-2">
                                        <img src="`+ item.image +`" alt=""
                                            class="img-fluid border border-1 rounded border-dark"
                                            style="max-height: 100px;">
                                    </div>
                                    <div class="col">
                                        <p class="mb-0">`+ item.product_name +`</p>
                                        ${ item.is_sale == 1 ? `<span class="badge" style="background-color:#28a745;">`+ item.sale_percentage + `% Off</span>` : '' }
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                `+ item.quantity + `
                            </td>
                            <td class="align-middle text-center">
                                ${ item.is_sale == 1 ? `
                                    <div><small class="text-decoration-line-through text-muted">₱`+ (item.price * item.quantity).toFixed(2) + `</small></div>
                                    <div class="fw-bold text-success">₱`+ (item.final_price * item.quantity).toFixed(2) + `</div>
                                ` : `₱`+ (item.final_price * item.quantity).toFixed(2) }
                            </td>
                        </tr>
                    `;
                });

                totalAmount = subTotal + shippingFee;

                orderSummary.innerHTML =`
                    <div class="d-flex justify-content-between mb-2">
                        <div>Subtotal</div>
                        <div>₱`+ subTotal.toFixed(2) + `</div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <div>Shipping</div>
                        <div>₱`+ shippingFee.toFixed(2) + `</div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <div>Total</div>
                        <div>₱`+ totalAmount.toFixed(2) + `</div>
                    </div>
                `;

                placeOrderSection.innerHTML = `
                    <p class="m-0">Total (`+ data.length + ` item${data.length !== 1 ? 's' : ''}) <strong>₱`+ totalAmount.toFixed(2) + `</strong></p>
                    <input type="button" id="placeOrder" class="btn text-white" style="background-color: #6EC064;" value="Place Order">
                `;

                orderData = {
                    items: data,
                    subtotal: subTotal,
                    shippingFee: shippingFee,
                    totalWeight: totalWeight,
                    totalAmount: totalAmount
                };

                submitOrder(data, subTotal, shippingFee, totalWeight, totalAmount);
                
                if (typeof paypal !== 'undefined' && paypal.Buttons) {
                    initPaypalButtons(totalAmount);
                } else {
                    var checkPaypal = setInterval(function() {
                        if (typeof paypal !== 'undefined' && paypal.Buttons) {
                            clearInterval(checkPaypal);
                            initPaypalButtons(totalAmount);
                        }
                    }, 100);
                    
                    setTimeout(function() {
                        clearInterval(checkPaypal);
                    }, 10000);
                }

            }catch(error){
                console.error('Error fetching cart items:', error);
                checkoutItems.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error loading checkout items</td></tr>';
            }

        }

        getUserCartItems();

        function submitOrder(checkoutItems, subtotal, totalShipping, totalWeight, total){
            const placeOrderBtn = document.getElementById('placeOrder');
            if (!placeOrderBtn) return;
            
            placeOrderBtn.addEventListener('click', async function(e){
                e.preventDefault();
                
                placeOrderBtn.disabled = true;
                placeOrderBtn.value = 'Processing...';
                
                const paymentMethod = getPaymentMethod();
                
                const payload = {
                    checkout_items: checkoutItems,
                    payment_method: paymentMethod,
                    subtotal: subtotal,
                    shipping_fee: totalShipping,
                    total_weight: totalWeight,
                    total_amount: total,
                    payment_status: paymentMethod === 'COD' ? 'pending' : 'pending',
                    transaction_id: null
                };
                
                try {
                    const response = await fetch('../api/addOrder.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        window.location.href = 'orderConfirmation.php?order_id=' + result.order_id;
                    } else {
                        alert('Error: ' + (result.error || 'Failed to place order'));
                        placeOrderBtn.disabled = false;
                        placeOrderBtn.value = 'Place Order';
                    }
                } catch(error) {
                    console.error('Error placing order:', error);
                    alert('Failed to place order. Please try again.');
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.value = 'Place Order';
                }
            });
        }

        function getPaymentMethod(){
            const paymentMethod = document.getElementById('cashOnDelivery').checked ? 'cod' : 'paypal';
            return paymentMethod;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var codRadio = document.getElementById('cashOnDelivery');
            var paypalRadio = document.getElementById('paypal');
            var backButton = document.getElementById('back-to-payment');
            
            if (codRadio && paypalRadio) {
                codRadio.addEventListener('change', togglePaymentUI);
                paypalRadio.addEventListener('change', togglePaymentUI);
            }
            
            if (backButton) {
                backButton.addEventListener('click', function() {
                    document.getElementById('cashOnDelivery').checked = true;
                    togglePaymentUI();
                });
            }
        });

        function togglePaymentUI() {
            var paymentMethod = getPaymentMethod();
            var placeOrderBtn = document.getElementById('placeOrder');
            var paypalContainer = document.getElementById('paypal-button-container');
            var paymentOptions = document.getElementById('payment-options');
            var backButton = document.getElementById('back-to-payment');
            
            if (paymentMethod === 'paypal') {
                if (placeOrderBtn) placeOrderBtn.style.display = 'none';
                paymentOptions.style.display = 'none';
                paypalContainer.style.display = 'block';
                backButton.style.display = 'block';
            } else {
                if (placeOrderBtn) placeOrderBtn.style.display = 'inline-block';
                paymentOptions.style.display = 'block';
                paypalContainer.style.display = 'none';
                backButton.style.display = 'none';
            }
        }

        function initPaypalButtons(amount) {
            if (paypalButtonsRendered) return;

            if (typeof paypal === 'undefined' || !paypal.Buttons) {
                console.error('PayPal SDK not loaded yet');
                return;
            }

            var amountUSD = (amount / 58).toFixed(2);

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return fetch('../api/createPaypalOrder.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            amount: amountUSD,
                            description: 'EasyBuy Grocery Order'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('PayPal Order Response:', data);
                        if (!data.success) {
                            console.error('Order creation failed:', data);
                            throw new Error(data.error || 'Failed to create order');
                        }
                        return data.orderId;
                    })
                    .catch(error => {
                        console.error('Create order error:', error);
                        throw error;
                    });
                },
                onApprove: function(data, actions) {
                    return fetch('../api/capturePaypalPayment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            paypalOrderId: data.orderID
                        })
                    })
                    .then(response => response.json())
                    .then(captureData => {
                        if (!captureData.success) throw new Error('Payment capture failed');
                        
                        return saveOrderWithPaypal(captureData.transactionId);
                    })
                    .then(result => {
                        if (result.success) {
                            window.location.href = 'orderConfirmation.php?orderId=' + result.order_id;
                        } else {
                            alert('Error saving order: ' + result.error);
                        }
                    })
                    .catch(error => {
                        console.error('PayPal error:', error);
                        alert('Payment processing failed. Please try again.');
                    });
                },
                onError: function(err) {
                    console.error('PayPal Buttons error:', err);
                    alert('Payment error occurred. Please try again.');
                }
            }).render('#paypal-button-container');

            paypalButtonsRendered = true;
        }

        async function saveOrderWithPaypal(transactionId) {
            var payload = {
                checkout_items: orderData.items,
                payment_method: 'paypal',
                subtotal: orderData.subtotal,
                shipping_fee: orderData.shippingFee,
                total_weight: orderData.totalWeight,
                total_amount: orderData.totalAmount,
                payment_status: 'completed',
                transaction_id: transactionId
            };

            var response = await fetch('../api/addOrder.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            return await response.json();
        }

    </script>
</body>

</html>