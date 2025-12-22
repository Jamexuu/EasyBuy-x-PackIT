<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">
    <style>
        .main-img-container {
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .star-rating {
            color: #FFC107;
        }

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
        }

        #buyNowBtn {
            background-color: #6EC064;
            color: white;
            border: none;
        }

        #buyNowBtn:hover {
            background-color: #5da054;
        }

        #addToCartBtn {
            background-color: #6EC064;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 8px;
        }

        #addToCartBtn:hover {
            background-color: #5da054;
        }

        .back-btn {
            background: none;
            border: none;
            color: #6EC064;
            font-size: 2rem;
            cursor: pointer;
        }

        .back-btn:hover {
            color: #5da054;
        }
    </style>
</head>

<body style="background-color: #f5e6e6;">
    <?php include './components/navbar.php'; ?>

    <div class="container mt-5 pt-3">
        <button class="back-btn mb-3" onclick="window.history.back()">
            <span class="material-symbols-rounded">arrow_back</span>
        </button>

        <div class="row bg-white rounded-3 shadow-sm p-4">
            <div class="col-12 col-md-6 mb-3">
                <div class="main-img-container text-center">
                    <img id="mainImage" src="" class="img-fluid" alt="" style="max-height: 400px;">
                </div>
            </div>

            <div class="col-12 col-md-6">
                <h2 class="fw-bold mb-3" id="productName" style="color: #4a4a4a;">Loading...</h2>
                
                <div class="d-flex align-items-center mb-3">
                    <h3 class="text-dark fw-bold mb-0 me-3" id="productPrice">₱0.00</h3>
                    <div class="star-rating">
                        <span>★★★★★</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <button class="qty-btn" style="background-color: #e8e8e8; color: #666;" onclick="decreaseQty()">-</button>
                    <input type="number" id="quantity" class="qty-input" value="1" min="1" readonly>
                    <button class="qty-btn" style="background-color: #6EC064; color: white;" onclick="increaseQty()">+</button>
                </div>

                <div class="d-flex gap-2">
                    <button id="addToCartBtn" class="btn" onclick="addToCart()">
                        <span class="material-symbols-rounded">shopping_cart</span>
                    </button>
                    <button id="buyNowBtn" class="btn flex-grow-1" onclick="buyNow()">Buy Now</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProduct = null;

        async function loadProduct() {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                alert('No product selected!');
                window.history.back();
                return;
            }

            try {
                const response = await fetch('../assets/products.json');
                const products = await response.json();
                currentProduct = products.find(p => p["Product ID"] == productId);

                if (!currentProduct) {
                    alert('Product not found!');
                    window.history.back();
                    return;
                }

                displayProduct();
            } catch (error) {
                console.error('Error loading product:', error);
                alert('Error loading product data!');
            }
        }

        function displayProduct() {
            document.getElementById('productName').textContent = currentProduct["Product Name"];
            document.getElementById('productPrice').textContent = '₱' + currentProduct.Price.toFixed(2);
            document.getElementById('mainImage').src = currentProduct.image;
            document.getElementById('mainImage').alt = currentProduct["Product Name"];
        }

        function increaseQty() {
            let qtyInput = document.getElementById('quantity');
            qtyInput.value = parseInt(qtyInput.value) + 1;
        }

        function decreaseQty() {
            let qtyInput = document.getElementById('quantity');
            if (parseInt(qtyInput.value) > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
            }
        }

        function addToCart() {
            const qty = document.getElementById('quantity').value;
            alert(`Added ${qty} x ${currentProduct["Product Name"]} to cart!`);
        }

        function buyNow() {
            const qty = document.getElementById('quantity').value;
            alert(`Buying ${qty} x ${currentProduct["Product Name"]}`);
        }

        window.onload = loadProduct;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>