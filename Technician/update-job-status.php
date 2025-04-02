<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

checkRole(['technician']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?error=Invalid request");
    exit();
}

$assignment_id = intval($_GET['id']);

// Get the current status of the job
$sql = "SELECT status FROM assignments WHERE assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    header("Location: dashboard.php?error=Job not found");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_status = $_POST['status'];
    $allowed_statuses = ['accepted', 'in_progress', 'completed', 'on_hold'];

    if (!in_array($new_status, $allowed_statuses)) {
        header("Location: update-job-status.php?id=$assignment_id&error=Invalid status");
        exit();
    }

    $sql = "UPDATE assignments SET status = ? WHERE assignment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $assignment_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Job Updated");
        exit();
    } else {
        header("Location: update-job-status.php?id=$assignment_id&error=Error updating job status");
        exit();
    }
}
?>

<h1>Update Job Status</h1>
<form method="POST">
    <label>Status:</label>
    <select name="status">
        <option value="accepted" <?php echo $job['status'] === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
        <option value="in_progress" <?php echo $job['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
        <option value="completed" <?php echo $job['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
        <option value="on_hold" <?php echo $job['status'] === 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
    </select>
    <button type="submit">Update Status</button>
</form>
