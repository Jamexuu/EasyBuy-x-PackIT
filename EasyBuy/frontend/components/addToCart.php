<?php include 'messageModal.php' ?>
<script>
    async function addToCart(productId, quantity = 1) {
        try {
            const response = await fetch('/EasyBuy-x-PackIT/EasyBuy/api/addToCart.php', {
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
                showMessage('info', 'Unauthorized', 'Please log in to add items to your cart.', 'OK');
                return { requiresLogin: true };
            }

            if (response.ok) {
                showMessage('success', 'Added to Cart', 'The item has been added to your cart successfully.', 'OK');
                return { success: true };
            } else {
                showMessage('error', 'Error', 'Failed to add the item to your cart. Please try again.', 'OK');
                return { error: 'Failed to add to cart.' };
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            return { error: 'Network error. Please try again.' };
        }
    }
</script>
