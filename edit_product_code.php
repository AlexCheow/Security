<?php
session_start();
include 'connection.php'; // Database connection

// Check if product_id is provided in the URL
if (isset($_GET['product_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_GET['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Prepare and execute the SQL query to update the product
    $update_query = "UPDATE Products SET name = ?, description = ?, price = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssdi", $name, $description, $price, $product_id);

    if ($update_stmt->execute()) {

        // Log the Action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Edit Product";
        $details = "Updated Product ID: $product_id";

        $sql = "INSERT INTO logs (user_id, role, action, details) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $role, $action, $details);
        $stmt->execute();

        $_SESSION['message'] = "Product updated successfully.";
    } else {
        //$_SESSION['error'] = "Error updating product: " . $conn->error;
    }

    // Redirect back to view_products.php
    header("Location: view_products.php");
    exit();
} else {
    // Redirect with error if product_id is not provided or request is not POST
    $_SESSION['error'] = "Invalid request.";
    header("Location: view_products.php");
    exit();
}
