<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product View</title>
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

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            background: white;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            max-height: 150px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .product-card .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-card .add-to-cart-icon:hover {
            background-color: #5da054;
        }
    </style>
</head>

<body>
    <?php include './components/navbar.php'; ?>

    <div class="container mt-5 p-4">
        <button class="back-btn mb-3" onclick="window.history.back()"
            style="background: none; border: none; color: #6EC064; font-size: 2rem; cursor: pointer;">
            <span class="material-symbols-rounded">arrow_back</span>
        </button>

        <div class="row bg-white rounded-3 p-4 mb-4" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); border: 1px solid #e8e8e8;">
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
                            onclick="addToCart(productId, getQuantity());"
                            style="background-color: #6EC064; color: white; border: none; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <span class="material-symbols-rounded">shopping_cart</span>
                        </button>
                    </form>
                    <form method="post" action="" class="flex-grow-1">
                        <button type="button" id="buyNowBtn" class="btn w-100" onclick="buyNow()"
                            style="background-color: #6EC064; color: white; border: none; height: 45px;">Buy Now</button>
                    </form>
                </div>
            </div>
        </div>

        <div style="margin-top: 50px;">
            <h3 style="font-size: 24px; font-weight: bold; color: #333; margin-bottom: 30px;">Similar Products</h3>
            <div class="row g-3" id="similarProductsContainer">
            </div>
        </div>
    </div>

    <script src="../assets/js/addToCart.js"></script>
    <script>
        let currentProduct = null;
        let allProducts = [];
        var urlParams = new URLSearchParams(window.location.search);
        var productId = urlParams.get('id');
        var qtyInput = document.getElementById('quantity');

        async function loadProduct() {


            if (!productId) {
                alert('No product selected!');
                window.history.back();
                return;
            }

            try {
                const response = await fetch('../assets/products.json');
                allProducts = await response.json();
                currentProduct = allProducts.find(p => p["Product ID"] == productId);

                if (!currentProduct) {
                    alert('Product not found!');
                    window.history.back();
                    return;
                }

                displayProduct();
                displaySimilarProducts();
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

        function buyNow() {
            const qty = document.getElementById('quantity').value;
            alert(`Buying ${qty} x ${currentProduct["Product Name"]}`);
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
                col.className = 'col-6 col-md-3';
                
                const badgeHtml = index === 0 ? '<span style="background-color: #6EC064; color: white; padding: 3px 8px; border-radius: 5px; font-size: 10px; position: absolute; top: 10px; right: 10px;">NEW</span>' : '';

                col.innerHTML = `
                    <div class="product-card position-relative" onclick="window.location.href='productView.php?id=${product["Product ID"]}'">
                        ${badgeHtml}
                        <img src="${product.image}" alt="${product["Product Name"]}" class="img-fluid">
                        <div class="product-name">${product["Product Name"]}</div>
                        <div class="product-rating mb-2">
                            <span style="color: #FFC107; font-size: 12px;">★★★★★</span>
                        </div>
                        <div style="color: #6EC064; font-weight: bold; font-size: 16px; margin-bottom: 10px;">₱${product.Price.toFixed(2)}</div>
                        <button class="add-to-cart-icon" onclick="event.stopPropagation(); addToCart(${product["Product ID"]}, 1);" style="background-color: #6EC064; color: white; border-radius: 5px; padding: 8px 12px; border: none; cursor: pointer;">
                            <span class="material-symbols-rounded" style="font-size: 18px; vertical-align: middle;">shopping_cart</span>
                        </button>
                    </div>
                `;

                container.appendChild(col);
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