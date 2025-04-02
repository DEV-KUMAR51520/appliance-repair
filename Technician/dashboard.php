<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only allow technicians to access this page
checkRole(['technician']);

$technician_id = $_SESSION['user_id'];
$pageTitle = "Technician Dashboard - Appliance Repair System";
require_once '../includes/header.php';

// Get assigned jobs
$sql = "SELECT a.assignment_id, rr.request_id, rr.issue_description, rr.priority, 
               a.status, a.assigned_at, a.completed_at, 
               c.full_name as customer_name, c.phone as customer_phone,
               app.name as appliance_name, app.type as appliance_type
        FROM assignments a
        JOIN repair_requests rr ON a.request_id = rr.request_id
        JOIN users c ON rr.user_id = c.user_id
        JOIN appliances app ON rr.appliance_id = app.appliance_id
        WHERE a.technician_id = ?
        ORDER BY 
            CASE 
                WHEN a.status = 'pending' THEN 1
                WHEN a.status = 'accepted' AND rr.priority = 'high' THEN 2
                WHEN a.status = 'accepted' AND rr.priority = 'medium' THEN 3
                WHEN a.status = 'accepted' AND rr.priority = 'low' THEN 4
                ELSE 5
            END,
            a.assigned_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $technician_id);
$stmt->execute();
$result = $stmt->get_result();
$assignments = $result->fetch_all(MYSQLI_ASSOC);

// Debugging (Uncomment to check fetched data)
// var_dump($assignments); die();

// Count assignments by status
$stats = [
    'pending' => 0,
    'accepted' => 0,
    'completed' => 0,
    'total' => count($assignments)
];

foreach ($assignments as $assignment) {
    $status = strtolower(trim($assignment['status'])); // Normalize status value
    if ($status === 'pending') $stats['pending']++;
    if ($status === 'accepted') $stats['accepted']++;
    if ($status === 'completed') $stats['completed']++;
}
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Technician Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Assignments</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total']; ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Pending Acceptance</h3>
            <p class="text-3xl font-bold text-yellow-500"><?php echo $stats['pending']; ?></p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Active Jobs</h3>
            <p class="text-3xl font-bold text-green-500"><?php echo $stats['accepted']; ?></p>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Your Assignments</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appliance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium"><?php echo htmlspecialchars($assignment['customer_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($assignment['customer_phone']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium"><?php echo htmlspecialchars($assignment['appliance_name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($assignment['appliance_type']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate"><?php echo htmlspecialchars($assignment['issue_description']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo getPriorityColorClass($assignment['priority']); ?>">
                                    <?php echo ucfirst($assignment['priority']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo getStatusColorClass($assignment['status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $assignment['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($assignment['assigned_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                if (!empty($assignment['completed_at'])) {
                                    echo date('M d, Y', strtotime($assignment['completed_at']));
                                } else {
                                    echo "Not completed";
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="job-details.php?id=<?php echo $assignment['assignment_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
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
    switch (strtolower(trim($status))) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'accepted': return 'bg-blue-100 text-blue-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'declined': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getPriorityColorClass($priority) {
    switch (strtolower(trim($priority))) {
        case 'high': return 'bg-red-100 text-red-800';
        case 'medium': return 'bg-yellow-100 text-yellow-800';
        case 'low': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

require_once '../includes/footer.php'; 
?>
