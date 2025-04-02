<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
checkRole(['admin']);

$pageTitle = "Manage Technicians";
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Manage Technicians</h1>
    
    <div class="flex justify-between items-center mb-4">
        <p class="text-gray-600">View and manage registered technicians.</p>
        <a href="add-technician.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Add Technician</a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b">Technician Name</th>
                    <th class="py-3 px-4 border-b">Email</th>
                    <th class="py-3 px-4 border-b">Phone</th>
                    <th class="py-3 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-3 px-4 border-b text-center" colspan="4">No technicians found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
