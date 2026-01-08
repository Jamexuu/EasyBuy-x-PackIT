<?php include 'messageModal.php'; ?>
<script>
    async function addToCart(productId, quantity = 1) {
        const response = await fetch('../api/addToCart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                productId: productId,
                quantity: quantity
            })
        });

        if (response.status === 401) {
            showMessage('error', 'Unauthorized', 'Please log in to add items to your cart.');
            window.location.href = 'login.php';
            return null;
        }

        if (response.ok) {
            const data = await response.json();
            showMessage('success', 'Item Added to Cart', data.message);
            return data;
        } else {
            const errorData = await response.json();
            showMessage('error', 'Error', errorData.message || 'Failed to add item to cart.');
            return null;
        }
    }
</script>
