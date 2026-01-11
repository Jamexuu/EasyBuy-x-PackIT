<?php 
    require '../api/classes/Auth.php';
    Auth::requireAuth();    
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins';
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body > .container,
        body > .container-fluid {
            flex: 1;
        }

        .card-custom {
            background-color: #f1f1f1;
            border-radius: 12px;
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

        .btn-order {
            background-color: #6EC064;
        }

        .btn-secondary:hover {
            background-color: #349C55;
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

        .form-check-input:checked {
            background-color: #6EC064;
            border-color: #6EC064;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container mt-4 text-strong fw-bold">
        <h3>Your Shopping Cart</h3>
    </div>


    <div class="container mt-3">
        <div class="row g-4">
            <div class="col-12">
                <div class="card rounded-4 overflow-hidden shadow-sm border-0">
                    <!-- Desktop Table View -->
                    <table class="table mb-0 d-none d-md-table">
                        <thead>
                            <tr class="table-secondary text-center">
                                <th scope="col" class="w-25">ITEM</th>
                                <th scope="col" class="w-auto">QUANTITY</th>
                                <th scope="col" class="w-auto">SUBTOTAL</th>
                                <th scope="col" class="w-auto"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItemContainer">

                        </tbody>
                    </table>
                    
                    <!-- Mobile Card View -->
                    <div class="d-md-none" id="cartItemContainerMobile">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5 d-flex justify-content-end align-items-center gap-4" id="checkoutSection">
        
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <div class="h5 fw-bold mb-2">Remove Item</div>
                <p class="mb-4">Are you sure you want to remove this item from your cart?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn px-4" style="background-color: #e8e8e8; color: #666;"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn px-4 text-white" style="background-color: #dc3545;"
                        id="confirmDeleteBtn">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include "components/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var cartItemContainer = document.getElementById('cartItemContainer');
        var cartItemContainerMobile = document.getElementById('cartItemContainerMobile');
        var checkoutSection = document.getElementById('checkoutSection');
        var qtyBoxes = document.querySelectorAll('.qty-box');
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        var itemToDelete = null;

        async function getCartItems() {
            try {
                const response = await fetch('../api/getCartItems.php', {
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
                    // desktop table row
                    cartItemContainer.innerHTML += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input cart-checkbox me-3" value="`+ item.id + `" data-price="`+ item.final_price + `" data-quantity="`+ item.quantity + `" onchange="handleCheckboxChange()" style="width: 20px; height: 20px;">
                                    <img src="`+ item.image + `" class="border border-1 rounded border-dark me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="Product Image">
                                    <div>
                                        <div class="fw-semibold">`+ item.product_name + `</div>
                                        <small class="text-muted">`+ item.category + `</small>
                                        ${ item.is_sale == 1 ? `<span class="badge" style="background-color:#28a745;">`+ item.sale_percentage + `% Off</span>` : '' }
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <button class="qty-btn" onclick="decreaseQty(this, `+ item.id + `)">-</button>
                                <input type="text" class="qty-box" value="`+ item.quantity + `" readonly>
                                <button class="qty-btn" onclick="increaseQty(this, `+ item.id + `)">+</button>
                            </td>
                            <td class="align-middle text-center">
                                ${ item.is_sale == 1 ? `
                                    <div><small class="text-decoration-line-through text-muted" data-cart-id="`+ item.id + `" data-type="original">`+ formatPhp((item.price * item.quantity)) + `</small></div>
                                    <div class="fw-bold text-success" data-cart-id="`+ item.id + `" data-type="subtotal">`+ formatPhp((item.final_price * item.quantity)) + `</div>
                                ` : `<div class="fw-bold" data-cart-id="`+ item.id + `" data-type="subtotal">`+ formatPhp((item.final_price * item.quantity)) + `</div>` }
                            </td>
                            <td class="align-middle text-center">
                                <button class="btn" onclick="showDeleteModal(`+ item.id + `)">
                                    <span class="material-symbols-outlined">
                                        delete
                                    </span>
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    // mobile card layout
                    cartItemContainerMobile.innerHTML += `
                        <div class="border-bottom p-3">
                            <div class="d-flex mb-3">
                                <input type="checkbox" class="form-check-input cart-checkbox me-2 mt-2" value="`+ item.id + `" data-price="`+ item.final_price + `" data-quantity="`+ item.quantity + `" onchange="handleCheckboxChange()" style="width: 20px; height: 20px;">
                                <img src="`+ item.image + `" class="border border-1 rounded border-dark me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="Product Image">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">`+ item.product_name + `</div>
                                    <small class="text-muted d-block mb-2">`+ item.category + `</small>
                                    ${ item.is_sale == 1 ? `<span class="badge mb-2" style="background-color:#28a745;">`+ item.sale_percentage + `% Off</span>` : '' }
                                    ${ item.is_sale == 1 ? `
                                        <div><small class="text-decoration-line-through text-muted" data-cart-id="`+ item.id + `" data-type="original">`+ formatPhp((item.price * item.quantity)) + `</small></div>
                                        <div class="fw-bold text-success fs-5" data-cart-id="`+ item.id + `" data-type="subtotal">`+ formatPhp((item.final_price * item.quantity)) + `</div>
                                    ` : `<div class="fw-bold text-success fs-5" data-cart-id="`+ item.id + `" data-type="subtotal">`+ formatPhp((item.final_price * item.quantity)) + `</div>` }
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <button class="qty-btn" onclick="decreaseQty(this, `+ item.id + `)">-</button>
                                    <input type="text" class="qty-box" value="`+ item.quantity + `" readonly>
                                    <button class="qty-btn" onclick="increaseQty(this, `+ item.id + `)">+</button>
                                </div>
                                <button class="btn" onclick="showDeleteModal(`+ item.id + `)">
                                    <span class="material-symbols-outlined">
                                        delete
                                    </span>
                                </button>
                            </div>
                        </div>
                    `;
                });
            } catch (error) {
                console.error('Error fetching cart items:', error);
                return [];
            }
        }

        getCartItems();

        async function getCartSummary(){
            try {
                const response = await fetch('../api/getCartSummary.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                checkoutSection.innerHTML = `
                    <p class="fw-normal" id="totalDisplay">
                        <strong style="color: #6EC064;">Subtotal: ₱<span id="selectedSubtotal">0.00</span></strong>
                        <br>
                        Shipping: ₱<span id="selectedShipping">0.00</span>
                        <br>
                        <strong style="color: #6EC064;">Total: ₱<span id="selectedTotal">0.00</span></strong>
                    </p>
                    <form method="post" action="checkout.php">
                        <button type="submit" id="checkoutBtn" class="btn text-white fw-bold py-2 px-4" style="background-color: #6EC064;" disabled>
                            CHECKOUT
                        </button>
                    </form>
                `;

                // Add event listener AFTER form is created
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const checkedItems = getCheckedItems();
                        
                        await fetch('../api/saveCartItems.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ cart_item_ids: checkedItems })
                        });
                        
                        window.location.href = 'checkout.php';
                    });
                }

                console.log(data);
            } catch (error) {
                console.error('Error fetching cart items:', error);
                return [];
            }
        }

        getCartSummary();

        function formatPhp(n) {
            return '₱' + n.toFixed(2);
        }

        function increaseQty(button, cartItemId) {
            var container = button.parentElement;
            var qtyEl = container.querySelector('.qty-box');
            var qty = parseInt(qtyEl.value) + 1;
            
            qtyEl.value = qty;
            updateItemDisplay(cartItemId, qty);
            updateQty(cartItemId, qty);
        }

        function decreaseQty(button, cartItemId) {
            var container = button.parentElement;
            var qtyEl = container.querySelector('.qty-box');
            var qty = parseInt(qtyEl.value) - 1;
            if (qty < 1) qty = 1;
            
            qtyEl.value = qty;
            updateItemDisplay(cartItemId, qty);
            updateQty(cartItemId, qty);
        }

        function updateItemDisplay(cartItemId, qty) {
            var checkbox = document.querySelector('.cart-checkbox[value="' + cartItemId + '"]');
            var price = parseFloat(checkbox.dataset.price);
            checkbox.dataset.quantity = qty;
            
            var subtotalElements = document.querySelectorAll('[data-cart-id="' + cartItemId + '"][data-type="subtotal"]');
            subtotalElements.forEach(function(el) {
                el.textContent = formatPhp(price * qty);
            });
            
            var originalElements = document.querySelectorAll('[data-cart-id="' + cartItemId + '"][data-type="original"]');
            if (originalElements.length > 0) {
                var originalPrice = price / (1 - parseFloat(checkbox.closest('tr, .border-bottom').querySelector('.badge')?.textContent || '0') / 100);
                originalElements.forEach(function(el) {
                    el.textContent = formatPhp(originalPrice * qty);
                });
            }
            
            updateTotals();
        }

        async function updateQty(cartItemId, quantity) {
            try {
                var response = await fetch('../api/updateCartQuantity.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        itemId: cartItemId,
                        quantity: quantity
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to update quantity');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('Failed to update quantity. Please try again.');
            }
        }

        document.addEventListener('DOMContentLoaded', updateTotals);

        function showDeleteModal(cartItemId) {
            itemToDelete = cartItemId;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
            if (itemToDelete !== null) {
                await deleteItem(itemToDelete);
                deleteModal.hide();
                itemToDelete = null;
            }
        });

        async function deleteItem(cartItemId) {
            try {
                const response = await fetch('../api/deleteCartItem.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ cart_item_id: cartItemId })
                });

                if (!response.ok) {
                    throw new Error('Failed to delete item');
                }

                const data = await response.json();
                console.log(data.message);
                
                location.reload();
            } catch (error) {
                console.error('Error deleting item:', error);
                alert('Failed to delete item. Please try again.');
            }
        }

        function getCheckedItems() {
            const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
            const checkedIds = [];
            checkboxes.forEach(checkbox => {
                checkedIds.push(checkbox.value);
            });
            return checkedIds;
        }

        function calculateSelectedTotal() {
            const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
            let subtotal = 0;
            
            checkboxes.forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                const quantity = parseInt(checkbox.dataset.quantity);
                subtotal += (price * quantity);
            });
            
            const shipping = checkboxes.length > 0 ? 50 : 0; // based sa calculation nila cj
            const total = subtotal + shipping;
            
            return {
                subtotal: subtotal.toFixed(2),
                shipping: shipping.toFixed(2),
                total: total.toFixed(2),
                itemCount: checkboxes.length
            };
        }

        function updateTotals() {
            // Recalculate totals based on current quantities and selections
            const totals = calculateSelectedTotal();
            
            // Update display
            const subtotalEl = document.getElementById('selectedSubtotal');
            const shippingEl = document.getElementById('selectedShipping');
            const totalEl = document.getElementById('selectedTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            if (subtotalEl) subtotalEl.textContent = totals.subtotal;
            if (shippingEl) shippingEl.textContent = totals.shipping;
            if (totalEl) totalEl.textContent = totals.total;
            
            // Enable/disable checkout button
            if (checkoutBtn) {
                if (totals.itemCount > 0) {
                    checkoutBtn.disabled = false;
                    checkoutBtn.style.opacity = '1';
                } else {
                    checkoutBtn.disabled = true;
                    checkoutBtn.style.opacity = '0.5';
                }
            }
        }

        function handleCheckboxChange() {
            const checkedItems = getCheckedItems();
            console.log('Checked items:', checkedItems);
            updateTotals();
        }
    </script>
</body>

</html>