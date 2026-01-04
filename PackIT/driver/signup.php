<?php
session_start();

require_once __DIR__ . '/../api/classes/Database.php';
require_once __DIR__ . '/../api/gmail/sendMail.php';

$db = new Database();
$error = "";

/**
 * Defaults (so variables exist on GET too)
 * This fixes: "Warning: Undefined variable $vehicle_type"
 */
$first_name = $last_name = $email = $contact = '';
$house_num = $street = $province = $city = $barangay = '';
$vehicle_type = '';
$license_plate = '';

// Allowed values to prevent blank/invalid inserts
$allowedVehicleTypes = ['Motor', 'Tricycle', 'Sedan', 'Pick-Up', 'Closed Van', 'Forward Truck'];

// Already logged in
if (isset($_SESSION['driver_id'])) {
    header("Location: driver.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contact    = trim($_POST['contact'] ?? '');

    $house_num  = trim($_POST['house_num'] ?? '');
    $street     = trim($_POST['street'] ?? '');
    $province   = trim($_POST['province'] ?? '');
    $city       = trim($_POST['city'] ?? '');
    $barangay   = trim($_POST['barangay'] ?? '');

    $vehicle_type  = trim($_POST['vehicle_type'] ?? '');
    $license_plate = trim($_POST['license_plate'] ?? '');
    $password      = (string)($_POST['password'] ?? '');

    // Strict validate vehicle type
    if (!in_array($vehicle_type, $allowedVehicleTypes, true)) {
        $vehicle_type = '';
    }

    // Validation
    if (
        $first_name === '' || $last_name === '' || $email === '' || $contact === '' ||
        $house_num === '' || $street === '' || $province === '' || $city === '' || $barangay === '' ||
        $vehicle_type === '' || $license_plate === '' || $password === ''
    ) {
        $error = "Please fill in all required fields (including vehicle type).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check duplicate email
        $stmt = $db->executeQuery("SELECT id FROM drivers WHERE email = ? LIMIT 1", [$email]);
        $rows = $db->fetch($stmt);

        if (!empty($rows)) {
            $error = "Email is already registered as a driver.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert into drivers
            $db->executeQuery(
                "INSERT INTO drivers
                    (first_name, last_name, email, password, contact_number, house_number, street, province, city, barangay, vehicle_type, license_plate, is_available, created_at)
                 VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())",
                [
                    $first_name,
                    $last_name,
                    $email,
                    $hash,
                    $contact,
                    $house_num,
                    $street,
                    $province,
                    $city,
                    $barangay,
                    $vehicle_type,
                    $license_plate
                ]
            );

            $driver_id = $db->lastInsertId();

            // Send welcome email (Driver)
            try {
                $subject = "Welcome to PackIT - Driver Partner Account Created";

                $html = '
                    <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #111;">
                        <h2 style="margin:0 0 10px;">Welcome to PackIT, ' . htmlspecialchars($first_name) . '!</h2>
                        <p>We’re excited to have you as a <strong>Driver Partner</strong>.</p>

                        <div style="background:#fce354; padding:14px; border-radius:12px; margin:14px 0;">
                            <p style="margin:0;"><strong>Vehicle Type:</strong> ' . htmlspecialchars($vehicle_type) . '</p>
                            <p style="margin:0;"><strong>License Plate:</strong> ' . htmlspecialchars($license_plate) . '</p>
                        </div>

                        <p>You can now log in to your Driver Portal and start accepting deliveries.</p>
                        <p style="margin:0;"><strong>Driver Login:</strong>
                            <a href="http://localhost/EasyBuy-x-PackIT/PackIT/driver/login.php">Open Driver Portal</a>
                        </p>

                        <hr style="border:none; border-top:1px solid #eee; margin:16px 0;">
                        <p style="font-size:12px; color:#555; margin:0;">
                            If you did not create this account, please ignore this email.
                        </p>
                    </div>
                ';

                sendMail($email, $subject, $html);
            } catch (Exception $e) {
                // don't block signup if email fails
            }

            // Auto-login
            $_SESSION['driver_id'] = (int)$driver_id;
            header("Location: driver.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Sign Up - PackIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-zinc-900 min-h-screen flex items-center justify-center p-6">

    <div class="bg-yellow-50 w-full max-w-lg rounded-[2.5rem] border-[10px] border-yellow-400 p-8 shadow-2xl">
        <div class="text-center mb-6">
            <i class="fas fa-id-card text-yellow-500 text-4xl mb-2"></i>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Driver Registration</h2>
            <p class="text-gray-600 text-sm">Join the PackIT delivery team</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-700 text-sm font-bold">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="space-y-3">
                <h3 class="font-bold text-gray-700 border-b border-yellow-200 pb-1">Personal Details</h3>

                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="first_name" placeholder="First Name" required
                        value="<?php echo htmlspecialchars($first_name); ?>"
                        class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                    <input type="text" name="last_name" placeholder="Last Name" required
                        value="<?php echo htmlspecialchars($last_name); ?>"
                        class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="email" name="email" placeholder="Email Address" required
                        value="<?php echo htmlspecialchars($email); ?>"
                        class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                    <input type="text" name="contact" placeholder="Contact Number" required
                        value="<?php echo htmlspecialchars($contact); ?>"
                        class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="font-bold text-gray-700 border-b border-yellow-200 pb-1">Address Information</h3>

                <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="house_num" placeholder="House #" required
                        value="<?php echo htmlspecialchars($house_num); ?>"
                        class="p-3 rounded-xl shadow-inner bg-white outline-none">
                    <input type="text" name="street" placeholder="Street" required
                        value="<?php echo htmlspecialchars($street); ?>"
                        class="p-3 rounded-xl shadow-inner bg-white outline-none">
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <input type="text" name="province" placeholder="Province" required
                        value="<?php echo htmlspecialchars($province); ?>"
                        class="p-2 rounded-xl shadow-inner bg-white outline-none text-sm">
                    <input type="text" name="city" placeholder="City" required
                        value="<?php echo htmlspecialchars($city); ?>"
                        class="p-2 rounded-xl shadow-inner bg-white outline-none text-sm">
                    <input type="text" name="barangay" placeholder="Barangay" required
                        value="<?php echo htmlspecialchars($barangay); ?>"
                        class="p-2 rounded-xl shadow-inner bg-white outline-none text-sm">
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="font-bold text-gray-700 border-b border-yellow-200 pb-1">Vehicle & Security</h3>

                <select name="vehicle_type" required class="w-full p-3 rounded-xl shadow-inner bg-white outline-none appearance-none">
                    <option value="" disabled <?php echo ($vehicle_type === '') ? 'selected' : ''; ?>>
                        Select Type of Vehicle
                    </option>

                    <option value="Motorcycle" <?php echo ($vehicle_type === 'Motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                    <option value="Tricycle" <?php echo ($vehicle_type === 'Tricycle') ? 'selected' : ''; ?>>Tricycle</option>
                    <option value="Sedan" <?php echo ($vehicle_type === 'Sedan') ? 'selected' : ''; ?>>Sedan</option>
                    <option value="Pick-up Truck" <?php echo ($vehicle_type === 'Pick-up Truck') ? 'selected' : ''; ?>>Pick-up Truck</option>
                    <option value="Closed Van" <?php echo ($vehicle_type === 'Closed Van') ? 'selected' : ''; ?>>Closed Van</option>
                    <option value="Forward Truck" <?php echo ($vehicle_type === 'Forward Truck') ? 'selected' : ''; ?>>Forward Truck</option>
                </select>

                <input type="text" name="license_plate" placeholder="License Plate" required
                    value="<?php echo htmlspecialchars($license_plate); ?>"
                    class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">

                <input type="password" name="password" placeholder="Create Password" required
                    class="w-full p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
            </div>

            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-black py-4 rounded-2xl shadow-lg transition-transform active:scale-95 uppercase mt-4">
                Create Driver Account
            </button>

            <p class="text-center text-sm text-gray-600">
                Already have an account? <a href="login.php" class="font-bold text-yellow-600 underline">Login here</a>
            </p>
        </form>
    </div>

</body>

</html>