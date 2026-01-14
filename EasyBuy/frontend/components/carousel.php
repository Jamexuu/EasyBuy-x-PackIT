<div id="carouselExampleInterval" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active" data-bs-interval="10000">
      <img src="assets/carousel/1fullReplacement.png" class="d-none d-lg-block w-100" alt="...">
      <img src="assets/carousel/1mediumReplacement.png" class="d-none d-md-block d-lg-none w-100" alt="...">
      <img src="assets/carousel/1smallReplacement.png" class="d-block d-md-none w-100" alt="...">
    </div>

    <div class="carousel-item" data-bs-interval="2000">
      <img src="assets/carousel/2full.png" class="d-none d-lg-block w-100" alt="...">
      <img src="assets/carousel/2medium.png" class="d-none d-md-block d-lg-none w-100" alt="...">
      <img src="assets/carousel/2small.png" class="d-block d-md-none w-100" alt="...">
    </div>

    <div class="carousel-item">
      <img src="assets/carousel/3fullReplacement.png" class="d-none d-lg-block w-100" alt="...">
      <img src="assets/carousel/3mediumReplacement.png" class="d-none d-md-block d-lg-none w-100" alt="...">
      <img src="assets/carousel/3smallReplacement.png" class="d-block d-md-none w-100" alt="...">
    </div>

    <div class="carousel-item">
      <img src="assets/carousel/4full.png" class="d-none d-lg-block w-100" alt="...">
      <img src="assets/carousel/4medium.png" class="d-none d-md-block d-lg-none w-100" alt="...">
      <img src="assets/carousel/4small.png" class="d-block d-md-none w-100" alt="...">
    </div>

    <div class="carousel-item">
      <img src="assets/carousel/5full.png" class="d-none d-lg-block w-100" alt="...">
      <img src="assets/carousel/5medium.png" class="d-none d-md-block d-lg-none w-100" alt="...">
      <img src="assets/carousel/5small.png" class="d-block d-md-none w-100" alt="...">
    </div>
  </div>

  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleInterval"
      data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"
      style="width:10px;height:10px;border-radius:50%;background-color:#cccccc;"></button>
    <button type="button"
      data-bs-target="#carouselExampleInterval"
      data-bs-slide-to="1" class="" aria-label="Slide 2"
      style="width:10px;height:10px;border-radius:50%;background-color:#cccccc;"></button>
    <button type="button" data-bs-target="#carouselExampleInterval"
      data-bs-slide-to="2" class="" aria-label="Slide 3"
      style="width:10px;height:10px;border-radius:50%;background-color:#cccccc;"></button>
    <button type="button"
      data-bs-target="#carouselExampleInterval"
      data-bs-slide-to="3" class="" aria-label="Slide 4"
      style="width:10px;height:10px;border-radius:50%;background-color:#cccccc;"></button>
    <button type="button"
      data-bs-target="#carouselExampleInterval"
      data-bs-slide-to="4" class="" aria-label="Slide 5"
      style="width:10px;height:10px;border-radius:50%;background-color:#cccccc;"></button>
</div>

  <button class="carousel-control-prev" type="button"
    data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>

  <button class="carousel-control-next" type="button"
    data-bs-target="#carouselExampleInterval" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<script>
  function setDotColors(activeIndex) {
    var dots = document.querySelectorAll('#carouselExampleInterval .carousel-indicators button');
    dots.forEach(function(btn, i) {
      btn.style.backgroundColor = (i === activeIndex) ? '#6EC064' : '#cccccc';
    });
  }

  var carouselEl = document.getElementById('carouselExampleInterval');

  carouselEl.addEventListener('slide.bs.carousel', function(e) {
    setDotColors(e.to); 
  });

  setDotColors(0);
</script>