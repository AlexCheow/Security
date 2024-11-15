<?php
session_start();
include 'connection.php'; // Database connection

// Ensure user has authorization to delete adjustments
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: unauthorized.php");
    exit();
}

// Validate and sanitize the adjustment ID from the URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error'] = "Invalid adjustment ID.";
    header("Location: view_adjustment.php");
    exit();
}
$adjustment_id = (int) $_GET['id'];

// Start a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Get adjustment details to restore stock quantities before deletion
    $get_details_query = "SELECT product_id, quantity FROM StockAdjustmentDetails WHERE adjustment_id = ?";
    $get_details_stmt = $conn->prepare($get_details_query);
    $get_details_stmt->bind_param("i", $adjustment_id);
    $get_details_stmt->execute();
    $details_result = $get_details_stmt->get_result();

    // Loop through each product in the adjustment and restore its stock quantity
    while ($row = $details_result->fetch_assoc()) {
        $product_id = (int) $row['product_id'];
        $quantity = (int) $row['quantity'];

        // Decrease the quantity from the stock
        $update_stock_query = "UPDATE Products SET stock = stock - ? WHERE id = ?";
        $update_stock_stmt = $conn->prepare($update_stock_query);
        $update_stock_stmt->bind_param("ii", $quantity, $product_id);
        $update_stock_stmt->execute();
    }

    // Delete the adjustment details first
    $delete_details_query = "DELETE FROM StockAdjustmentDetails WHERE adjustment_id = ?";
    $delete_details_stmt = $conn->prepare($delete_details_query);
    $delete_details_stmt->bind_param("i", $adjustment_id);
    $delete_details_stmt->execute();

    // Delete the adjustment record
    $delete_adjustment_query = "DELETE FROM StockAdjustments WHERE id = ?";
    $delete_adjustment_stmt = $conn->prepare($delete_adjustment_query);
    $delete_adjustment_stmt->bind_param("i", $adjustment_id);
    $delete_adjustment_stmt->execute();

    // Commit the transaction
    $conn->commit();

    $_SESSION['message'] = "Stock adjustment deleted successfully and stock quantities adjusted.";
} catch (Exception $e) {
    // Rollback the transaction if there was an error
    $conn->rollback();
    $_SESSION['error'] = "Error deleting adjustment: " . $e->getMessage();
}

// Redirect back to the adjustments list page
header("Location: view_adjustment.php");
exit();
?>
