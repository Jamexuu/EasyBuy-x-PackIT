<?php
    require '../api/classes/Auth.php';
    Auth::requireAuth();

    if (Auth::isAdmin()) {
        header("Location: ../admin/adminDashboard.php");
        exit();
    }
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account - Easy Buy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" rel="stylesheet">
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container">
        <div class="row m-3">
            <div class="col">
                <div class="h1 my-3">
                    Account
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center" id="accountContentField">
            
        </div>
    </div>
    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    
    <script>
        var accountContentField = document.getElementById('accountContentField');

        async function fetchUserData() {
            try {
                const response = await fetch('../api/getUserDetails.php');

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                console.log(data);

                accountContentField.innerHTML += `
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="card mb-5 rounded-4">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="h3 mb-3" style="color: #6EC064;">Profile</div>
                                </div>
                                <div class="card-text">
                                    <p>Last Name : `+ data.last_name +`</p>
                                    <p>First Name: `+ data.first_name +`</p>
                                    <p>Email: `+ data.email +`</p>
                                    <p>Contact Number: `+ data.contact_number +`</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5  rounded-4">
                            <div class="card-body">
                                <div class="card-title">
                                    <div class="h3 mb-3" style="color: #6EC064;">Address</div>
                                </div>
                                <div class="card-text">
                                    <p>House No : `+ data.house_number +`</p>
                                    <p>Barangay: `+ data.barangay +`</p>
                                    <p>Province: `+ data.province +`</p>
                                    <p>Street: `+ data.street +`</p>
                                    <p>City: `+ data.city +`</p>
                                    <p>Postal Code: `+ data.postal_code +`</p>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-5 rounded-4">
                            <div class="card-body">
                                <div class="card-title d-flex justify-content-between align-items-center">
                                    <div class="h3 mb-3" style="color: #6EC064;">Orders</div>
                                    <a href="orderHistory.php" class="text-decoration-none" style="color: #6EC064; font-size: 14px;">View order history ›</a>
                                </div>
                                <div class="card-text d-flex justify-content-center gap-5">
                                    <a href="userOrders.php?tab=to-ship" class="btn border-0 bg-transparent d-flex flex-column align-items-center text-decoration-none">
                                        <span class="material-symbols-rounded" style="font-size: 40px; color: #333;">package_2</span>
                                        <span style="font-size: 14px; color: #333;">To Ship</span>
                                    </a>
                                    <a href="userOrders.php?tab=to-receive" class="btn border-0 bg-transparent d-flex flex-column align-items-center text-decoration-none">
                                        <span class="material-symbols-rounded" style="font-size: 40px; color: #333;">local_shipping</span>
                                        <span style="font-size: 14px; color: #333;">To Receive</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mb-3">
                            <form action="../api/logout.php" method="POST">
                                <button type="submit" class="btn btn-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                `;

            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        }

        fetchUserData();
    </script>
</body>


</html>