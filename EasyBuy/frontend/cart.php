<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .card-custom {
            background-color: #f1f1f1;
            border-radius: 12px;
        }

        .btn-green {
            background-color: #6EC064;
            border: none;
        }

        .btn-green:hover {
            background-color: #349C55;
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

    <div class="container mt-4">
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
                            <img src="images/43.jpg" class="m-3 mx-4" width="60" alt="Product Image">
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
                            ₱150.00
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-custom p-4 shadow-sm text-center">
                    <h5 class="mb-3">Order Summary</h5>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>₱150.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Shipping Fee</span>
                        <span>₱50.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-4">
                        <span>Order Total</span>
                        <span>₱200.00</span>
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
        <button class="btn btn-green px-5 py-2 text-white fw-bold" onclick="">
            PLACE ORDER
        </button>
    </div>
    </div>

    <?php include "components/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>