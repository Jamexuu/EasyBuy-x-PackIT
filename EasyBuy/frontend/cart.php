<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body {
            font-family: 'Poppins';
            background-color: #f8f9fa;
        }

        .card-custom {
            background-color: #f1f1f1;
            border-radius: 12px;
        }


        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background-color: #6EC064;
            color: #fff;
            font-weight: bold;
        }

        .btn-order {
            background-color: #6EC064;
        }

        .btn-secondary:hover {
            background-color: #349C55;
        }


        .qty-box {
            width: 30px;
            text-align: center;
            border: none;
            background: transparent;
        }

        .payment-box {
            background-color: #e5e5e5;
            border-radius: 12px;
            padding: 20px;
        }

        .img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?> 

    <div class="container mt-4 text-strong fw-bold">
        <h3>Your Shopping Cart</h3>
    </div>


    <div class="container mt-3">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card-custom p-4 shadow-sm">
                    <div class="row fw-bold mb-3 text-muted">
                        <div class="col-5 text-center">ITEM</div>
                        <div class="col-4 text-center">QUANTITY</div>
                        <div class="col-3 text-end">SUBTOTAL</div>
                    </div>

                    <div class="row align-items-center mb-4" id="cartItemContainer">
                       
                    </div>
                </div>
            </div>

            <div class="col-md-4" id="orderSummaryContainer">
                
            </div>

        </div>
    </div>


    <div class="container mt-5">
        <div class="payment-box shadow-sm">
            <div class="row text-center">
                <div class="col-md-6">
                    <input type="radio" name="payment">
                    <strong> CASH ON DELIVERY</strong>
                    <p class="small text-muted mb-0">
                        Your order will be delivered to your default address
                    </p>
                </div>

                <div class="col-md-6">
                    <input type="radio" name="payment">
                    <strong> E - WALLET</strong>
                    <p class="small text-muted mb-0">
                        Paypal, Maya, Gcash,
                    </p>
                </div>
            </div>
        </div>
    </div>

   <div class="container text-center my-5">
    <form method="post" action="">
        <button type="submit" class="btn btn-success text-white fw-bold py-2 px-4">
            PLACE ORDER
        </button>
    </form>
</div>


    </div>

    <?php include "components/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var cartItemContainer = document.getElementById('cartItemContainer');
        var orderSummaryContainer = document.getElementById('orderSummaryContainer');

        async function getCartItems() {
            try {
                const response = await fetch('../api/getCartItems.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                data.forEach(item => {
                    cartItemContainer.innerHTML += `
                         <div class="col-5 d-flex align-items-center">
                            <img src="`+ item.image +`" class="m-3 mx-4 img product-img" width="100" height="100" alt="Product Image">
                            <div>
                                <div class="mx-4">`+ item.product_name +`</div>
                                <small class="text-muted ms-4">`+ item.category +`</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <button class="qty-btn" onclick="decreaseQty()">-</button>
                            <input type="text" id="qty" class="qty-box" value="`+ item.quantity +`" readonly>
                            <button class="qty-btn" onclick="increaseQty()">+</button>
                        </div>

                        <div class="col-3 text-end">
                            <span id="item-price" data-price="150">`+ item.price * item.quantity +`</span>
                        </div>
                    `;
                });
            } catch (error) {
                console.error('Error fetching cart items:', error);
                return [];
            }
        }

        getCartItems();

        async function getOrderSummary(){
            try {
                const response = await fetch('../api/getCartSummary.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                orderSummaryContainer.innerHTML = `
                    <div class="card-custom p-4 shadow-sm text-center text-muted">
                        <h5 class="mb-3">Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span id="subtotal">₱`+ data.subtotal +`</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Shipping Fee</span>
                            <span id="shipping" data-shipping="50">₱`+ data.shipping +`</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fw-bold">
                            <span>Order Total</span>
                            <span id="order-total">₱`+ data.total +`</span>
                        </div>
                        <form method="post" action="">
                            <button type="submit" class="btn btn-secondary w-100">CHECKOUT</button>
                        </form>
                    </div>
                `;

                console.log(data);
            } catch (error) {
                console.error('Error fetching cart items:', error);
                return [];
            }
        }

        getOrderSummary();

        function formatPhp(n) {
            return '₱' + n.toFixed(2);
        }

        function increaseQty() {
            const qtyEl = document.getElementById('qty');
            if (!qtyEl) return;
            let current = parseInt(qtyEl.value) || 1;
            current += 1;
            qtyEl.value = current;
            updateTotals();
        }

        function decreaseQty() {
            const qtyEl = document.getElementById('qty');
            if (!qtyEl) return;
            let current = parseInt(qtyEl.value) || 1;
            if (current > 1) current -= 1;
            qtyEl.value = current;
            updateTotals();
        }

        document.addEventListener('DOMContentLoaded', updateTotals);
    </script>
</body>

</html>