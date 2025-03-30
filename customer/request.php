<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Only allow customers to access this page
checkRole(['customer']);

$customer_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appliance_type = $_POST['appliance_type'] ?? '';
    $issue_description = trim($_POST['issue_description'] ?? '');
    $urgency = $_POST['urgency'] ?? 'medium';

    // Validate input
    if (empty($appliance_type) || empty($issue_description)) {
        $error = 'Please fill in all required fields';
    } else {
        // Insert new repair request
        $sql = "INSERT INTO repair_requests (user_id, appliance_type, issue_description, priority, status, created_at) 
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $customer_id, $appliance_type, $issue_description, $urgency);

        if ($stmt->execute()) {
            $success = 'Repair request submitted successfully!';
            $_POST = []; // Reset form
        } else {
            $error = 'Failed to submit repair request. Please try again.';
        }
    }
}

$pageTitle = "New Repair Request - Appliance Repair System";
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">New Repair Request</h1>
        <p class="text-gray-600">Fill out the form below to request appliance repair service</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
        <div class="mt-4">
            <a href="dashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a> or 
            <a href="request.php" class="text-blue-600 hover:underline">Submit Another Request</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <!-- Appliance Type Dropdown -->
                <div class="mb-6">
                    <label for="appliance_type" class="block text-gray-700 font-medium mb-2">Select Appliance Type*</label>
                    <select id="appliance_type" name="appliance_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Appliance Type --</option>
                        <option value="Refrigerator">Refrigerator</option>
                        <option value="Washing Machine">Washing Machine</option>
                        <option value="Air Conditioner">Air Conditioner</option>
                        <option value="Microwave">Microwave</option>
                        <option value="Television">Television</option>
                    </select>
                </div>

                <!-- Issue Description -->
                <div class="mb-6">
                    <label for="issue_description" class="block text-gray-700 font-medium mb-2">Issue Description*</label>
                    <textarea id="issue_description" name="issue_description" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo $_POST['issue_description'] ?? ''; ?></textarea>
                </div>

                <!-- Urgency Level -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Urgency*</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="urgency" value="low" 
                                   <?php if (($_POST['urgency'] ?? 'medium') === 'low') echo 'checked'; ?> 
                                   class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Low</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="urgency" value="medium" 
                                   <?php if (($_POST['urgency'] ?? 'medium') === 'medium') echo 'checked'; ?> 
                                   class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Medium</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="urgency" value="high" 
                                   <?php if (($_POST['urgency'] ?? 'medium') === 'high') echo 'checked'; ?> 
                                   class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">High</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <a href="dashboard.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-3 hover:bg-gray-400">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Submit Request</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
