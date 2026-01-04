<?php
session_start();
require_once __DIR__ . '/../api/classes/Database.php';

$db = new Database();
$error = "";

if (isset($_SESSION['driver_id'])) {
    header("Location: driver.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = "Please enter email and password.";
    } else {
        $stmt = $db->executeQuery("SELECT id, password FROM drivers WHERE email = ? LIMIT 1", [$email]);
        $rows = $db->fetch($stmt);

        if (empty($rows)) {
            $error = "Invalid email or password.";
        } else {
            $driver = $rows[0];
            if (!password_verify($password, $driver['password'])) {
                $error = "Invalid email or password.";
            } else {
                $_SESSION['driver_id'] = (int)$driver['id'];
                header("Location: driver.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Login - PackIT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-zinc-900 min-h-screen flex items-center justify-center p-6">

    <div class="bg-yellow-50 w-full max-w-sm rounded-[2.5rem] border-[10px] border-yellow-400 p-10 shadow-2xl">

        <div class="text-center mb-8">
            <div class="inline-block bg-black p-3 rounded-2xl mb-4">
                <i class="fas fa-motorcycle text-yellow-400 text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Driver Login</h2>
            <p class="text-gray-600 text-sm">Welcome back, Partner!</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-700 text-sm font-bold">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase ml-2 mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" placeholder="driver@packit.com" required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="w-full pl-10 p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase ml-2 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full pl-10 p-3 rounded-xl border-none shadow-inner bg-white focus:ring-2 focus:ring-yellow-400 outline-none">
                </div>
            </div>

            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-black py-4 rounded-2xl shadow-lg transition-transform active:scale-95 uppercase mt-2">
               Log In
            </button>

            <div class="text-center pt-4 border-t border-yellow-200">
                <p class="text-sm text-gray-600">
                    Don't have a driver account? <br>
                    <a href="signup.php" class="font-bold text-yellow-600 underline">Register Now</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>