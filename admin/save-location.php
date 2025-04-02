<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $user_id = $_SESSION['user_id']; // Assuming user session is stored

    if ($latitude && $longitude) {
        $stmt = $conn->prepare("UPDATE users SET latitude = ?, longitude = ? WHERE user_id = ?");
        $stmt->bind_param("ddi", $latitude, $longitude, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Location updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update location.";
        }
    }
}

header("Location: profile.php");
exit();
?>
