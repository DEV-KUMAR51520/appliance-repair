<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only allow admins to access this page
checkRole(['admin']);

$pageTitle = "Admin Dashboard - Appliance Repair System";
require_once '../includes/header.php';

// Get statistics
$stats = [];
$queries = [
    'total_requests' => "SELECT COUNT(*) as count FROM repair_requests",
    'pending_requests' => "SELECT COUNT(*) as count FROM repair_requests WHERE status = 'pending'",
    'in_progress_requests' => "SELECT COUNT(*) as count FROM repair_requests WHERE status = 'in_progress'",
    'completed_requests' => "SELECT COUNT(*) as count FROM repair_requests WHERE status = 'completed'",
    'total_customers' => "SELECT COUNT(*) as count FROM users WHERE role = 'customer'",
    'total_technicians' => "SELECT COUNT(*) as count FROM users WHERE role = 'technician'",
    'recent_requests' => "SELECT rr.*, u.full_name as customer_name 
                          FROM repair_requests rr
                          JOIN users u ON rr.customer_id = u.user_id
                          ORDER BY rr.created_at DESC LIMIT 5"
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    if ($key === 'recent_requests') {
        $stats[$key] = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $stats[$key] = $result->fetch_assoc()['count'];
    }
}
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Requests</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_requests']; ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
            <p class="text-3xl font-bold text-yellow-500"><?php echo $stats['pending_requests']; ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">In Progress</h3>
            <p class="text-3xl font-bold text-purple-500"><?php echo $stats['in_progress_requests']; ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Completed</h3>
            <p class="text-3xl font-bold text-green-500"><?php echo $stats['completed_requests']; ?></p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2">
            <h2 class="text-xl font-semibold mb-4">Recent Requests</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($stats['recent_requests'] as $request): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($request['customer_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo getStatusColorClass($request['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="../admin/request-details.php?id=<?php echo $request['request_id']; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Quick Stats</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium">Customers</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $stats['total_customers']; ?></p>
                </div>
                <div>
                    <h3 class="text-lg font-medium">Technicians</h3>
                    <p class="text-2xl font-bold text-green-600"><?php echo $stats['total_technicians']; ?></p>
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-lg font-medium mb-2">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="technicians.php" class="block bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded">Manage Technicians</a>
                    <a href="appointments.php" class="block bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded">View All Appointments</a>
                    <a href="add-technician.php" class="block bg-blue-100 hover:bg-blue-200 px-4 py-2 rounded text-blue-800">Add New Technician</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Requests Overview</h2>
        <canvas id="requestsChart" height="100"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Requests Chart
    const ctx = document.getElementById('requestsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                label: 'Repair Requests',
                data: [
                    <?php echo $stats['pending_requests']; ?>,
                    <?php echo $stats['in_progress_requests']; ?>,
                    <?php echo $stats['completed_requests']; ?>
                ],
                backgroundColor: [
                    'rgba(234, 179, 8, 0.7)',
                    'rgba(168, 85, 247, 0.7)',
                    'rgba(16, 185, 129, 0.7)'
                ],
                borderColor: [
                    'rgba(234, 179, 8, 1)',
                    'rgba(168, 85, 247, 1)',
                    'rgba(16, 185, 129, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>

<?php 
function getStatusColorClass($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'assigned': return 'bg-blue-100 text-blue-800';
        case 'in_progress': return 'bg-purple-100 text-purple-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

require_once '../includes/footer.php'; 
?>