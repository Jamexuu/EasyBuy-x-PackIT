<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

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

                    <div class="row align-items-center mb-4">
                        <div class="col-5 d-flex align-items-center">
                            <img src="" class="m-3 mx-4 img product-img" width="100" height="100" alt="Product Image" onerror="this.src='https://via.placeholder.com/60'">
                            <div>
                                <div class="mx-4">ARLA</div>
                                <small class="text-muted ms-4">Milk</small>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <button class="qty-btn" onclick="decreaseQty()">-</button>
                            <input type="text" id="qty" class="qty-box" value="1" readonly>
                            <button class="qty-btn" onclick="increaseQty()">+</button>
                        </div>

                        <div class="col-3 text-end">
                            <span id="item-price" data-price="150">₱150.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 shadow-sm text-center text-muted">
                    <h5 class="mb-3">Order Summary</h5>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span id="subtotal">₱150.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping Fee</span>
                        <span id="shipping" data-shipping="50">₱50.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 fw-bold">
                        <span>Order Total</span>
                        <span id="order-total">₱200.00</span>
                    </div>
                    <button class="btn btn-secondary w-100">CHECKOUT</button>
                </div>
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
    <button class="btn btn-success text-white fw-bold py-2 px-4">
        PLACE ORDER
    </button>
</div>


    </div>

    <?php include "components/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function formatPhp(n) {
            return '₱' + n.toFixed(2);
        }

        function getItemPrice() {
            const el = document.getElementById('item-price');
            return el && el.dataset && el.dataset.price ? parseFloat(el.dataset.price) : 0;
        }

        function getShipping() {
            const el = document.getElementById('shipping');
            if (!el) return 0;
            const ds = el.dataset && el.dataset.shipping;
            if (ds) return parseFloat(ds);
            return parseFloat(el.textContent.replace(/[^0-9.]/g, '')) || 0;
        }

        function updateTotals() {
            const qtyEl = document.getElementById('qty');
            const qty = Math.max(1, parseInt(qtyEl.value) || 1);
            const price = getItemPrice();
            const subtotal = price * qty;
            const shipping = getShipping();
            const total = subtotal + shipping;

            const subtotalEl = document.getElementById('subtotal');
            const orderTotalEl = document.getElementById('order-total');
            if (subtotalEl) subtotalEl.textContent = formatPhp(subtotal);
            if (orderTotalEl) orderTotalEl.textContent = formatPhp(total);
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