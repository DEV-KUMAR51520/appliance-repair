<?php
session_start();
include 'includes/db.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please enter both email and password!";
        header("Location: login.php");
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id, full_name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'technician') {
                header("Location: technician/dashboard.php");
            } else {
                header("Location: customer/dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password!";
        }
    } else {
        $_SESSION['error'] = "User not found!";
    }
    $stmt->close();
    $conn->close();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Appliance Repair System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>

        <!-- Display Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class='text-red-500 bg-red-100 p-2 rounded-md mb-4'><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full p-2 border rounded-md" required>

            <label for="password" class="block text-gray-700 mt-2">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded-md" required>

            <button type="submit" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-md">
                Login
            </button>
        </form>

        <p class="mt-4 text-center text-gray-600">
            Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a>
        </p>
    </div>
</body>
</html>
