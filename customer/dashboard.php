<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only allow customers to access this page
checkRole(['customer']);

$pageTitle = "Customer Dashboard - Appliance Repair System";
require_once '../includes/header.php';

// Get customer's repair requests
$customer_id = $_SESSION['user_id'];
$sql = "SELECT rr.*, a.name as appliance_name, a.type as appliance_type 
        FROM repair_requests rr
        JOIN appliances a ON rr.appliance_id = a.appliance_id
        WHERE rr.user_id = ?
        ORDER BY rr.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Customer Dashboard</h1>
        <a href="request.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New Repair Request</a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Requests</h3>
            <p class="text-3xl font-bold text-blue-600">
                <?php echo count($requests); ?>
            </p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
            <p class="text-3xl font-bold text-yellow-500">
                <?php echo count(array_filter($requests, function($r) { return $r['status'] === 'pending'; })); ?>
            </p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Completed Requests</h3>
            <p class="text-3xl font-bold text-green-500">
                <?php echo count(array_filter($requests, function($r) { return $r['status'] === 'completed'; })); ?>
            </p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Repair Requests</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appliance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($request['appliance_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($request['appliance_type']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo getStatusColorClass($request['status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="request-details.php?id=<?php echo $request['request_id']; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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