<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .unauthorized-container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .unauthorized-container .icon {
            font-size: 4rem;
            color: #dc3545;
        }
        .unauthorized-container h1 {
            font-size: 1.75rem;
            margin-top: 1rem;
        }
        .unauthorized-container p {
            color: #6c757d;
            margin-top: 0.5rem;
        }
        .btn-back-home {
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="unauthorized-container">
    <i class="fa fa-exclamation-triangle icon"></i>
    <h1>Unauthorized Access</h1>
    <p>Sorry, you don't have permission to view this page.</p>
    <p>Please contact the administrator if you believe this is an error.</p>
    <a href="index.php" class="btn btn-primary btn-back-home"><i class="fa fa-home me-2"></i>Back to Login</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
