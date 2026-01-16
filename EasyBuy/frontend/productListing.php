<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - Easy Buy</title>
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

<body class="d-flex flex-column min-vh-100">
    <?php include './components/navbar.php'; ?>

    <div class="container-fluid pt-lg-5">
        <div class="row justify-content-center g-3">
            <div class="col-12 col-lg-3 order-1 order-lg-1">
                <div class="container-fluid p-3">
                    <div class="h5 text-start fw-bold">Filter</div>
                    <div class="mb-2">
                        <select class="form-select" id="priceOptions" aria-label="Filter by price">
                            <option value="" selected>Price</option>
                            <option value="all">All Sale items</option>
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
                </div>
            </div>
            <div class="col-12 col-lg-9 order-2 order-lg-2">
                <div class="container px-0 py-3 justify-content-center">
                    <div class="row g-2 g-md-3" id="productsArea"></div>
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
    <?php include 'components/addToCart.php'; ?>
    <?php include 'components/chatbot.php'; ?>
    <script src="../assets/js/chatbot.js"></script>
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

            getCategoryFromURL();

            if (currentCategoryFilter) {
                applyFilters();
            } else {
                getProducts();
            }

            paginationArea.style.display = filteredProducts.length > cardSize ? 'block' : 'none';
        }

        function getProducts() {
            contentArea.innerHTML = "";
            pageText.innerHTML = page;

            const start = (page - 1) * cardSize;
            const end = start + cardSize;

            filteredProducts.slice(start, end).forEach(product => {
                const isOnSale = product.is_sale == 1;
                const salePrice = isOnSale && product.sale_percentage ?
                    (product.price * (1 - product.sale_percentage / 100)).toFixed(2) :
                    product.price;

                contentArea.innerHTML += `
           <div class="col-6 col-md-4 col-lg-3 mb-2 d-flex justify-content-center">
                    <div class="card rounded-4 h-100" style="max-width: 280px; width: 100%;">
                        ${isOnSale ? `<div class="position-relative">
                            <img class="img-fluid object-fit-contain p-3 d-block mx-auto" style="height: 180px; width: 100%; cursor: pointer;" 
                                 src="${product.image}" alt="${product.product_name}"
                                 onclick="window.location.href='productView.php?id=${product.id}'">
                            <div class="position-absolute top-0 end-0 me-3 mt-3">
                                <span class="badge fw-normal" style="background-color:#28a745;">${product.sale_percentage}% Off</span>
                            </div>
                        </div>` : `<img class="img-fluid object-fit-contain p-3 d-block mx-auto" style="height: 180px; width: 100%; cursor: pointer;" 
                             src="${product.image}" alt="${product.product_name}"
                             onclick="window.location.href='productView.php?id=${product.id}'">`}
                       <div class="card-body mt-0 pt-0 pb-0 d-block" style="cursor:pointer; padding-bottom:0;"  onclick="window.location.href='productView.php?id=${product.id}'">
                        <h6 class="card-title text-center fw-bold" style="font-size:clamp(0.85rem,2.5vw,1rem); display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.4em;">${product.product_name}</h6>
                            <p class="card-text text-center text-secondary mb-0" style="font-size:clamp(0.7rem,2vw,0.9rem); display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.2em;">${product.size}</p>

                        </div>
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-wrap justify-content-start align-items-center mb-2 ${isOnSale ? 'flex-column flex-md-row' : ''} gap-1 gap-md-2" style="max-width:calc(100% - 48px);">

                            ${isOnSale ? `
                                <p class="card-text fw-bold text-success mb-0 order-md-1" style="font-size:1.1em;">
                                    ₱${salePrice}
                                </p>
                                <p class="card-text text-muted text-decoration-line-through mb-0 order-md-2" style="font-size:0.9em;">
                                    ₱${product.price}
                                </p>
                            ` : `
                                <p class="card-text fw-bold text-success mb-0" style="font-size:1.1em;">
                                    ₱${product.price}
                                </p>
                            `}
                        </div>


                            <button type="button" class="btn rounded-3 addToCartBtn" data-product-id="${product.id}" onclick="addToCart(${product.id});">
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

        function getCategoryFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const categoryParam = urlParams.get('category');

            if (categoryParam) {
                const categoryMap = {
                    'produce': 'produce',
                    'meats': 'meat',
                    'dairy': 'dairy',
                    'frozen-foods': 'frozen',
                    'condiments-sauces': 'condiments',
                    'snacks': 'snacks',
                    'beverages': 'beverages',
                    'personal-care': 'personal'
                };

                const mappedCategory = categoryMap[categoryParam];
                if (mappedCategory) {
                    currentCategoryFilter = mappedCategory;
                    document.getElementById('categoryOptions').value = mappedCategory;
                }
            }
        }

        document.getElementById('priceOptions').addEventListener('change', function() {
            currentPriceFilter = this.value;
            applyFilters();
        });
        document.getElementById('categoryOptions').addEventListener('change', function() {
            currentCategoryFilter = this.value;
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
                if (currentPriceFilter === "all") {
                    filteredProducts = filteredProducts.filter(product => product.is_sale == 1);
                } else if (currentPriceFilter === "below-100") {
                    filteredProducts = filteredProducts.filter(product => {
                        const price = product.is_sale == 1 && product.sale_percentage ?
                            parseFloat(product.price) * (1 - product.sale_percentage / 100) :
                            parseFloat(product.price);
                        return price < 100;
                    });
                } else if (currentPriceFilter === "100-200") {
                    filteredProducts = filteredProducts.filter(product => {
                        const price = product.is_sale == 1 && product.sale_percentage ?
                            parseFloat(product.price) * (1 - product.sale_percentage / 100) :
                            parseFloat(product.price);
                        return price >= 100 && price < 200;
                    });
                } else if (currentPriceFilter === "200-300") {
                    filteredProducts = filteredProducts.filter(product => {
                        const price = product.is_sale == 1 && product.sale_percentage ?
                            parseFloat(product.price) * (1 - product.sale_percentage / 100) :
                            parseFloat(product.price);
                        return price >= 200 && price < 300;
                    });
                } else if (currentPriceFilter === "300-400") {
                    filteredProducts = filteredProducts.filter(product => {
                        const price = product.is_sale == 1 && product.sale_percentage ?
                            parseFloat(product.price) * (1 - product.sale_percentage / 100) :
                            parseFloat(product.price);
                        return price >= 300 && price < 400;
                    });
                } else if (currentPriceFilter === "above-500") {
                    filteredProducts = filteredProducts.filter(product => {
                        const price = product.is_sale == 1 && product.sale_percentage ?
                            parseFloat(product.price) * (1 - product.sale_percentage / 100) :
                            parseFloat(product.price);
                        return price > 500;
                    });
                }
                console.log('After price filter:', filteredProducts.length);
            }

            console.log('Final filtered products:', filteredProducts.map(p => ({
                name: p.product_name,
                category: p.category,
                price: p.price
            })));

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