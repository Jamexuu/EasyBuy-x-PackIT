<div class="container py-3 pt-4 p-2 p-lg-5">
  <div class="row">
    <div class="col mb-4 d-flex justify-content-center justify-content-lg-start pt-4">
      <div class="h1 fw-bold text-md-center ps-3 text-lg-start " style="color:#6EC064">
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
<script>
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
                <div class="card h-100 rounded-4 shadow-sm">
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
                    <button type="button" class="btn rounded-3" id="addToCart"
                        style="border:1px solid #6EC064;color:#6EC064;">
                        <span class="material-symbols-rounded">shopping_cart</span>
                    </button>
                    <button class="btn btn-sm rounded-3"
                        style="flex:1;background-color:#6EC064;color:white; ">
                        BUY NOW
                    </button>
                    </div>
                </div>
                </div>

                `;
          row.appendChild(col);
        });
        carouselItem.appendChild(row);
        carouselInner.appendChild(carouselItem);
        slideIndex++;
      }
    })
    .catch(err => console.error(err));
</script>