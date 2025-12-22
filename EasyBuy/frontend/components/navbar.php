<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
</head>

<body>

  <div class="d-block d-lg-none text-center" style="background: var(--gradient-color);">
    <div class="pt-3 pb-1 ">
      <img src="assets/easybuylogolongwhite.svg" alt="EasyBuy" style="max-height:36px;">
    </div>
    <div class="ps-3 pe-1 pb-2">
      <div class="d-flex align-items-center gap-0 w-100 mb-2">
        <input type="text" placeholder="Search EasyBuy" class="form-control rounded-5">
        <button class="btn" style="border-radius:999px;">
          <span class="material-symbols-rounded text-white fs-2">
            search
          </span>
        </button>
      </div>
    </div>
  </div>

  <div class="navbar navbar-expand-lg d-none d-lg-flex" style="background: var(--gradient-color)">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="assets/navbar_logo.svg" alt="" class="img-fluid px-lg-5 p-2" style="max-height: 68px;">
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="material-symbols-rounded text-white fs-2">menu</span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav navbar-text mb-2 ms-auto mb-lg-0 gap-4">
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="#">HOME</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="#">SALE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="#">CATEGORIES</a>
          </li>
        </ul>
        <div class="d-flex flex-column flex-lg-row align-items-center gap-2 px-5">
          <div class="d-flex align-items-center gap-2 w-100 w-lg-auto">
            <input type="text" placeholder="Search" class="form-control rounded-5">
            <div class="btn">
              <span class="material-symbols-rounded text-white fs-2">
                search
              </span>
            </div>
          </div>
          <div class="d-flex gap-2 justify-content-center">
            <div class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                account_circle
              </span><br>
              <span class="text-white"></span>
            </div>
            <div class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                shopping_cart
              </span><br>
              <span class="text-white"></span>
            </div>
            <div class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                support_agent
              </span><br>
              <span class="text-white"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="navbar navbar-dark fixed-bottom d-flex d-lg-none py-0" style="background:#ffffff;">
    <div class="container-fluid justify-content-around py-0" style="padding-left:0;padding-right:0;">
      <a href="#"
        class="nav-link text-center active"
        style="background:#6EC064; width:64px; height:56px; display:flex; align-items:center;
         justify-content:center; padding:0;">
        <span class="material-symbols-rounded"
          style="color:#ffffff;font-size:26px;line-height:1;">
          home
        </span>
      </a>
      <a href="#"
        class="nav-link text-center"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;padding:0;">
        <span class="material-symbols-rounded"
          style="color:#00b369;font-size:26px;line-height:1;">
          sell
        </span>
      </a>
      <a href="#"
        class="nav-link text-center"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;padding:0;">
        <span class="material-symbols-rounded"
          style="color:#00b369;font-size:26px;line-height:1;">
          grid_view
        </span>
      </a>
      <a href="#"
        class="nav-link text-center"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;padding:0;">
        <span class="material-symbols-rounded"
          style="color:#00b369;font-size:26px;line-height:1;">
          shopping_cart
        </span>
      </a>
      <a href="#"
        class="nav-link text-center"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;padding:0;">
        <span class="material-symbols-rounded"
          style="color:#00b369;font-size:26px;line-height:1;">
          account_circle
        </span>
      </a>
    </div>
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
</body>

</html>