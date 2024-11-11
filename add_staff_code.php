<?php
session_start();
include 'connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $role = $_POST['role'];

    // Check if the password and confirm password match
    if ($password !== $confirmpassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: add_staff.php");
        exit();
    }

    // Prepare and execute the SQL query to insert the product
    $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        // Redirect to view_products.php after successful addition
        header("Location: view_staff.php");
        exit();
    } else {
        // Store error message in session if there's an issue with the insertion
        $_SESSION['error'] = "Error adding staff: " . $conn->error;
        header("Location: view_staff.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>
