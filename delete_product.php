<?php
session_start();
include 'connection.php'; // Database connection

// Check if product_id is set in the URL
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Prepare the delete statement
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
    } else {
        // Set an error message in the session if deletion fails
        $_SESSION['error'] = "Error deleting product: " . $conn->error;
    }

    // Redirect to the products list page
    header("Location: view_products.php");
    exit();
} else {
    // Redirect if product_id is not set
    $_SESSION['error'] = "No product ID provided.";
    header("Location: view_products.php");
    exit();
}
?>
