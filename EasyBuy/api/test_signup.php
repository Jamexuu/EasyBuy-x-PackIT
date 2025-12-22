<?php
include 'User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $userData = [
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'contactNumber' => $_POST['contactNumber']
    ];

    $addressData = [
        'houseNumber' => $_POST['houseNumber'],
        'street' => $_POST['street'],
        'lot' => $_POST['lot'],
        'block' => $_POST['block'],
        'barangay' => $_POST['barangay'],
        'city' => $_POST['city'],
        'province' => $_POST['province'],
        'postalCode' => $_POST['postalCode']
    ];

    $user = new User();
    $user->register($userData, $addressData);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name="firstName" placeholder="First Name">
        <br>
        <input type="text" name="lastName" placeholder="Last Name">
        <br>
        <input type="email" name="email" placeholder="Email">
        <br>
        <input type="password" name="password" placeholder="Password">
        <br>
        <input type="text" name="contactNumber" placeholder="Contact Number">
        <br>

        <input type="text" name="houseNumber" placeholder="House Number">
        <br>
        <input type="text" name="street" placeholder="Street">
        <br>
        <input type="text" name="lot" placeholder="Lot">
        <br>
        <input type="text" name="block" placeholder="Block">
        <br>
        <input type="text" name="barangay" placeholder="Barangay">
        <br>
        <input type="text" name="city" placeholder="City">
        <br>
        <input type="text" name="province" placeholder="Province">
        <br>
        <input type="text" name="postalCode" placeholder="Postal Code">
        <br>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>