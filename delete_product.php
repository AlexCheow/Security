<?php
session_start();
include 'connection.php'; // Database connection

// Verify that user is logged in and has the appropriate role (e.g., admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: view_products.php");
    exit();
}

// Check if product_id is provided in the URL and validate it
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id']; // Cast to int to prevent SQL injection

    // Prepare the delete statement securely
    $query = "DELETE FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        // Log the Action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Delete Product";
        $details = "Deleted Product ID: $product_id";

        $sql = "INSERT INTO logs (user_id, role, action, details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $role, $action, $details);
        $stmt->execute();

        // Set a success message in the session
        $_SESSION['message'] = "Product deleted successfully.";

        // Log the action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Deleted Product ID: $product_id";
        
        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("iss", $user_id, $role, $action);
        $log_stmt->execute();
    } else {
        // Set an error message in the session if deletion fails
        $_SESSION['error'] = "Error deleting product: " . htmlspecialchars($conn->error);
    }
    
    // Redirect to the products list page
    header("Location: view_products.php");
    exit();
} else {
    // Redirect with error if product_id is not valid
    $_SESSION['error'] = "Invalid product ID provided.";
    header("Location: view_products.php");
    exit();
}

// Close the database connection
$conn->close();
?>
