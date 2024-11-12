<?php
session_start();
include 'connection.php'; // Database connection

// Verify that the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: view_products.php");
    exit();
}

// Check if product_id is provided in the URL and validate it
if (isset($_GET['product_id']) && filter_var($_GET['product_id'], FILTER_VALIDATE_INT)) {
    $product_id = (int)$_GET['product_id']; // Cast to int for added safety

    // Prepare and execute the delete statement
    $query = "DELETE FROM Products WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $product_id);

        // Execute the deletion and handle result
        if ($stmt->execute()) {
            // Log the action with details
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            $action = "Deleted Product ID: $product_id";

            $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
            if ($log_stmt = $conn->prepare($log_query)) {
                $log_stmt->bind_param("iss", $user_id, $role, $action);
                $log_stmt->execute();
                $log_stmt->close();
            }

            // Set a success message in the session
            $_SESSION['message'] = "Product deleted successfully.";
        } else {
            // Set an error message in the session if deletion fails
            $_SESSION['error'] = "Error deleting product: " . htmlspecialchars($conn->error);
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: Unable to prepare delete statement.";
    }

    // Redirect to the products list page
    header("Location: view_products.php");
    exit();
} else {
    // Redirect with error if product_id is invalid
    $_SESSION['error'] = "Invalid product ID provided.";
    header("Location: view_products.php");
    exit();
}

// Close the database connection
$conn->close();
?>
