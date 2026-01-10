<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Products - EasyBuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
    <style>
        .product-item:nth-child(even) {
            background-color: #d3d3d3;
        }

        .action-btn:hover {
            opacity: 0.7;
        }

        .btn-remove-image:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../frontend/components/adminNavBar.php'; ?>
    <?php include '../frontend/components/messageModal.php'; ?>
    <?php include '../frontend/components/confirmModal.php'; ?>

    <div class="container py-5" id="productListView">
        <div class="row align-items-center mb-4">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <button class="btn border-0 rounded-pill px-4 py-2" id="addProductBtn"
                    style="background-color: #28a745; color: white;">
                    <span>+</span> Add Product
                </button>
            </div>
            <div class="col-12 col-lg-4 ms-auto">
                <div class="d-flex gap-2 align-items-center">
                    <input type="text" id="searchInput" class="form-control border-0 rounded-pill flex-grow-1"
                        style="background-color: #e0e0e0; padding: 0.5rem 1rem;" placeholder="Search">
                    <span class="material-symbols-rounded text-success fs-2">search</span>
                </div>
            </div>
        </div>
        <div id="productList"></div>
    </div>

    <div class="container py-5" id="productFormView" style="display: none;">
        <div class="row align-items-center mb-4">
            <div class="col-12">
                <button class="btn border-0 rounded-pill px-4 py-2" id="backToListBtn"
                    style="background-color: #6c757d; color: white;">
                    <span>←</span> Back to Products
                </button>
            </div>
        </div>
        <div class="card border-0 shadow-sm p-4" style="background-color: #e8e8e8;">
            <div class="row mb-3">
                <div class="col-12">
                    <h5 class="mb-4 fw-bold" id="formTitle">New Product</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-center position-relative mb-3 w-100 rounded-3"
                        id="uploadBox" style="background-color: #d3d3d3; height: 200px;">
                        <button
                            class="btn-remove-image position-absolute border-0 rounded-circle d-none align-items-center justify-content-center fw-bold text-danger"
                            id="removeImageBtn"
                            style="top: 10px; right: 10px; background-color: rgba(255, 255, 255, 0.9); width: 30px; height: 30px; z-index: 10; cursor: pointer;">✕</button>
                        <img id="imagePreview" src="" alt=""
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px; display: none;">
                    </div>
                    <button id="fileUploadBtn" class="btn border-0 rounded-pill text-white"
                        style="background-color: #28a745; padding: 0.6rem 1.5rem;">
                        <span class="material-symbols-rounded align-middle" style="font-size: 1.2rem;">download</span>
                        File Upload
                    </button>
                    <input type="file" id="fileInput" accept="image/*" style="display: none;">
                </div>
                <div class="col-12 col-md-8">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Product Name *</label>
                            <input type="text" id="productName" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;"
                                placeholder="ARLA Milk Goodness Full Cream">
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Weight in Grams *</label>
                            <input type="text" id="productWeight" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="400">
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Category *</label>
                            <select id="productCategory" class="form-select border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;">
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
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Size in ML</label>
                            <input type="text" id="productSize" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="400 ml">
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Discount</label>
                            <input type="text" id="discountPercentage" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="1%-99%">
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Price *</label>
                            <input type="text" id="productPrice" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="123.35">
                        </div>
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="fw-medium mb-2" style="color: #6a6a6a;">Stocks *</label>
                            <input type="text" id="productStocks" class="form-control border-0 rounded-pill"
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="100">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn border-0 rounded-pill" id="cancelBtn"
                                style="background-color: #6c757d; color: white; padding: 0.6rem 2rem;">Cancel</button>
                            <button class="btn border-0 rounded-pill text-white" id="saveProductBtn"
                                style="background-color: #28a745; padding: 0.6rem 2rem;">Add Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        var products = [];
        var filteredProducts = [];
        var currentImageData = '';
        var editingProductId = null;

        function showProductList() {
            document.getElementById('productListView').style.display = 'block';
            document.getElementById('productFormView').style.display = 'none';
            clearForm();
        }

        function showProductForm(isEdit = false) {
            document.getElementById('productListView').style.display = 'none';
            document.getElementById('productFormView').style.display = 'block';
            document.getElementById('formTitle').textContent = isEdit ? 'Edit Product' : 'New Product';
            document.getElementById('saveProductBtn').textContent = isEdit ? 'Update Product' : 'Add Product';
        }

        function clearForm() {
            document.getElementById('productName').value = '';
            document.getElementById('productWeight').value = '';
            document.getElementById('productSize').value = '';
            document.getElementById('productCategory').value = 'all';
            document.getElementById('productPrice').value = '';
            document.getElementById('productStocks').value = '';
            document.getElementById('discountPercentage').value = '';
            document.getElementById('imagePreview').src = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('removeImageBtn').style.display = 'none';
            document.getElementById('fileInput').value = '';
            currentImageData = '';
            editingProductId = null;
        }

        document.getElementById('addProductBtn').addEventListener('click', function () {
            editingProductId = null;
            showProductForm(false);
        });

        document.getElementById('backToListBtn').addEventListener('click', showProductList);
        document.getElementById('cancelBtn').addEventListener('click', showProductList);

        document.getElementById('fileUploadBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', async function (e) {
            var file = e.target.files[0];
            if (!file) return;

            var formData = new FormData();
            formData.append('image', file);

            try {
                var response = await fetch('../api/uploadProductImage.php', {
                    method: 'POST',
                    body: formData
                });

                var result = await response.json();
                if (result.success) {
                    var img = document.getElementById('imagePreview');
                    var removeBtn = document.getElementById('removeImageBtn');
                    currentImageData = result.path;
                    img.src = currentImageData;
                    img.style.display = 'block';
                    removeBtn.style.display = 'flex';
                } else {
                    alert('Failed to upload image');
                }
            } catch (error) {
                alert('Error uploading image');
                console.error(error);
            }
        });

        document.getElementById('removeImageBtn').addEventListener('click', function () {
            document.getElementById('imagePreview').src = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('removeImageBtn').style.display = 'none';
            document.getElementById('fileInput').value = '';
            currentImageData = '';
        });

        document.getElementById('saveProductBtn').addEventListener('click', async function () {
            var productName = document.getElementById('productName').value.trim();
            var productWeight = document.getElementById('productWeight').value.trim();
            var productSize = document.getElementById('productSize').value.trim();
            var productCategory = document.getElementById('productCategory').value;
            var productPrice = document.getElementById('productPrice').value.trim();
            var salePercentage = document.getElementById('discountPercentage').value.trim();
            var productStocks = document.getElementById('productStocks').value.trim();

            if (!productName || !productSize || !productCategory || !productPrice || !productStocks || !currentImageData) {
                showMessage('error', 'Missing Information', 'Please fill in all required fields and upload an image.', 'OK');
                return;
            }

            var actionText = editingProductId !== null ? 'update' : 'add';
            var confirmResult = await showConfirm(
                'info',
                'Confirm ' + (editingProductId !== null ? 'Update' : 'Add'),
                'Are you sure you want to ' + actionText + ' this product?',
                editingProductId !== null ? 'Update' : 'Add',
                'Cancel'
            );

            if (!confirmResult) return;

            var productData = {
                product_name: productName,
                size: productSize,
                weight_grams: productWeight,
                category: productCategory,
                price: productPrice,
                stocks: productStocks,
                is_sale: salePercentage > 0 ? 1 : 0,
                sale_percentage: salePercentage || 0,
                image: currentImageData
            };

            try {
                var url = editingProductId !== null ? '../api/updateProduct.php' : '../api/addProduct.php';
                var method = editingProductId !== null ? 'PUT' : 'POST';
                
                if (editingProductId !== null) {
                    productData.product_id = editingProductId;
                }

                var response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });

                if (!response.ok) {
                    throw new Error('Failed to save product');
                }

                showMessage('success', 'Product Saved', 'The product has been saved successfully.', 'Continue');
                displayProducts();
                showProductList();
            } catch (error) {
                showMessage('error', 'Error', 'Failed to save product. Please try again.', 'OK');
                console.error(error);
            }
        });

        async function displayProducts() {
            var response = await fetch("../api/getAllProducts.php");
            products = await response.json();
            filteredProducts = products;
            getProducts();
        }

        function getProducts() {
            var productList = document.getElementById('productList');
            productList.innerHTML = '';
            
            if (filteredProducts.length === 0) {
                productList.innerHTML = '<div class="text-center text-muted py-5"><p>No products yet. Click "Add Product" to get started.</p></div>';
                return;
            }
            
            filteredProducts.forEach(function(product) {
                var productId = product.id || product.product_id;
                var productName = product.name || product.product_name;
                var productItem = document.createElement('div');
                productItem.className = 'product-item d-flex justify-content-between align-items-center rounded-3 mb-3';
                productItem.style.cssText = 'background-color: #e8e8e8; padding: 1.25rem 1.5rem;';
                productItem.innerHTML = `
                    <span>${productName}</span>
                    <div class="d-flex gap-2">
                        <button class="action-btn btn border-0 bg-transparent p-1" style="cursor: pointer;" onclick="editProduct(${productId})">
                            <span class="material-symbols-rounded">edit</span>
                        </button>
                        <button class="action-btn btn border-0 bg-transparent p-1" style="cursor: pointer;" onclick="deleteProduct(${productId})">
                            <span class="material-symbols-rounded text-danger">delete</span>
                        </button>
                    </div>
                `;
                productList.appendChild(productItem);
            });
        }

        async function deleteProduct(id) {
            var confirmResult = await showConfirm(
                'delete',
                'Delete Product',
                'Are you sure you want to delete this product? This action cannot be undone.',
                'Delete',
                'Cancel'
            );

            if (!confirmResult) return;

            try {
                var response = await fetch('../api/deleteProduct.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ productId: id })
                });

                if (response.ok) {
                    products = products.filter(function(p) { return (p.id || p.product_id) !== id; });
                    filteredProducts = products;
                    getProducts();
                    showMessage('success', 'Product Deleted', 'The product has been deleted successfully.', 'OK');
                } else {
                    showMessage('error', 'Delete Failed', 'Failed to delete product. Please try again.', 'OK');
                }
            } catch (error) {
                showMessage('error', 'Error', 'An error occurred while deleting the product.', 'OK');
            }
        }

        function editProduct(id) {
            var product = products.find(function(p) { return (p.id || p.product_id) === id; });
            if (!product) return;

            editingProductId = product.id || product.product_id;
            document.getElementById('productName').value = product.name || product.product_name || '';
            document.getElementById('productWeight').value = product.weight_grams || '';
            document.getElementById('productSize').value = product.size || product.product_size || '';
            document.getElementById('productCategory').value = product.category || product.product_category || 'all';
            document.getElementById('productPrice').value = product.price || product.product_price || '';
            document.getElementById('productStocks').value = product.stocks || '';
            document.getElementById('discountPercentage').value = product.sale_percentage || '';
            
            var productImage = product.image || product.product_image;
            if (productImage) {
                currentImageData = productImage;
                document.getElementById('imagePreview').src = productImage;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('removeImageBtn').style.display = 'flex';
            }
            
            showProductForm(true);
        }

        document.getElementById('searchInput').addEventListener('input', function (e) {
            var searchTerm = e.target.value.toLowerCase();
            filteredProducts = products.filter(function(product) {
                var productName = (product.name || product.product_name || '').toLowerCase();
                return productName.includes(searchTerm);
            });
            getProducts();
        });

        displayProducts();
    </script>
</body>

</html>