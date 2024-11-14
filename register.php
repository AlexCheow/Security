<?php
session_start();
include 'connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Set default role as 'staff'
    $role = 'staff';

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert the new user into the database
        $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        // Use hashed password instead of plain text
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        
        if ($stmt->execute()) {
            // Redirect to login page on successful registration
            header("Location: index.php");
            exit();
        } else {
            // Store error message in session if there's an issue with the insertion
            $_SESSION['error'] = "Error registering user: " . $conn->error;
            header("Location: register.php");
            exit();
        }
    }
}

// Close the database connection
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <title>User Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="img js-fullheight" style="background-image: url(images/bg.jpg);">
<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <h1 class="heading-section">Register</h1>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<div style='color:red;'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                    unset($_SESSION['error']);
                }
                ?>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <form action="" method="POST" class="signin-form" onsubmit="return validateForm()">
                        <div class="form-group">
                            <input type="text" name="username" value="" class="form-control" placeholder="Username" value="" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <input id="password" type="password" name="password" value="" class="form-control" placeholder="Password" value="" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <input id="confirm_password" type="password" name="confirm_password" value="" class="form-control" placeholder="Confirm Password" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="privacy_checkbox"> I agree to the <a href="privacy_policy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary submit px-3">Register</button>
                        </div>
                        <div class="form-group">
                            <a href="index.php" class="form-control btn btn-secondary submit px-3">Back to Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script>
function validateForm() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const privacyCheckbox = document.getElementById("privacy_checkbox");

    const passwordStrengthRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordStrengthRegex.test(password)) {
        alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        return false;
    }

    if (!privacyCheckbox.checked) {
        alert("Please agree to the Privacy Policy before proceeding.");
        return false;
    }

    return true;
}
</script>
</body>
</html>
