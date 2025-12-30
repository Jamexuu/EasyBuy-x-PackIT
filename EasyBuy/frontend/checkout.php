<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container-fluid mb-5">
        <div class="row">
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
            <div class="row">
                <div class="col-12 p-md-5">
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
                <div class="col-12 col-md-6 p-0 pe-2">
                    <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                        <div class="card-header bg-secondary-subtle">
                            <div class="h6 m-0 p-2">Order Summary</div>
                        </div>
                        <div class="card-body p-5">
                            <div class="d-flex justify-content-between mb-2">
                                <div>Subtotal</div>
                                <div>₱140.00</div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <div>Shipping</div>
                                <div>₱20.00</div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <div>Total</div>
                                <div>₱160.00</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 p-0 ps-2">
                    <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                        <div class="card-header bg-secondary-subtle">
                            <div class="h6 m-0 p-2">Order Summary</div>
                        </div>
                        <div class="card-body px-5 py-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioDefault" id="cashOnDelivery">
                                <label class="form-check-label" for="cashOnDelivery">
                                    Cash on Delivery
                                </label>
                                <p class="fw-normal">Your order will be delivered to your default address</p>
                            </div>
                            <hr>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radioDefault" id="paypal" checked>
                                <label class="form-check-label" for="paypal">
                                    PayPal
                                </label>
                                <p class="fw-normal">Pay online using your PayPal e-wallet and receive your order at
                                    your default address</p>
                            </div>
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
                <div class="col p-0 d-flex justify-content-end align-items-center gap-4">
                    <p class="m-0">Total (2 items) <strong>₱403</strong></p>
                    <div class="btn text-white" style="background-color: #6EC064;">Place Order</div>
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

                data.forEach(item => {
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
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                `+ item.quantity + `
                            </td>
                            <td class="align-middle text-center">
                                ₱`+ (item.price * item.quantity).toFixed(2) + `
                            </td>
                        </tr>
                    `;
                });

            }catch(error){
                console.error('Error fetching cart items:', error);
            }

        }

        getUserCartItems();

    </script>
</body>

</html>