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

    <!-- Product List View -->
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
        <div id="productPagination" class="d-flex justify-content-center align-items-center gap-3 mt-4"></div>
    </div>

    <!-- Product Form View -->
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
                                style="background-color: #f5f5f5; padding: 0.6rem 1rem;" placeholder="400 ml (Optional)">
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
        let products = [];
        let filteredProducts = [];
        let currentPage = 1;
        const pageSize = 10;
        let currentImageData = '';
        let editingProductId = null;

        // View Management
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

        // Form Validation
        function validateProductForm() {
            const productName = document.getElementById('productName').value.trim();
            const productWeight = document.getElementById('productWeight').value.trim();
            const productCategory = document.getElementById('productCategory').value;
            const productPrice = document.getElementById('productPrice').value.trim();
            const productStocks = document.getElementById('productStocks').value.trim();

            if (!productName) {
                showMessage('error', 'Missing Information', 'Please enter a product name.', 'OK');
                return false;
            }

            if (!productWeight) {
                showMessage('error', 'Missing Information', 'Please enter the product weight.', 'OK');
                return false;
            }

            if (!productCategory || productCategory === 'all') {
                showMessage('error', 'Missing Information', 'Please select a product category.', 'OK');
                return false;
            }

            if (!productPrice) {
                showMessage('error', 'Missing Information', 'Please enter the product price.', 'OK');
                return false;
            }

            if (!productStocks) {
                showMessage('error', 'Missing Information', 'Please enter the product stocks.', 'OK');
                return false;
            }

            if (!currentImageData) {
                showMessage('error', 'Missing Information', 'Please upload a product image.', 'OK');
                return false;
            }

            return true;
        }

        // Add Product Function
        async function addProduct() {
            if (!validateProductForm()) return;

            const confirmResult = await showConfirm(
                'info',
                'Confirm Add Product',
                'Are you sure you want to add this product?',
                'Add',
                'Cancel'
            );

            if (!confirmResult) return;

            const sizeValue = document.getElementById('productSize').value.trim();
            const discountValue = document.getElementById('discountPercentage').value.trim();

            const productData = {
                product_name: document.getElementById('productName').value.trim(),
                size: sizeValue ? sizeValue : null,
                weight_grams: document.getElementById('productWeight').value.trim(),
                category: document.getElementById('productCategory').value,
                price: document.getElementById('productPrice').value.trim(),
                stocks: document.getElementById('productStocks').value.trim(),
                is_sale: discountValue > 0 ? 1 : 0,
                sale_percentage: discountValue || 0,
                image: currentImageData
            };

            try {
                const response = await fetch('../api/addProduct.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });

                if (!response.ok) {
                    throw new Error('Failed to add product');
                }

                showMessage('success', 'Product Added', 'The product has been added successfully.', 'Continue');
                await displayProducts();
                showProductList();
            } catch (error) {
                showMessage('error', 'Error', 'Failed to add product. Please try again.', 'OK');
                console.error(error);
            }
        }

        // Update Product Function
        async function updateProduct() {
            if (!validateProductForm()) return;

            const confirmResult = await showConfirm(
                'info',
                'Confirm Update Product',
                'Are you sure you want to update this product?',
                'Update',
                'Cancel'
            );

            if (!confirmResult) return;

            const sizeValue = document.getElementById('productSize').value.trim();
            const discountValue = document.getElementById('discountPercentage').value.trim();

            const productData = {
                product_id: editingProductId,
                product_name: document.getElementById('productName').value.trim(),
                size: sizeValue ? sizeValue : null,
                weight_grams: document.getElementById('productWeight').value.trim(),
                category: document.getElementById('productCategory').value,
                price: document.getElementById('productPrice').value.trim(),
                stocks: document.getElementById('productStocks').value.trim(),
                is_sale: discountValue > 0 ? 1 : 0,
                sale_percentage: discountValue || 0,
                image: currentImageData
            };

            try {
                const response = await fetch('../api/updateProduct.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });

                if (!response.ok) {
                    throw new Error('Failed to update product');
                }

                showMessage('success', 'Product Updated', 'The product has been updated successfully.', 'Continue');
                await displayProducts();
                showProductList();
            } catch (error) {
                showMessage('error', 'Error', 'Failed to update product. Please try again.', 'OK');
                console.error(error);
            }
        }

        // Delete Product Function
        async function deleteProduct(id) {
            const confirmResult = await showConfirm(
                'delete',
                'Delete Product',
                'Are you sure you want to delete this product? This action cannot be undone.',
                'Delete',
                'Cancel'
            );

            if (!confirmResult) return;

            try {
                const response = await fetch('../api/deleteProduct.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ productId: id })
                });

                if (response.ok) {
                    products = products.filter(p => (p.id || p.product_id) !== id);
                    filteredProducts = filteredProducts.filter(p => (p.id || p.product_id) !== id);
                    
                    // Adjust current page if needed
                    const totalPages = Math.max(1, Math.ceil(filteredProducts.length / pageSize));
                    if (currentPage > totalPages) currentPage = totalPages;
                    
                    getProducts();
                    showMessage('success', 'Product Deleted', 'The product has been deleted successfully.', 'OK');
                } else {
                    showMessage('error', 'Delete Failed', 'Failed to delete product. Please try again.', 'OK');
                }
            } catch (error) {
                showMessage('error', 'Error', 'An error occurred while deleting the product.', 'OK');
            }
        }

        // Edit Product Function
        function editProduct(id) {
            const product = products.find(p => (p.id || p.product_id) === id);
            if (!product) return;

            editingProductId = product.id || product.product_id;
            document.getElementById('productName').value = product.name || product.product_name || '';
            document.getElementById('productWeight').value = product.weight_grams || '';
            document.getElementById('productSize').value = product.size || product.product_size || '';
            document.getElementById('productCategory').value = product.category || product.product_category || 'all';
            document.getElementById('productPrice').value = product.price || product.product_price || '';
            document.getElementById('productStocks').value = product.stocks || '';
            document.getElementById('discountPercentage').value = product.sale_percentage || '';

            const productImage = product.image || product.product_image;
            if (productImage) {
                currentImageData = productImage;
                document.getElementById('imagePreview').src = productImage;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('removeImageBtn').style.display = 'flex';
            }

            showProductForm(true);
        }

        // Display Products with Pagination
        async function displayProducts() {
            try {
                const response = await fetch("../api/getAllProducts.php");
                products = await response.json();
                filteredProducts = products;
                currentPage = 1;
                getProducts();
            } catch (error) {
                console.error('Error fetching products:', error);
                showMessage('error', 'Error', 'Failed to load products.', 'OK');
            }
        }

        function getProducts() {
            const productList = document.getElementById('productList');
            productList.innerHTML = '';

            if (filteredProducts.length === 0) {
                productList.innerHTML = '<div class="text-center text-muted py-5"><p>No products found.</p></div>';
                document.getElementById('productPagination').innerHTML = '';
                return;
            }

            const totalPages = Math.max(1, Math.ceil(filteredProducts.length / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const pageItems = filteredProducts.slice(startIndex, endIndex);

            pageItems.forEach(product => {
                const productId = product.id || product.product_id;
                const productName = product.name || product.product_name;
                const productItem = document.createElement('div');
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

            renderPagination(filteredProducts.length, currentPage, pageSize);
        }

        // Pagination Functions
        function renderPagination(totalItems, page, perPage) {
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            const container = document.getElementById('productPagination');
            
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = `
                <button class="btn btn-sm btn-outline-secondary" ${page === 1 ? 'disabled' : ''} onclick="prevPage()">
                    Previous
                </button>
                <div class="px-3 fw-medium">
                    Page ${page} of ${totalPages}
                </div>
                <button class="btn btn-sm btn-outline-secondary" ${page === totalPages ? 'disabled' : ''} onclick="nextPage()">
                    Next
                </button>
            `;
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                getProducts();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function nextPage() {
            const totalPages = Math.max(1, Math.ceil(filteredProducts.length / pageSize));
            if (currentPage < totalPages) {
                currentPage++;
                getProducts();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Event Listeners
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
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('../api/uploadProductImage.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    const img = document.getElementById('imagePreview');
                    const removeBtn = document.getElementById('removeImageBtn');
                    currentImageData = result.path;
                    img.src = currentImageData;
                    img.style.display = 'block';
                    removeBtn.style.display = 'flex';
                } else {
                    showMessage('error', 'Upload Failed', 'Failed to upload image. Please try again.', 'OK');
                }
            } catch (error) {
                showMessage('error', 'Error', 'An error occurred while uploading the image.', 'OK');
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

        document.getElementById('saveProductBtn').addEventListener('click', function () {
            if (editingProductId !== null) {
                updateProduct();
            } else {
                addProduct();
            }
        });

        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            filteredProducts = products.filter(product => {
                const productName = (product.name || product.product_name || '').toLowerCase();
                return productName.includes(searchTerm);
            });
            currentPage = 1;
            getProducts();
        });

        // Initialize
        displayProducts();
    </script>
</body>

</html>