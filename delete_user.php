<?php
session_start();
include 'connection.php'; // Database connection

// Check if product_id is set in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Prepare the delete statement
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['message'] = "User deleted successfully.";
    } else {
        // Set an error message in the session if deletion fails
        $_SESSION['error'] = "Error deleting user: " . $conn->error;
    }

    // Redirect to the products list page
    header("Location: view_staff.php");
    exit();
} else {
    // Redirect if product_id is not set
    $_SESSION['error'] = "No user ID provided.";
    header("Location: view_staff.php");
    exit();
}
?>
