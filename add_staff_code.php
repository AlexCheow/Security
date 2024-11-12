<?php
session_start();
include 'connection.php'; // Database connection


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: view_staff.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $role = $_POST['role'];

    // Check if the password and confirm password match
    if ($password !== $confirmpassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: add_staff.php");
        exit();
    }

    // Validate role to prevent any unauthorized roles
    $allowed_roles = ['Staff', 'Admin'];
    if (!in_array($role, $allowed_roles)) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: add_staff.php");
        exit();
    }

    // Hash the password for security before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL query to insert the staff member securely
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $_SESSION['error'] = "Error preparing query: " . $conn->error;
        header("Location: add_staff.php");
        exit();
    }
    
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        // Log the action
        $user_id = $_SESSION['user_id'];
        $action = "Added new staff member: $username";
        
        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        if ($log_stmt) {
            $log_stmt->bind_param("iss", $user_id, $_SESSION['role'], $action);
            $log_stmt->execute();
        }

        // Redirect to view_staff.php after successful addition
        $_SESSION['message'] = "Staff member added successfully.";
        header("Location: view_staff.php");
        exit();
    } else {
        // Store error message in session if there's an issue with the insertion
        $_SESSION['error'] = "Error adding staff: " . htmlspecialchars($conn->error);
        header("Location: add_staff.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>
