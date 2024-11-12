<?php
session_start();
include 'connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = 0; // Default stock

    // Prepare and execute the SQL query to insert the product
    $query = "INSERT INTO Products (name, description, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $name, $description, $price, $stock);

    if ($stmt->execute()) {
        // Log the Action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Added Product : $name";

        $sql = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $role, $action);
        $stmt->execute();

        // Redirect to view_products.php after successful addition
        header("Location: view_products.php");
        exit();
    } else {
        // Store error message in session if there's an issue with the insertion
        $_SESSION['error'] = "Error adding product: " . $conn->error;
        header("Location: add_products.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>
