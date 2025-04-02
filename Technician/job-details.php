<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

checkRole(['technician']);

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$assignment_id = intval($_GET['id']);

$sql = "SELECT a.assignment_id, rr.request_id, rr.issue_description, rr.urgency, a.status as assignment_status,
               a.assigned_at, a.completed_at, c.full_name as customer_name, c.phone as customer_phone,
               app.name as appliance_name, app.type as appliance_type
        FROM assignments a
        JOIN repair_requests rr ON a.request_id = rr.request_id
        JOIN users c ON rr.customer_id = c.user_id
        JOIN appliances app ON rr.appliance_id = app.appliance_id
        WHERE a.assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Job not found");
}

$job = $result->fetch_assoc();
?>

<h1>Job Details</h1>
<p>Customer: <?php echo htmlspecialchars($job['customer_name']); ?></p>
<p>Phone: <?php echo htmlspecialchars($job['customer_phone']); ?></p>
<p>Appliance: <?php echo htmlspecialchars($job['appliance_name']); ?></p>
<p>Issue: <?php echo htmlspecialchars($job['issue_description']); ?></p>
<p>Status: <?php echo htmlspecialchars($job['assignment_status']); ?></p>

<a href="accept-job.php?id=<?php echo $assignment_id; ?>">Accept Job</a> |
<a href="decline-job.php?id=<?php echo $assignment_id; ?>">Decline Job</a> |
<a href="update-job-status.php?id=<?php echo $assignment_id; ?>">Update Status</a>
