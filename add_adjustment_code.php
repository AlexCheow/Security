<?php
session_start();
include 'connection.php'; // Database connection

// Check if the user is authenticated and has a valid CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // Sanitize and validate input data
    $adjustment_date = filter_input(INPUT_POST, 'adjustment_date', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $products = $_POST['product'] ?? [];
    $quantities = $_POST['quantity'] ?? [];

    $adjustment_type = "Stock Adjustment"; // Fixed adjustment type

    if (empty($products) || empty($quantities)) {
        $_SESSION['error'] = "Please select at least one product and specify a quantity.";
        header("Location: add_adjustment.php");
        exit();
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into StockAdjustments table
        $query = "INSERT INTO StockAdjustments (adjustment_date, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Preparation of StockAdjustments query failed: " . $conn->error);
        }
        $stmt->bind_param("ss", $adjustment_date, $description);
        if (!$stmt->execute()) {
            throw new Exception("Execution of StockAdjustments insert query failed: " . $stmt->error);
        }
        $adjustment_id = $stmt->insert_id;

        // Prepare query for StockAdjustmentDetails insertion
        $detail_query = "INSERT INTO StockAdjustmentDetails (adjustment_id, product_id, adjustment_type, quantity) VALUES (?, ?, ?, ?)";
        $detail_stmt = $conn->prepare($detail_query);
        if (!$detail_stmt) {
            throw new Exception("Preparation of StockAdjustmentDetails query failed: " . $conn->error);
        }

        foreach ($products as $index => $product_id) {
            $product_id = (int)$product_id; // Sanitize product ID
            $quantity = (int)$quantities[$index]; // Sanitize quantity

            // Check current stock
            $stock_check_query = "SELECT stock FROM Products WHERE id = ?";
            $stock_check_stmt = $conn->prepare($stock_check_query);
            if (!$stock_check_stmt) {
                throw new Exception("Preparation of stock check query failed: " . $conn->error);
            }
            $stock_check_stmt->bind_param("i", $product_id);
            $stock_check_stmt->execute();
            $stock_result = $stock_check_stmt->get_result();
            $product = $stock_result->fetch_assoc();

            if ($product) {
                $current_stock = $product['stock'];
                $new_stock = $current_stock + $quantity;

                if ($new_stock < 0) {
                    throw new Exception("Adjustment would result in negative stock for product ID $product_id.");
                }

                $detail_stmt->bind_param("iisi", $adjustment_id, $product_id, $adjustment_type, $quantity);
                if (!$detail_stmt->execute()) {
                    throw new Exception("Execution of StockAdjustmentDetails insert query failed: " . $detail_stmt->error);
                }

                // Update stock in Products table
                $update_stock_query = "UPDATE Products SET stock = stock + ? WHERE id = ?";
                $update_stock_stmt = $conn->prepare($update_stock_query);
                if (!$update_stock_stmt) {
                    throw new Exception("Preparation of stock update query failed: " . $conn->error);
                }
                $update_stock_stmt->bind_param("ii", $quantity, $product_id);
                if (!$update_stock_stmt->execute()) {
                    throw new Exception("Execution of stock update query failed: " . $update_stock_stmt->error);
                }
            } else {
                throw new Exception("Product ID $product_id not found.");
            }
        }

        // Log the action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Added stock adjustment ID: ". $adjustment_id;


        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        if (!$log_stmt) {
            throw new Exception("Preparation of log query failed: " . $conn->error);
        }
        $log_stmt->bind_param("isss", $user_id, $role, $action);
        if (!$log_stmt->execute()) {
            throw new Exception("Execution of log query failed: " . $log_stmt->error);
        }

        $conn->commit();
        $_SESSION['message'] = "Stock adjustment added successfully.";
        header("Location: view_adjustment.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error adding adjustment: " . $e->getMessage();
        header("Location: add_adjustment.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: add_adjustment.php");
    exit();
}
?>