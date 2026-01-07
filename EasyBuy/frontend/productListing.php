<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
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
            color: dimgray
        }

        .dropdown-item:active,
        .dropdown-item:focus {
            border-radius: 5px;
            padding: 5px;
        }

        .badge {
            background-color: #FFC107;
        }
    </style>
</head>

<body>
    <?php include './components/navbar.php'; ?>

    <div class="container-fluid mt-5 pt-5">
        <div class="row">
            <div class="col-12 col-md-3 col-lg-3">
                <div class="container-fluid p-3">
                    <div class="h5 text-start fw-bold">Filter</div>
                    <div class="mb-2">
                        <select class="form-select" id="priceOptions" aria-label="Filter by price">
                            <option value="" selected>Price</option>
                            <option value="below-100">Below 100</option>
                            <option value="100-200">100 - 200</option>
                            <option value="200-300">200 - 300</option>
                            <option value="300-400">300 - 400</option>
                            <option value="above-500">Above 500</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select class="form-select" id="categoryOptions" aria-label="Filter by category">
                            <option value="" selected>Category</option>
                            <option value="all">All Products</option>
                            <option value="produce">Produce</option>
                            <option value="meat">Meat and Seafood</option>
                            <option value="dairy">Dairy</option>
                            <option value="frozen">Frozen</option>
                            <option value="condiments">Condiments and Sauces</option>
                            <option value="snacks">Snacks</option>
                            <option value="beverages">Beverages</option>
                            <option value="personal">Health and Personal Care</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select class="form-select" id="salesOptions" aria-label="Filter by sales">
                            <option value="" selected>Sales</option>
                            <option value="all">All Sale items</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-9 col-lg-9">
                <div class="container p-3 justify-content-center">
                    <div class="row" id="productsArea"></div>
                    <div class="row" id="paginationArea">
                        <div class="col d-flex mb-5 justify-content-center">
                            <button class="btn me-2" id="prevBtn"
                                onclick="prevPage(); window.scrollTo(0, 0);">Prev</button>
                            <div class="align-self-center mx-2 rounded-3 px-2 py-1" style="color: #6EC064;"
                                id="pageText"></div>
                            <button class="btn ms-2" id="nextBtn"
                                onclick="nextPage(); window.scrollTo(0, 0);">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/addToCart.js"></script>
    <script>
        const contentArea = document.getElementById("productsArea");
        const pageText = document.getElementById("pageText");
        const paginationArea = document.getElementById("paginationArea");

        var products = [];
        var filteredProducts = [];
        var page = 1;
        const cardSize = 16;

        async function displayProducts() {
            const response = await fetch("../api/getAllProducts.php");
            products = await response.json();
            filteredProducts = products;
            getProducts();
            paginationArea.style.display = filteredProducts.length > cardSize ? 'block' : 'none';
        }

        function getProducts() {
            contentArea.innerHTML = "";
            pageText.innerHTML = page;

            const start = (page - 1) * cardSize;
            const end = start + cardSize;

            filteredProducts.slice(start, end).forEach(product => {
                const isSale = parseFloat(product.price) <= 50;

                contentArea.innerHTML += `
                <div class="col-12 col-md-4 col-lg-3 mb-4">
                    <div class="card rounded-4 h-100" style="cursor: pointer;" onclick="window.location.href='productView.php?id=${product.id}'">
                        <img class="img-fluid object-fit-contain p-3 justify-content-center align-items-center" style="height: 180px;"
                             src="${product.image}" alt="${product.product_name}">
                        ${isSale ? '<div class="card-img-overlay"><span class="badge position-absolute me-3 end-0">Sale</span></div>' : ''}
                        <div class="card-body mt-0 pt-0 d-block">
                            <h5 class="card-title d-none d-md-block text-center fw-bold">${product.product_name}</h5>
                            <h3 class="card-title d-md-none text-center fw-bold">${product.product_name}</h3>
                            <p class="card-text text-center text-secondary">${product.size}</p>
                        </div>
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <span class="h6 d-none d-md-flex" style="color: #6EC064;">PHP ${product.price}</span>
                            <span class="h4 d-md-none" style="color: #6EC064;">PHP ${product.price}</span>
                            <button type="button" class="btn rounded-3 addToCartBtn" data-product-id="${product.id}" onclick="event.stopPropagation(); addToCart(${product.id});">
                                <span class="material-symbols-rounded">shopping_cart</span>
                            </button>
                        </div>
                    </div>
                </div>
                `;
            });
        }

        let currentPriceFilter = '';
        let currentCategoryFilter = '';
        let currentSalesFilter = '';

        document.getElementById('priceOptions').addEventListener('change', function () {
            currentPriceFilter = this.value;
            applyFilters();
        });
        document.getElementById('categoryOptions').addEventListener('change', function () {
            currentCategoryFilter = this.value;
            applyFilters();
        });
        document.getElementById('salesOptions').addEventListener('change', function () {
            currentSalesFilter = this.value;
            applyFilters();
        });

        function applyFilters() {
            page = 1;
            filteredProducts = products;

            console.log('Applying filters:', {
                category: currentCategoryFilter,
                price: currentPriceFilter,
                sales: currentSalesFilter,
                totalProducts: products.length
            });

            if (currentCategoryFilter !== "" && currentCategoryFilter !== "all") {
                if (currentCategoryFilter === 'produce') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'produce');
                } else if (currentCategoryFilter === 'meat') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'meat and seafood');
                } else if (currentCategoryFilter === 'dairy') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'dairy');
                } else if (currentCategoryFilter === 'frozen') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'frozen goods');
                } else if (currentCategoryFilter === 'condiments') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'condiments and sauces');
                } else if (currentCategoryFilter === 'snacks') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'snacks');
                } else if (currentCategoryFilter === 'beverages') {
                    filteredProducts = filteredProducts.filter(product => product.category && product.category.toLowerCase() === 'beverages');
                } else if (currentCategoryFilter === 'personal') {
                    filteredProducts = filteredProducts.filter(product => product.category && (product.category.toLowerCase() === 'personal' || product.category.toLowerCase() === 'health and personal care'));
                }
                console.log('After category filter:', filteredProducts.length);
            }

            if (currentPriceFilter !== "") {
                if (currentPriceFilter === "below-100") {
                    filteredProducts = filteredProducts.filter(product => parseFloat(product.price) < 100);
                } else if (currentPriceFilter === "100-200") {
                    filteredProducts = filteredProducts.filter(product => parseFloat(product.price) >= 100 && parseFloat(product.price) < 200);
                } else if (currentPriceFilter === "200-300") {
                    filteredProducts = filteredProducts.filter(product => parseFloat(product.price) >= 200 && parseFloat(product.price) < 300);
                } else if (currentPriceFilter === "300-400") {
                    filteredProducts = filteredProducts.filter(product => parseFloat(product.price) >= 300 && parseFloat(product.price) < 400);
                } else if (currentPriceFilter === "above-500") {
                    filteredProducts = filteredProducts.filter(product => parseFloat(product.price) > 500);
                }
                console.log('After price filter:', filteredProducts.length);
            }

            if (currentSalesFilter === 'all') {
                filteredProducts = filteredProducts.filter(product => parseFloat(product.price) <= 50);
                console.log('After sales filter:', filteredProducts.length);
            }

            console.log('Final filtered products:', filteredProducts.map(p => ({ name: p.product_name, category: p.category, price: p.price })));

            getProducts();
            paginationArea.style.display = filteredProducts.length > cardSize ? 'block' : 'none';
        }

        function prevPage() {
            if (page > 1) {
                page--;
                getProducts();
            }
        }

        function nextPage() {
            if (page * cardSize < filteredProducts.length) {
                page++;
                getProducts();
            }
        }

        displayProducts();
    </script>

    <?php include './components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>