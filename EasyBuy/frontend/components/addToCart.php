<script>
    async function addToCart(productId, quantity = 1) {
        try {
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
                return { requiresLogin: true };
            }

            if (response.ok) {
                const data = await response.json();
                return { message: data.message };
            } else {
                const errorData = await response.json();
                return { error: errorData.message || 'Failed to add item to cart.' };
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            return { error: 'Network error. Please try again.' };
        }
    }
</script>
