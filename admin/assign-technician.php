<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only allow admin to access this page
checkRole(['admin']);

$success = '';
$error = '';

// Fetch all pending repair requests that need a technician
$sql_requests = "SELECT r.request_id, r.issue_description, u.full_name AS customer_name
                 FROM repair_requests r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.technician_id IS NULL";
$result_requests = $conn->query($sql_requests);

if (!$result_requests) {
    die("Error fetching repair requests: " . $conn->error);
}

$requests = $result_requests->fetch_all(MYSQLI_ASSOC);

// Fetch all available technicians
$sql_technicians = "SELECT user_id, full_name FROM users WHERE role = 'technician'";
$result_technicians = $conn->query($sql_technicians);

if (!$result_technicians) {
    die("Error fetching technicians: " . $conn->error);
}

$technicians = $result_technicians->fetch_all(MYSQLI_ASSOC);

// Handle form submission to assign a technician
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? '';
    $technician_id = $_POST['technician_id'] ?? '';

    if (empty($request_id) || empty($technician_id)) {
        $error = "Please select both a request and a technician.";
    } else {
        $sql_assign = "UPDATE repair_requests SET technician_id = ? WHERE request_id = ?";
        $stmt = $conn->prepare($sql_assign);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ii", $technician_id, $request_id);

        if ($stmt->execute()) {
            $success = "Technician assigned successfully!";
        } else {
            $error = "Failed to assign technician. Please try again.";
        }
    }
}

$pageTitle = "Assign Technician - Admin Panel";
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold">Assign Technician</h1>
    <p class="text-gray-600">Assign technicians to pending repair requests</p>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="assign-technician.php" method="POST" class="bg-white rounded-lg shadow-md p-6 mt-4">
        <div class="mb-4">
            <label for="request_id" class="block text-gray-700 font-medium mb-2">Select Repair Request*</label>
            <select id="request_id" name="request_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="">-- Select Request --</option>
                <?php foreach ($requests as $request): ?>
                    <option value="<?php echo $request['request_id']; ?>">
                        <?php echo htmlspecialchars($request['customer_name'] . ' - ' . $request['issue_description']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="technician_id" class="block text-gray-700 font-medium mb-2">Select Technician*</label>
            <select id="technician_id" name="technician_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="">-- Select Technician --</option>
                <?php foreach ($technicians as $technician): ?>
                    <option value="<?php echo $technician['user_id']; ?>">
                        <?php echo htmlspecialchars($technician['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Assign Technician
            </button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
