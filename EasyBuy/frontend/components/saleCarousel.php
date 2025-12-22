<div class="container py-3 pt-4 p-2 p-lg-5">
  <div class="row">
    <div class="col mb-4 d-flex justify-content-center justify-content-lg-start">
      <div class="h1 fw-bold text-md-center ps-3 text-lg-start" style="color:#6EC064">
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
  fetch("/EasyBuy-x-PackIT/EasyBuy/assets/products.json")
    .then(res => res.json())
    .then(products => {

      const carouselInner = document.getElementById("saleCarouselContent");

      const ITEMS_PER_SLIDE = 4;
      let slideIndex = 0;

      for (let i = 0; i < 12; i += ITEMS_PER_SLIDE) {
        const slideProducts = products.slice(i, i + ITEMS_PER_SLIDE);

        const carouselItem = document.createElement("div");
        carouselItem.className = "carousel-item" + (slideIndex === 0 ? " active" : "");

        const row = document.createElement("div");
        row.className = "row justify-content-center";

        slideProducts.forEach(product => {
          const col = document.createElement("div");
          col.className = "col-6 col-md-3 mb-4";

          const imgSrc = "/EasyBuy-x-PackIT/EasyBuy/Product Images/all/" +
            product.image.split("/").pop();

     col.innerHTML = `
<div class="card h-100 rounded-4 shadow-sm">
  <img src="/EasyBuy-x-PackIT/EasyBuy/Product%20Images/all/${product.image.split('/').pop()}"
    class="card-img-top p-3" style="height:160px;object-fit:contain;"
    alt="${product["Product Name"]}">
  <div class="card-body text-center d-flex flex-column p-2 p-sm-3">
    <h6 class="card-title fw-bold"
      style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.4em;">
      ${product["Product Name"]}
    </h6>
    <p class="card-title text-secondary"
      style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.2em;">
      ${product["Size"]}
    </p>
    <p class="card-text fw-bold mt-auto text-success" style="font-size:1.1em;">
      ₱${product["Price"]}
    </p>
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