<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pageTitle = "Appointments";
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Appointments</h1>
    
    <p class="text-gray-600 mb-4">View and manage customer service appointments.</p>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b">Customer</th>
                    <th class="py-3 px-4 border-b">Appliance</th>
                    <th class="py-3 px-4 border-b">Status</th>
                    <th class="py-3 px-4 border-b">Date</th>
                    <th class="py-3 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3 px-4 border-b text-center" colspan="5">No appointments found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
