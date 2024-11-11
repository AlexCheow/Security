<?php
session_start();
include 'connection.php'; // Database connection

// Check if product_id is provided in the URL
if (isset($_GET['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_GET['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Update the staff information in the database
    $update_query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssi", $username, $password, $role, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Staff updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating staff: " . $conn->error;
    }

    // Redirect back to view_products.php
    header("Location: view_staff.php");
    exit();
} else {
    // Redirect with error if product_id is not provided or request is not POST
    $_SESSION['error'] = "Invalid request.";
    header("Location: view_staff.php");
    exit();
}
