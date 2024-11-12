<?php
session_start();
include 'connection.php'; // Database connection

// Check if the user is authenticated and authorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    // Redirect unauthorized users to an error or login page
    header("Location: unauthorized.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user inputs
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $price = floatval($_POST['price']);
    $stock = 0; // Default stock

    // Prepare and execute the SQL query to insert the product
    $query = "INSERT INTO Products (name, description, price, stock) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssdi", $name, $description, $price, $stock);
        if ($stmt->execute()) {
            // Log the action securely
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            $action = "Added Product : " . $name;


            $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
            if ($log_stmt = $conn->prepare($log_query)) {
                $log_stmt->bind_param("iss", $user_id, $role, $action);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $_SESSION['error'] = "Error logging action: " . $conn->error;
            }

            // Redirect to view_products.php after successful addition
            header("Location: view_products.php");
            exit();
        } else {
            // Store error message in session if there's an issue with the insertion
            $_SESSION['error'] = "Error adding product: " . $conn->error;
            header("Location: add_products.php");
            exit();
        }
        $stmt->close();
    
    }
}

// Close the database connection
$conn->close();
?>
