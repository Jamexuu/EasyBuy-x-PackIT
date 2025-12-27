function addToCart(productId, quantity = 1) {
    fetch('../api/addToCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            productId: productId,
            quantity: quantity
        })
    }).then(response => {
        if (response.status === 401) {
            alert('Please log in to add items to your cart');
            window.location.href = 'login.php';
            return null;
        }
        return response.json();
    })
        .then(data => {
            if (data) {
                if (data.message) {
                    alert(data.message);
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Failed to add product to cart');
        });
}