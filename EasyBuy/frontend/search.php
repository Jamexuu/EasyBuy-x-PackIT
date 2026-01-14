<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - Easy Buy</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=shopping_cart" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        #addToCart, #nextBtn, #prevBtn {
            background-color: #6EC064;
            color: #FFFFFF;
        }

        #addToCart:hover, #nextBtn:hover, #prevBtn:hover {
            background-color: lightgray;
            color: dimgray
        }
    </style>
</head>

<body class="d-flex flex-column vh-100">
    <?php include './components/navbar.php'; ?>

    <div class="container mt-3 pt-5">
        <div class="h3 mb-3" id="resultsLabel"></div>
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
    <script>
        const contentArea = document.getElementById("productsArea");
        const pageText = document.getElementById("pageText");
        const paginationArea = document.getElementById("paginationArea");
        const resultsLabel = document.getElementById("resultsLabel");

        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');

        var products = [];
        var page = 1;
        const cardSize = 16;

        async function displayProducts() {
            const response = await fetch(`../api/searchProducts.php?keyword=${encodeURIComponent(query || '')}`);
            resultsLabel.innerText = `Search results for "${query || ''}"`;
            products = await response.json();
            getProducts();
        }

        function getProducts() {
            contentArea.innerHTML = "";
            pageText.innerHTML = page;

            const start = (page - 1) * cardSize;
            const end = start + cardSize;

            products.slice(start, end).forEach(product => {
                contentArea.innerHTML += `
                <div class="col-12 col-md-4 col-lg-3 mb-4">
                    <div class="card rounded-4 h-100" style="cursor: pointer;" onclick="window.location.href='productView.php?id=${product.id}'">
                        <img class="img-fluid object-fit-contain p-3 justify-content-center align-items-center" style="height: 180px;"
                             src="${product.image}" alt="${product.product_name}">
                        <div class="card-body mt-0 pt-0 d-block">
                            <h5 class="card-title d-none d-md-block text-center fw-bold">${product.product_name}</h5>
                            <h3 class="card-title d-md-none text-center fw-bold">${product.product_name}</h3>
                            <p class="card-text text-center text-secondary">${product.size}</p>
                        </div>
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <span class="h6 d-none d-md-flex" style="color: #6EC064;">PHP ${product.price}</span>
                            <span class="h4 d-md-none" style="color: #6EC064;">PHP ${product.price}</span>
                            <button type="button" class="btn rounded-3" id="addToCart" onclick="event.stopPropagation()">
                                <span class="material-symbols-rounded">shopping_cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            });
        }

        function prevPage() {
            if (page > 1) {
                page--;
                getProducts();
            }
        }

        function nextPage() {
            if (page * cardSize < products.length) {
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