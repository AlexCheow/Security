<?php
session_start();
include 'connection.php'; // Database connection

// Check if user is logged in and has admin permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: index.php"); // Redirect to login if unauthorized
    exit();
}

// Check if user_id is set and is a valid integer
if (isset($_GET['user_id']) && filter_var($_GET['user_id'], FILTER_VALIDATE_INT)) {
    $user_id = $_GET['user_id'];

    // Prepare the delete statement
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        // Log the deletion action
        $logged_user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Deleted user ID: $user_id";

        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("iss", $logged_user_id, $role, $action);
        $log_stmt->execute();

        // Set a success message in the session
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        // Set an error message if deletion fails
        $_SESSION['error'] = "Error deleting user: " . $conn->error;
    }

    // Redirect to the staff list page
    header("Location: view_staff.php");
    exit();
} else {
    // Redirect if user_id is not set or is invalid
    $_SESSION['error'] = "Invalid user ID provided.";
    header("Location: view_staff.php");
    exit();
}

// Close the database connection
$conn->close();
?>
