<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Appliance Repair System'; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {},
            },
            plugins: [],
        }
    </script>
    
    <!-- Additional CSS/JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold">ApplianceRepair</a>
            
            <div class="hidden md:flex space-x-6">
            <a href="index.php" class="hover:text-blue-200">Home</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'customer'): ?>
                        <a href="../customer/dashboard.php" class="hover:text-blue-200">Dashboard</a>
                        <a href="../customer/request.php" class="hover:text-blue-200">New Request</a>
                    <?php elseif ($_SESSION['role'] === 'technician'): ?>
                        <a href="../technician/dashboard.php" class="hover:text-blue-200">Dashboard</a>
                        <a href="../technician/jobs.php" class="hover:text-blue-200">My Jobs</a>
                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/dashboard.php" class="hover:text-blue-200">Dashboard</a>
                        <a href="../admin/appointments.php" class="hover:text-blue-200">Appointments</a>
                        <a href="../admin/technicians.php" class="hover:text-blue-200">Technicians</a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="/about.php" class="hover:text-blue-200">About</a>
                <a href="/contact.php" class="hover:text-blue-200">Contact</a>
            </div>
            
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="hidden md:inline"> Welcome, <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Guest'; ?></span>
                    <a href="/appliance-repair/logout.php" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg">Login</a>
                    <a href="register.php" class="bg-white text-blue-600 hover:bg-gray-100 px-4 py-2 rounded-lg">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container mx-auto px-4 py-6">