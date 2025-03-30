<?php
require_once 'config.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to sanitize input data
function sanitizeInput($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect user based on role
function redirectBasedOnRole() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    switch ($_SESSION['role']) {
        case 'admin':
            redirect('admin/dashboard.php');
            break;
        case 'technician':
            redirect('technician/dashboard.php');
            break;
        case 'customer':
            redirect('customer/dashboard.php');
            break;
        default:
            redirect('index.php'); // Default fallback
    }
}

// Check user role and redirect if unauthorized
function checkRole($allowedRoles) {
    if (!isLoggedIn() || !in_array($_SESSION['role'], $allowedRoles)) {
        redirect('login.php');
    }
}

// Login function
function login($username, $password) {
    global $conn;
    
    $username = sanitizeInput($username);
    
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Login query failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }
    }
    return false;
}

// Register function
function register($username, $email, $password, $full_name, $role, $phone = null, $address = null) {
    global $conn;
    
    $username = sanitizeInput($username);
    $email = sanitizeInput($email);
    $full_name = sanitizeInput($full_name);
    $role = sanitizeInput($role);
    $phone = sanitizeInput($phone);
    $address = sanitizeInput($address);
    
    // Hash the password securely without sanitization
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Split full_name into first_name and last_name
    $name_parts = explode(" ", $full_name, 2);
    $first_name = $name_parts[0];  
    $last_name = isset($name_parts[1]) ? $name_parts[1] : "";  

    // Check if username or email already exists
    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    if (!$checkStmt) {
        error_log("User check query failed: " . $conn->error);
        return false;
    }

    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        return false; // Username or email already exists
    }
    $checkStmt->close();

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, role, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("User insert query failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ssssssss", $username, $email, $hashed_password, $first_name, $last_name, $role, $phone, $address);

    return $stmt->execute();
}
?>
