<?php 
    require '../api/classes/Auth.php';
    Auth::requireAuth();    
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
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


    <?php include "components/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var cartItemContainer = document.getElementById('cartItemContainer');
        var cartItemContainerMobile = document.getElementById('cartItemContainerMobile');
        var checkoutSection = document.getElementById('checkoutSection');

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
                                    <input type="checkbox" class="form-check-input cart-checkbox me-3" value="`+ item.cart_id + `" data-price="`+ item.price + `" data-quantity="`+ item.quantity + `" onchange="handleCheckboxChange()" style="width: 20px; height: 20px;">
                                    <img src="`+ item.image + `" class="border border-1 rounded border-dark me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="Product Image">
                                    <div>
                                        <div class="fw-semibold">`+ item.product_name + `</div>
                                        <small class="text-muted">`+ item.category + `</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <button class="qty-btn" onclick="decreaseQty()">-</button>
                                <input type="text" id="qty" class="qty-box" value="`+ item.quantity + `" readonly>
                                <button class="qty-btn" onclick="increaseQty()">+</button>
                            </td>
                            <td class="align-middle text-center">
                                `+ formatPhp((item.price * item.quantity)) + `
                            </td>
                            <td class="align-middle text-center">
                                <button class="btn" onclick="deleteItem(`+ item.id + `)">
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
                                <input type="checkbox" class="form-check-input cart-checkbox me-2 mt-2" value="`+ item.cart_id + `" data-price="`+ item.price + `" data-quantity="`+ item.quantity + `" onchange="handleCheckboxChange()" style="width: 20px; height: 20px;">
                                <img src="`+ item.image + `" class="border border-1 rounded border-dark me-3" style="width: 100px; height: 100px; object-fit: cover;" alt="Product Image">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">`+ item.product_name + `</div>
                                    <small class="text-muted d-block mb-2">`+ item.category + `</small>
                                    <div class="fw-bold text-danger fs-5">`+ formatPhp((item.price * item.quantity)) + `</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <button class="qty-btn" onclick="decreaseQty()">-</button>
                                    <input type="text" class="qty-box" value="`+ item.quantity + `" readonly>
                                    <button class="qty-btn" onclick="increaseQty()">+</button>
                                </div>
                                <button class="btn" onclick="deleteItem(`+ item.id + `)">
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
                            body: JSON.stringify({ cart_ids: checkedItems })
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

        function increaseQty() {
            const qtyEl = document.getElementById('qty');
            if (!qtyEl) return;
            let current = parseInt(qtyEl.value) || 1;
            current += 1;
            qtyEl.value = current;
            updateTotals();
        }

        function decreaseQty() {
            const qtyEl = document.getElementById('qty');
            if (!qtyEl) return;
            let current = parseInt(qtyEl.value) || 1;
            if (current > 1) current -= 1;
            qtyEl.value = current;
            updateTotals();
        }

        document.addEventListener('DOMContentLoaded', updateTotals);

        async function deleteItem(cartItemId) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

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

        function handleCheckboxChange() {
            const checkedItems = getCheckedItems();
            console.log('Checked items:', checkedItems);
            
            // Calculate totals for selected items
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
    </script>
</body>

</html>