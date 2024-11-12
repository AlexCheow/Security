<?php
session_start();
include 'connection.php'; // Database connection

// Check if the user is authenticated and has the appropriate role (e.g., admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: unauthorized.php");
    exit();
}

// Check if user_id is provided in the URL and request method is POST
if (isset($_GET['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_GET['user_id']); // Cast to int to prevent SQL injection

    // Retrieve and sanitize input
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES, 'UTF-8');

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update the staff information in the database
    $update_query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssi", $username, $hashed_password, $role, $user_id);

    if ($update_stmt->execute()) {
        // Log the action for auditing purposes
        $logged_user_id = $_SESSION['user_id'];
        $logged_role = $_SESSION['role'];
        $action = "Updated Staff: " . $username;

        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("iss", $logged_user_id, $logged_role, $action);
        $log_stmt->execute();
        $log_stmt->close();

        $_SESSION['message'] = "Staff updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating staff: " . $conn->error;
    }

    // Redirect back to view_staff.php
    header("Location: view_staff.php");
    exit();
} else {
    // Redirect with error if user_id is not provided or request is not POST
    $_SESSION['error'] = "Invalid request.";
    header("Location: view_staff.php");
    exit();
}

// Close the database connection
$conn->close();
?>
