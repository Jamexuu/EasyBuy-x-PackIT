<div class="container py-5  p-lg-5">
    <div class="row">
        <div class="col mb-4 d-flex justify-content-between pt-4">
            <div class="h1 fw-bold text-md-center text-lg-start " style="color:#6EC064">
                Categories
            </div>
            <a href="frontend/productListing.php"
                class="text-decoration-none text-muted d-flex align-items-center gap-1"
                style="font-size: clamp(0.7rem, 2vw, 1rem); line-height:1.1; font-weight:normal;">
                See all products
                <span class="material-symbols-outlined" style="font-size:1em;">
                    arrow_forward
                </span>
            </a>


        </div>
    </div>
    <div class="row g-3 g-md-4 justify-content-center" id="categoriesContainer"></div>
</div>
<script>
    var categories = [{
            name: "Produce",
            description: "Fresh fruits and vegetables",
            image: "1.webp"
        },
        {
            name: "Meats",
            description: "Fresh cuts of beef, pork, and poultry",
            image: "11.webp"
        },
        {
            name: "Dairy",
            description: "Milk, cheese, and dairy essentials",
            image: "21.webp"
        },
        {
            name: "Frozen Foods",
            description: "Frozen meals, vegetables, and desserts",
            image: "31.webp"
        },
        {
            name: "Condiments & Sauces",
            description: "Sauces, seasonings, and cooking essentials",
            image: "41.webp"
        },
        {
            name: "Snacks",
            description: "Chips, biscuits, and quick bites",
            image: "51.webp"
        },
        {
            name: "Beverages",
            description: "Juices, soft drinks, coffee, and tea",
            image: "61.webp"
        },
        {
            name: "Personal Care",
            description: "Hygiene and personal care products",
            image: "71.webp"
        }
    ];

    var container = document.getElementById("categoriesContainer");

    for (var i = 0; i < categories.length; i++) {
        container.innerHTML += `
            <div class="col-6 col-lg-3 mb-2">
                <a href="frontend/productListing.php?category=${categories[i].name.toLowerCase().replace(/ & /g, '-').replace(/ /g, '-')}" 
                class="card h-100 rounded-4 shadow-sm overflow-hidden text-decoration-none">
                    <img class="card-img-top p-3"
                    src="/EasyBuy-x-PackIT/EasyBuy/Product%20Images/all/${categories[i].image}"
                    style="height:160px;object-fit:contain;"
                    alt="${categories[i].name}">
                    <div class="card-body text-center d-flex flex-column p-2 p-sm-3"
                    style="background-color:#DCDCDC;border-radius:0 0 1rem 1rem;">
                    <h6 class="card-title fw-bold">
                        ${categories[i].name}
                    </h6>
                    <p class="card-title text-secondary" style="font-size:0.9em; font-weight:normal;">
                        ${categories[i].description}
                    </p>
                    </div>
                </a>
            </div>
        `;
    }
</script>