<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
</head>

<body>

  <div class="d-block d-lg-none text-center sticky-top" style="background: var(--gradient-color);">
    <div class="pt-3 pb-1 ">
      <img src="/EasyBuy-x-PackIT/EasyBuy/assets/easybuylogolongwhite.svg" alt="EasyBuy" style="max-height:36px;">
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
      <a class="navbar-brand" href="/EasyBuy-x-PackIT/EasyBuy/index.php">
        <img src="/EasyBuy-x-PackIT/EasyBuy/assets/navbar_logo.svg" alt="" class="img-fluid px-lg-5 p-2" style="max-height: 68px;">
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="material-symbols-rounded text-white fs-2">menu</span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav navbar-text mb-2 ms-auto mb-lg-0 gap-4">
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="/EasyBuy-x-PackIT/EasyBuy/index.php">HOME</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="/EasyBuy-x-PackIT/EasyBuy/index.php#sale">SALE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white ps-3" href="/EasyBuy-x-PackIT/EasyBuy/index.php#categories">CATEGORIES</a>
          </li>
        </ul>
        <div class="d-flex flex-column flex-lg-row align-items-center gap-2 px-5">
          <div class="d-flex align-items-center gap-2 w-100 w-lg-auto">
            <input type="text" placeholder="Search" id="searchInput" class="form-control rounded-5">
            <a href="#" onclick="searchProduct()" class="btn" id="searchButton" style="border-radius:999px;">
              <span class="material-symbols-rounded text-white fs-2">
                search
              </span>
            </a>
          </div>
          <div class="d-flex gap-2 justify-content-center">
            <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/login.php" class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                account_circle
              </span><br>
              <span class="text-white"></span>
            </a>
            <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/cart.php" class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                shopping_cart
              </span><br>
              <span class="text-white"></span>
            </a>
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

  <div class="navbar navbar-dark fixed-bottom d-flex d-lg-none py-0 z-1" style="background:#ffffff; box-shadow: 0 -8px 16px rgba(0, 0, 0, 0.11);">
    <div class="container-fluid justify-content-around p-0">
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php"
        class="nav-link text-center active p-4"
        style="background:#6EC064; display:flex; align-items:center;
         justify-content:center;">
        <span class="material-symbols-rounded fs-1"
          style="color:#ffffff;">
          home
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php#sale"
        class="nav-link text-center p-0"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-rounded fs-1"
          style="color:#00b369;">
          sell
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php#categories"
        class="nav-link text-center p-0"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-rounded fs-1"
          style="color:#00b369;">
          grid_view
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/cart.php"
        class="nav-link text-center p-0"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-rounded fs-1"
          style="color:#00b369;">
          shopping_cart
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/login.php"
        class="nav-link text-center p-0"
        style="background:#ffffff; flex:1;display:flex;align-items:center;justify-content:center;">
        <span class="material-symbols-rounded fs-1"
          style="color:#00b369;">
          account_circle
        </span>
      </a>
    </div>
  </div>

    <script>
        const searchButton = document.getElementById('searchButton');
        const searchInput = document.getElementById('searchInput');

        function searchProduct(){
            const query = searchInput.value;
            window.location.href='/EasyBuy-x-PackIT/EasyBuy/frontend/search.php?q=' + encodeURIComponent(query || '');
        }

        searchInput.addEventListener('keydown', function(event){
            if(event.key === 'Enter'){
                event.preventDefault();
                searchProduct();
            }
        });
    </script>
</body>

</html>