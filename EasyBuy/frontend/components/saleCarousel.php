<div class="container py-3 pt-4 p-2 p-lg-5">
  <style>
    .card-img-overlay {
      pointer-events: none;
    }
  </style>
  <div class="row">
    <div class="col mb-4 d-flex justify-content-center justify-content-lg-start mt-4 p-0">
      <div class="h1 fw-bold ps-lg-3" style="color:#6EC064">
        Sale
      </div>
    </div>
  </div>
  <div id="saleCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner" id="saleCarouselContent"></div>
    <div class="d-flex justify-content-center gap-3 mt-3">
      <button class="btn p-0" data-bs-target="#saleCarousel" data-bs-slide="prev">
        <span class="material-symbols-rounded" style="font-size:32px; color:#6EC064;">chevron_left</span>
      </button>
      <button class="btn p-0" data-bs-target="#saleCarousel" data-bs-slide="next">
        <span class="material-symbols-rounded" style="font-size:32px; color:#6EC064;">chevron_right</span>
      </button>
    </div>
  </div>
</div>

<div id="carouselLoginRequiredModal" class="modal fade" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 rounded-5 shadow text-center border-0"
      style="width: 85%; max-width: 320px; margin: auto;">
      <div class="h5 fw-bold mb-2">Login Required</div>
      <p class="mb-4">Please log in to continue with your purchase</p>
      <div class="d-flex justify-content-center gap-2">
        <button type="button" class="btn px-4" style="background-color: #e8e8e8; color: #666;"
          data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
          onclick="window.location.href='frontend/login.php'">
          Login
        </button>
      </div>
    </div>
  </div>
</div>

<div id="carouselBuyNowErrorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4 rounded-5 shadow text-center border-0"
      style="width: 85%; max-width: 320px; margin: auto;">
      <div class="h5 fw-bold mb-2">Error</div>
      <p class="mb-4" id="carouselBuyNowErrorMessage">Failed to process request</p>
      <div class="d-flex justify-content-center">
        <button type="button" class="btn px-4 text-white" style="background-color: #6EC064;"
          data-bs-dismiss="modal">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  async function carouselBuyNow(productId) {
    const buyNowBtn = event.target;
    const originalText = buyNowBtn.innerHTML;
    
    buyNowBtn.disabled = true;
    buyNowBtn.textContent = 'Processing...';
    
    try {
      const response = await fetch('api/createDirectCheckout.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          productId: productId,
          quantity: 1
        })
      });

      if (response.status === 401) {
        const loginModal = new bootstrap.Modal(document.getElementById("carouselLoginRequiredModal"));
        loginModal.show();
        buyNowBtn.disabled = false;
        buyNowBtn.innerHTML = originalText;
        return;
      }

      const result = await response.json();
      
      if (result.success) {
        window.location.href = 'frontend/checkout.php';
      } else {
        document.getElementById("carouselBuyNowErrorMessage").textContent = result.error || 'Failed to process request';
        const errorModal = new bootstrap.Modal(document.getElementById("carouselBuyNowErrorModal"));
        errorModal.show();
        buyNowBtn.disabled = false;
        buyNowBtn.innerHTML = originalText;
      }
      
    } catch (error) {
      console.error('Error in carouselBuyNow:', error);
      document.getElementById("carouselBuyNowErrorMessage").textContent = "Failed to process your request. Please try again.";
      const errorModal = new bootstrap.Modal(document.getElementById("carouselBuyNowErrorModal"));
      errorModal.show();
      buyNowBtn.disabled = false;
      buyNowBtn.innerHTML = originalText;
    }
  }

  fetch("api/getDiscountedProducts.php")
    .then(res => res.json())
    .then(products => {

      const carouselInner = document.getElementById("saleCarouselContent");

      const ITEMS_PER_SLIDE = 4;
      let slideIndex = 0;

      for (let i = 0; i < 16; i += ITEMS_PER_SLIDE) {
        const slideProducts = products.slice(i, i + ITEMS_PER_SLIDE);

        const carouselItem = document.createElement("div");
        carouselItem.className = "carousel-item" + (slideIndex === 0 ? " active" : "");

        const row = document.createElement("div");
        row.className = "row justify-content-center g-2 g-md-3";

        slideProducts.forEach(product => {
          const col = document.createElement("div");
          col.className = "col-6 col-lg-3 mb-2";

          const imgSrc = "/EasyBuy-x-PackIT/EasyBuy/Product Images/all/" +
            product.image.split("/").pop();

            col.innerHTML = `
              <div class="card h-100 rounded-4 shadow-sm" style="cursor:pointer;">
                <div class="card-img-overlay"><span class="badge position-absolute me-3 end-0 fw-normal" style="background-color:#28a745;">${product.sale_percentage}% Off</span></div>
                <img src="/EasyBuy-x-PackIT/EasyBuy/Product%20Images/all/${product.image.split('/').pop()}"
                  class="card-img-top p-3" style="height:160px;object-fit:contain;"
                  alt="${product.product_name}">
                <div class="card-body text-center d-flex flex-column p-2 p-sm-3">
                  <h6 class="card-title fw-bold"
                  style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.4em;">
                  ${product.product_name}
                  </h6>
                  <p class="card-title text-secondary"
                  style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.2em;">
                  ${product.size}
                  </p>
                  <div class="d-flex flex-row justify-content-center align-items-center gap-2 mb-2">
                    <p class="card-text fw-bold text-success mb-0" style="font-size:1.1em;">
                      ₱${product.sale_price}
                    </p>
                    <p class="card-text text-muted text-decoration-line-through mb-0" style="font-size:0.9em;">
                      ₱${product.price}
                    </p>
                  </div>
                  <div style="display:flex;gap:8px;width:100%;">
                  <button type="button" class="btn rounded-3 add-to-cart-btn"
                    style="border:1px solid #6EC064;color:#6EC064;">
                    <span class="material-symbols-rounded">shopping_cart</span>
                  </button>
                  <button class="btn btn-sm rounded-3 buy-now-btn"
                    style="flex:1;background-color:#6EC064;color:white;">
                    BUY NOW
                  </button>
                  </div>
                </div>
              </div>
              `;
            // Add event listeners after DOM creation
            const card = col.querySelector('.card');
            const addToCartBtn = col.querySelector('.add-to-cart-btn');
            const buyNowBtn = col.querySelector('.buy-now-btn');
            card.addEventListener('click', function(e) {
              if (
                !e.target.closest('.add-to-cart-btn') &&
                !e.target.closest('.buy-now-btn')
              ) {
                window.location.href = 'frontend/productView.php?id=' + product.id;
              }
            });
            addToCartBtn.addEventListener('click', function(e) {
              e.stopPropagation();
              addToCart(product.id);
            });
            buyNowBtn.addEventListener('click', function(e) {
              e.stopPropagation();
              carouselBuyNow(product.id);
            });
            row.appendChild(col);
          });
        carouselItem.appendChild(row);
        carouselInner.appendChild(carouselItem);
        slideIndex++;
      }
    })
    .catch(err => console.error(err));
</script>