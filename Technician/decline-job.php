<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

checkRole(['technician']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php?error=Invalid request");
    exit();
}

$assignment_id = intval($_GET['id']);

// Check if the assignment exists and is not already completed
$sql = "SELECT status FROM assignments WHERE assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment = $result->fetch_assoc();

if (!$assignment || $assignment['status'] === 'completed') {
    header("Location: dashboard.php?error=Cannot decline a completed job");
    exit();
}

// Update the assignment status
$sql = "UPDATE assignments SET status = 'declined' WHERE assignment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $assignment_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?msg=Job Declined");
    exit();
} else {
    header("Location: dashboard.php?error=Error updating job status");
    exit();
}
?>
