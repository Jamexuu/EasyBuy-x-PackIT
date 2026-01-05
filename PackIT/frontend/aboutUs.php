<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT - About Us</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .about-section {
            padding: 60px 0;
        }

        .about-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-text {
            color: #333;
            line-height: 1.6;
            font-size: 1.1rem;
            max-width: 90%;
        }

        .animated-gif {
            width: 100%;
            height: auto;
            display: block;
        }

        .forklift-gif {
            max-width: 350px;
            margin-top: 2rem;
        }

        .truck-col {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <?php include("components/navbar.php"); ?>

    <!-- ABOUT SECTION -->
    <section class="about-section">
        <div class="container-fluid">
            <div class="row align-items-center">

                <!-- LEFT CONTENT -->
                <div class="col-md-6 mb-5 mb-md-0">
                    <h2 class="about-title">About Us</h2>

                    <p class="about-text">
                        At <strong>PackIT</strong>, we make logistics simple, fast, and affordable.
                        As a Philippine-based nationwide delivery company, we specialize in getting
                        your packages to their destination quickly, safely, and at the lowest
                        possible cost.
                    </p>

                    <p class="about-text">
                        Whether you’re sending documents, parcels, or larger shipments,
                        PackIT is your reliable partner for hassle-free deliveries across
                        Luzon, Visayas, and Mindanao.
                    </p>

                    <img
                        src="../assets/gif2.gif"
                        alt="Warehouse Animation"
                        class="animated-gif forklift-gif"
                        style="max-width: 100%;">
                </div>

                <!-- RIGHT IMAGE -->
                <div class="col-md-6 truck-col">
                    <img
                        src="../assets/gif1.gif"
                        alt="PackIT Delivery Truck"
                        class="animated-gif">
                </div>

            </div>
        </div>
    </section>

    <!-- SERVICES / CAROUSEL -->
    <section class="about-section bg-light">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12 text-center mb-4">
                    <h2 class="about-title">Our Services</h2>
                </div>

                <div class="col-12">
                    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner-fluid">

                            <div class="carousel-item active">
                                <img src="../assets/carousel1.png" class="d-block w-100" alt="1">
                            </div>

                            <div class="carousel-item">
                                <img src="../assets/carousel2.png" class="d-block w-100" alt="2">
                            </div>

                            <div class="carousel-item">
                                <img src="../assets/carousel3.png" class="d-block w-100" alt="3">
                            </div>

                            <div class="carousel-item">
                                <img src="../assets/carousel4.png" class="d-block w-100" alt="4">
                            </div>

                            <div class="carousel-item">
                                <img src="../assets/carousel5.png" class="d-block w-100" alt="5">
                            </div>

                        </div>

                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>

                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

     <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>