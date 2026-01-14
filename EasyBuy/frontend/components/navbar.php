<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet" />
  <style>
    .navbar-toggler:focus,
    .navbar-toggler:active {
      box-shadow: none !important;
      outline: none !important;
      border: none !important;
    }

    .btn:focus,
    .btn:active,
    .nav-link:focus,
    .nav-link:active {
      box-shadow: none !important;
      outline: none !important;
    }

    .mobile-nav-link {
      background: #ffffff;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      padding: 1rem 0 !important;
      position: relative;
      min-height: 64px;
    }

    .mobile-nav-link:focus,
    .mobile-nav-link:active {
      background: #6EC064 !important;
      outline: none !important;
      box-shadow: inset 0 0 0 3px rgba(110, 192, 100, 0.3) !important;
    }

    .mobile-nav-link.active {
      background: #6EC064 !important;
    }

    .mobile-nav-link:focus .material-symbols-rounded,
    .mobile-nav-link:active .material-symbols-rounded {
      color: #ffffff !important;
      transform: scale(1.1);
    }

    .mobile-nav-link .material-symbols-rounded {
      color: #00b369;
      transition: all 0.3s ease;
    }

    .mobile-nav-link.active .material-symbols-rounded {
      color: #ffffff !important;
    }

    .mobile-navbar-container {
      width: 100%;
      display: flex;
    }

    .mobile-navbar-container .container-fluid {
      width: 100%;
      padding: 0;
      margin: 0;
      max-width: 100%;
    }

    .mobile-nav-link {
      -webkit-tap-highlight-color: transparent;
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      user-select: none;
    }
  </style>
</head>

<body>

  <div class="d-block d-lg-none text-center sticky-top" style="background: var(--gradient-color);">
    <a href="/EasyBuy-x-PackIT/EasyBuy/index.php" class="pt-3 pb-1 ">
      <img src="/EasyBuy-x-PackIT/EasyBuy/assets/easybuylogolongwhite.svg" alt="EasyBuy" style="max-height:36px;">
    </a>
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
        <img src="/EasyBuy-x-PackIT/EasyBuy/assets/navbar_logo.svg" alt="" class="img-fluid px-lg-5 p-2"
          style="max-height: 68px;">
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
            <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/cart.php" class="btn position-relative">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                shopping_cart
              </span>
              <span id="cartBadge" class="position-absolute badge rounded-pill bg-danger"
                style="display: none; top: 5px; right: 5px; font-size: 0.75rem; padding: 0.35em 0.5em;">
                0
              </span>
            </a>
            <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/customerSupport.php" class="btn">
              <span class="material-symbols-rounded text-white fs-1 fs-lg-2">
                support_agent
              </span><br>
              <span class="text-white"></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="navbar navbar-dark fixed-bottom d-flex d-lg-none py-0 z-1 mobile-navbar-container"
    style="background:#ffffff; box-shadow: 0 -8px 16px rgba(0, 0, 0, 0.11);">
    <div class="container-fluid justify-content-around p-0">
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php" class="nav-link text-center mobile-nav-link" data-nav="home"
        onclick="setActiveNav(event, 'home')" tabindex="0">
        <span class="material-symbols-rounded fs-1">
          home
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php#sale" class="nav-link text-center mobile-nav-link" data-nav="sale"
        onclick="setActiveNav(event, 'sale')" tabindex="0">
        <span class="material-symbols-rounded fs-1">
          sell
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/index.php#categories" class="nav-link text-center mobile-nav-link"
        data-nav="categories" onclick="setActiveNav(event, 'categories')" tabindex="0">
        <span class="material-symbols-rounded fs-1">
          grid_view
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/cart.php"
        class="nav-link text-center mobile-nav-link position-relative" data-nav="cart"
        onclick="setActiveNav(event, 'cart')" tabindex="0">
        <span class="material-symbols-rounded fs-1">
          shopping_cart
        </span>
        <span id="cartBadgeMobile" class="position-absolute badge rounded-pill bg-danger"
          style="display: none; top: 8px; right: 30%; font-size: 0.65rem; padding: 0.3em 0.45em;">
          0
        </span>
      </a>
      <a href="/EasyBuy-x-PackIT/EasyBuy/frontend/login.php" class="nav-link text-center mobile-nav-link"
        data-nav="account" onclick="setActiveNav(event, 'account')" tabindex="0">
        <span class="material-symbols-rounded fs-1">
          account_circle
        </span>
      </a>
    </div>
  </div>

  <script>
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    function searchProduct() {
      const query = searchInput.value;
      window.location.href = '/EasyBuy-x-PackIT/EasyBuy/frontend/search.php?q=' + encodeURIComponent(query || '');
    }

    searchInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        searchProduct();
      }
    });

    function updateCartCount() {
      fetch('/EasyBuy-x-PackIT/EasyBuy/api/getCartCount.php')
        .then(response => response.json())
        .then(data => {
          const count = data.count || 0;
          const cartBadge = document.getElementById('cartBadge');
          const cartBadgeMobile = document.getElementById('cartBadgeMobile');

          if (count > 0) {
            cartBadge.textContent = count;
            cartBadge.style.display = 'inline-block';
            cartBadgeMobile.textContent = count;
            cartBadgeMobile.style.display = 'inline-block';
          } else {
            cartBadge.style.display = 'none';
            cartBadgeMobile.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Error fetching cart count:', error);
        });
    }

    updateCartCount();

    setInterval(updateCartCount, 1000);

    window.updateCartCount = updateCartCount;
    window.updateCartCount();

    function setActiveNav(event, navItem) {
      localStorage.setItem('activeNav', navItem);

      const allNavLinks = document.querySelectorAll('.mobile-nav-link');
      allNavLinks.forEach(link => {
        link.classList.remove('active');
      });

      event.currentTarget.classList.add('active');
    }

    function initActiveNav() {
      const currentPage = window.location.pathname;
      const currentHash = window.location.hash;
      let activeNav = localStorage.getItem('activeNav') || 'home';

      if (currentPage.includes('cart.php')) {
        activeNav = 'cart';
      } else if (currentPage.includes('login.php') || currentPage.includes('account.php')) {
        activeNav = 'account';
      } else if (currentHash === '#sale') {
        activeNav = 'sale';
      } else if (currentHash === '#categories') {
        activeNav = 'categories';
      } else if (currentPage.includes('index.php') || currentPage === '/') {
        activeNav = 'home';
      }

      const allNavLinks = document.querySelectorAll('.mobile-nav-link');
      allNavLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-nav') === activeNav) {
          link.classList.add('active');
        }
      });

      localStorage.setItem('activeNav', activeNav);
    }

    document.addEventListener('DOMContentLoaded', initActiveNav);
    window.addEventListener('hashchange', initActiveNav);
    document.querySelectorAll('.mobile-nav-link').forEach(link => {
      link.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.click();
        }
      });
    });
  </script>
</body>

</html>