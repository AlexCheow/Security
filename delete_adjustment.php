<?php
session_start();
include 'connection.php'; // Database connection

// Check if the user is authenticated, has admin privileges, and the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $adjustment_id = (int)$_POST['id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Retrieve and reverse the stock quantities for each product in the adjustment
        $retrieve_query = "SELECT product_id, quantity FROM StockAdjustmentDetails WHERE adjustment_id = ?";
        $retrieve_stmt = $conn->prepare($retrieve_query);
        if (!$retrieve_stmt) {
            throw new Exception("Preparation of retrieve query failed: " . $conn->error);
        }
        $retrieve_stmt->bind_param("i", $adjustment_id);
        $retrieve_stmt->execute();
        $result = $retrieve_stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];

            // Update the stock to reverse the adjustment
            $update_stock_query = "UPDATE Products SET stock = stock - ? WHERE id = ?";
            $update_stock_stmt = $conn->prepare($update_stock_query);
            if (!$update_stock_stmt) {
                throw new Exception("Preparation of stock update query failed: " . $conn->error);
            }
            $update_stock_stmt->bind_param("ii", $quantity, $product_id);
            if (!$update_stock_stmt->execute()) {
                throw new Exception("Execution of stock update query failed: " . $update_stock_stmt->error);
            }
        }

        // Delete from StockAdjustmentDetails table
        $detail_delete_query = "DELETE FROM StockAdjustmentDetails WHERE adjustment_id = ?";
        $detail_delete_stmt = $conn->prepare($detail_delete_query);
        if (!$detail_delete_stmt) {
            throw new Exception("Preparation of StockAdjustmentDetails delete query failed: " . $conn->error);
        }
        $detail_delete_stmt->bind_param("i", $adjustment_id);
        if (!$detail_delete_stmt->execute()) {
            throw new Exception("Execution of StockAdjustmentDetails delete query failed: " . $detail_delete_stmt->error);
        }

        // Delete from StockAdjustments table
        $adjustment_delete_query = "DELETE FROM StockAdjustments WHERE id = ?";
        $adjustment_delete_stmt = $conn->prepare($adjustment_delete_query);
        if (!$adjustment_delete_stmt) {
            throw new Exception("Preparation of StockAdjustments delete query failed: " . $conn->error);
        }
        $adjustment_delete_stmt->bind_param("i", $adjustment_id);
        if (!$adjustment_delete_stmt->execute()) {
            throw new Exception("Execution of StockAdjustments delete query failed: " . $adjustment_delete_stmt->error);
        }

        // Log the action
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $action = "Deleted stock adjustment ID: " . $adjustment_id;

        $log_query = "INSERT INTO logs (user_id, role, action) VALUES (?, ?, ?)";
        $log_stmt = $conn->prepare($log_query);
        if (!$log_stmt) {
            throw new Exception("Preparation of log query failed: " . $conn->error);
        }
        $log_stmt->bind_param("iss", $user_id, $role, $action);
        if (!$log_stmt->execute()) {
            throw new Exception("Execution of log query failed: " . $log_stmt->error);
        }

        // Commit the transaction
        $conn->commit();
        $_SESSION['message'] = "Stock adjustment deleted successfully.";
        header("Location: view_adjustment.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting adjustment: " . $e->getMessage();
        header("Location: view_adjustment.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Unauthorized access or invalid request.";
    header("Location: view_adjustment.php");
    exit();
}
?>
