<?php
session_start();
include("connection.php"); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Debugging: Print entered username and password
    echo "Entered Username: " . htmlspecialchars($username) . "<br>";
    echo "Entered Password: " . htmlspecialchars($password) . "<br>";

    // Prepare and execute the SQL statement to retrieve the user's hashed password
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debugging: Check if any result is returned
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Debugging: Display retrieved username and hashed password from the database
        echo "Retrieved Username: " . htmlspecialchars($user['username']) . "<br>";
        echo "Retrieved Hashed Password: " . htmlspecialchars($user['password']) . "<br>";

        // Verify the entered password with the hashed password in the database
        if (password_verify($password, $user['password'])) {

            //session_start() is used to start a session, and session_regenerate_id(true) is implemented for session fixation protection.
            session_regenerate_id(true);

            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } elseif ($user['role'] === 'staff') {
                header("Location: staff_dashboard.php");
            }
            exit();
        } else {
            // Invalid password
            $error = "Invalid username or password";
            echo $error; // Debugging: Display error
            exit();
        }
    } else {
        // Username not found
        $error = "Invalid username or password";
        echo $error; // Debugging: Display error
        exit();
    }
}

// Close the database connection
$conn->close();
?>
