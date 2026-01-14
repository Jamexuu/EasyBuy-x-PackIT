<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product View - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .qty-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            font-weight: bold;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            border: none;
            background: transparent;
            padding: 0;
        }

        .qty-input::-webkit-inner-spin-button,
        .qty-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .qty-input {
            -moz-appearance: textfield;
        }

        #buyNowBtn:hover {
            background-color: #5da054;
        }

        #addToCartBtn:hover {
            background-color: #5da054;
        }

        .back-btn:hover {
            color: #5da054;
        }

        /* Match listing add-to-cart button styling */
        .addToCartBtn,
        .dropdown-item:active,
        .dropdown-item:focus,
        #nextBtn,
        #prevBtn {
            background-color: #6EC064;
            color: #FFFFFF;
        }

        .addToCartBtn:hover,
        #nextBtn:hover,
        #prevBtn:hover {
            background-color: lightgray;
            color: dimgray;
        }
    </style>
</head>

<body>
    <?php include './components/navbar.php'; ?>
    <?php include './components/addToCart.php'; ?>

    <div class="container mt-5 p-4">
        <button class="back-btn mb-3" onclick="window.history.back()"
            style="background: none; border: none; color: #6EC064; font-size: 2rem; cursor: pointer;">
            <span class="material-symbols-rounded">arrow_back</span>
        </button>

        <div class="row bg-white rounded-3 p-4 mb-4"
            style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid #e8e8e8;">
            <div class="col-12 col-md-6 mb-3">
                <div class="text-center"
                    style="border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                    <img id="mainImage" src="" class="img-fluid" alt="" style="max-height: 400px;">
                </div>
            </div>

            <div class="col-12 col-md-6">
                <h2 class="fw-bold mb-3" id="productName" style="color: #4a4a4a;">Loading...</h2>

                <div class="d-flex align-items-center mb-3">
                    <h3 class="text-dark fw-bold mb-0 me-3" id="productPrice">₱0.00</h3>
                    <div style="color: #FFC107;">
                        <span>★★★★★</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <button class="qty-btn" style="background-color: #e8e8e8; color: #666;"
                        onclick="decreaseQty()">-</button>
                    <input type="number" id="quantity" class="qty-input" value="1" min="1" readonly>
                    <button class="qty-btn" style="background-color: #6EC064; color: white;"
                        onclick="increaseQty()">+</button>
                </div>

                <div class="d-flex gap-2">
                    <form method="post" action="" class="d-inline">
                        <button type="button" id="addToCartBtn" class="btn"
                            onclick="addToCartWithModal(productId, getQuantity());"
                            style="background-color: #6EC064; color: white; border: none; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span class="material-symbols-rounded">shopping_cart</span>
                        </button>
                    </form>
                    <form method="post" action="" class="flex-grow-1">
                        <button type="button" id="buyNowBtn" class="btn w-100" onclick="buyNow()"
                            style="background-color: #6EC064; color: white; border: none; height: 45px;">Buy
                            Now</button>
                    </form>
                </div>
            </div>
        </div>

        <div style="margin-top: 50px;">
            <h3 style="font-size: 24px; font-weight: bold; color: #333; margin-bottom: 30px;">Similar Products</h3>
            <div class="row g-1" id="similarProductsContainer">
            </div>
        </div>
    </div>

    <div id="noItem" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Error</div>
                <p class="mb-4">No product selected</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
                        data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="noProductData" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Error</div>
                <p class="mb-4">Error loading product data</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
                        data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="addToCartModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Success</div>
                <p class="mb-4" id="addToCartMessage"></p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
                        data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="addToCartErrorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Error</div>
                <p class="mb-4" id="addToCartErrorMessage">Failed to add item to cart</p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
                        data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="loginRequiredModal" class="modal fade" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Login Required</div>
                <p class="mb-4">Please log in to continue with your purchase</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn px-4" style="background-color: #e8e8e8; color: #666;"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
                        onclick="window.location.href='login.php'">
                        Login
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let currentProduct = null;
        let allProducts = [];
        var urlParams = new URLSearchParams(window.location.search);
        var productId = urlParams.get('id');
        var qtyInput = document.getElementById('quantity');

        async function loadProduct() {
            if (!productId) {
                const noItemModal = new bootstrap.Modal(document.getElementById("noItem"));
                noItemModal.show();
                setTimeout(() => window.history.back(), 2000);
                return;
            }

            try {
                const response = await fetch('../assets/products.json');
                allProducts = await response.json();
                currentProduct = allProducts.find(p => p["Product ID"] == productId);

                if (!currentProduct) {
                    const noItemModal = new bootstrap.Modal(document.getElementById("noItem"));
                    noItemModal.show();
                    setTimeout(() => window.history.back(), 2000);
                    return;
                }

                displayProduct();
                displaySimilarProducts();
            } catch (error) {
                console.error('Error loading product:', error);
                const noProductModal = new bootstrap.Modal(document.getElementById("noProductData"));
                noProductModal.show();
            }
        }

        function displayProduct() {
            document.getElementById('productName').textContent = currentProduct["Product Name"];
            document.getElementById('productPrice').textContent = '₱' + currentProduct.Price.toFixed(2);
            document.getElementById('mainImage').src = currentProduct.image;
            document.getElementById('mainImage').alt = currentProduct["Product Name"];
        }

        function increaseQty() {
            qtyInput.value = parseInt(qtyInput.value) + 1;
        }

        function decreaseQty() {
            if (parseInt(qtyInput.value) > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
            }
        }

        function getQuantity() {
            return parseInt(qtyInput.value);
        }

        async function buyNow() {
            const qty = getQuantity();
            const buyNowBtn = document.getElementById('buyNowBtn');

            buyNowBtn.disabled = true;
            buyNowBtn.textContent = 'Processing...';

            try {
                const response = await fetch('../api/createDirectCheckout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        productId: productId,
                        quantity: qty
                    })
                });

                if (response.status === 401) {
                    const loginModal = new bootstrap.Modal(document.getElementById("loginRequiredModal"));
                    loginModal.show();
                    buyNowBtn.disabled = false;
                    buyNowBtn.textContent = 'Buy Now';
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'checkout.php';
                } else {
                    document.getElementById("addToCartErrorMessage").textContent = result.error || 'Failed to process request';
                    const errorModal = new bootstrap.Modal(document.getElementById("addToCartErrorModal"));
                    errorModal.show();
                    buyNowBtn.disabled = false;
                    buyNowBtn.textContent = 'Buy Now';
                }

            } catch (error) {
                console.error('Error in buyNow:', error);
                document.getElementById("addToCartErrorMessage").textContent = "Failed to process your request. Please try again.";
                const errorModal = new bootstrap.Modal(document.getElementById("addToCartErrorModal"));
                errorModal.show();
                buyNowBtn.disabled = false;
                buyNowBtn.textContent = 'Buy Now';
            }
        }

        function displaySimilarProducts() {
            const similarProducts = allProducts.filter(p =>
                p.Category === currentProduct.Category &&
                p["Product ID"] !== currentProduct["Product ID"]
            );

            const container = document.getElementById('similarProductsContainer');
            container.innerHTML = '';

            similarProducts.forEach((product, index) => {
                const col = document.createElement('div');
                col.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex justify-content-center';

                const badgeHtml = '';

                col.innerHTML = `
                    <div class="card rounded-4 h-100" style="max-width: 280px; width: 100%; min-height: 340px;">
                        ${badgeHtml}
                        <div class="position-relative">
                            <img class="img-fluid object-fit-contain p-3 d-block mx-auto" style="height: 180px; width: 100%; cursor: pointer;" src="${product.image}" alt="${product["Product Name"]}" onclick="window.location.href='productView.php?id=${product["Product ID"]}'">
                        </div>
                        <div class="card-body mt-0 pt-0 d-block" style="cursor: pointer;" onclick="window.location.href='productView.php?id=${product["Product ID"]}'">
                            <h5 class="card-title d-none d-md-block text-center fw-bold">${product["Product Name"]}</h5>
                            <h3 class="card-title d-md-none text-center fw-bold">${product["Product Name"]}</h3>
                            <p class="card-text text-center text-secondary">${product.size || ''}</p>
                        </div>
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-column">
                                <span class="h6 d-none d-md-block fw-bold" style="color: #6EC064;">₱${product.Price.toFixed(2)}</span>
                                <span class="h5 d-md-none fw-bold" style="color: #6EC064;">₱${product.Price.toFixed(2)}</span>
                            </div>
                            <button type="button" class="btn rounded-3 addToCartBtn" data-product-id="${product["Product ID"]}" onclick="event.stopPropagation(); addToCartWithModal(${product["Product ID"]}, 1);" style="background-color: #6EC064; color: #FFFFFF; border: none; width: 45px; height: 45px; border-radius: 8px; display:flex; align-items:center; justify-content:center;">
                                <span class="material-symbols-rounded">shopping_cart</span>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(col);
            });
        }

        function addToCartWithModal(productId, quantity) {
            addToCart(productId, quantity).then(data => {
                const product = allProducts.find(p => p["Product ID"] == productId);
                const productName = product ? product["Product Name"] : "Product";

                let modalId;

                if (data.requiresLogin) {
                    modalId = "loginRequiredModal";
                } else if (data.message) {
                    modalId = "addToCartModal";
                    document.getElementById("addToCartMessage").textContent = `${productName} (Qty: ${quantity}) added to cart!`;
                } else if (data.error) {
                    modalId = "addToCartErrorModal";
                    document.getElementById("addToCartErrorMessage").textContent = data.error;
                }

                if (modalId) new bootstrap.Modal(document.getElementById(modalId)).show();
            }).catch(() => {
                document.getElementById("addToCartErrorMessage").textContent = "Failed to add product to cart. Please try again.";
                new bootstrap.Modal(document.getElementById("addToCartErrorModal")).show();
            });
        }

        window.onload = loadProduct;
    </script>

    <?php include './components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>