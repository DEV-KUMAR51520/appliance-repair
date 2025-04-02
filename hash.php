<?php
require_once 'includes/db.php'; // Ensure your database connection is correct

// Get all users with unencrypted (plain-text) passwords
$sql = "SELECT user_id, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($user = $result->fetch_assoc()) {
        $user_id = $user['user_id'];
        $plain_password = $user['password'];

        // Check if the password is already hashed (assume it's not hashed if it's short)
        if (password_needs_rehash($plain_password, PASSWORD_DEFAULT)) {
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
    echo "All passwords have been hashed successfully!";
} else {
    echo "No users found!";
}

$conn->close();
?>
