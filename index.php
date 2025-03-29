<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pageTitle = "Home - Appliance Repair System";
require_once 'includes/header.php';
?>

<div class="hero bg-blue-500 text-white py-16 rounded-lg mb-8">
    <div class="container mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Professional Appliance Repair Services</h1>
        <p class="text-xl mb-8">Fast, reliable, and affordable repair services for all your home appliances</p>
        <?php if (!isLoggedIn()): ?>
            <a href="/register.php" class="bg-white text-blue-600 font-bold px-6 py-3 rounded-lg hover:bg-gray-100 inline-block">Get Started</a>
        <?php else: ?>
            <?php if ($_SESSION['role'] === 'customer'): ?>
                <a href="/customer/request.php" class="bg-white text-blue-600 font-bold px-6 py-3 rounded-lg hover:bg-gray-100 inline-block">Request a Repair</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-blue-500 mb-4">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Easy Booking</h3>
        <p>Schedule your appliance repair service in just a few clicks. Our system makes it simple and convenient.</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-blue-500 mb-4">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Fast Response</h3>
        <p>Our technicians respond quickly to service requests, minimizing your appliance downtime.</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="text-blue-500 mb-4">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold mb-2">Quality Service</h3>
        <p>Certified technicians with years of experience ensure your appliances are repaired to the highest standard.</p>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mb-12">
    <h2 class="text-2xl font-bold mb-6">Our Services</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
            <h3 class="font-bold mb-2">Refrigerator Repair</h3>
            <p class="text-gray-600">Diagnosis and repair of cooling issues, leaks, and other refrigerator problems.</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
            <h3 class="font-bold mb-2">Washing Machine Repair</h3>
            <p class="text-gray-600">Fixing spin cycle issues, leaks, drainage problems, and more.</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
            <h3 class="font-bold mb-2">Oven & Stove Repair</h3>
            <p class="text-gray-600">Repairing heating elements, igniters, temperature controls, and other issues.</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow">
            <h3 class="font-bold mb-2">Dishwasher Repair</h3>
            <p class="text-gray-600">Fixing drainage problems, leaks, and cleaning performance issues.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>