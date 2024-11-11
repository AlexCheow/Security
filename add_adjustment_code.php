<?php
session_start();
include 'connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adjustment_date = $_POST['adjustment_date'];
    $description = $_POST['description'];
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    
    $adjustment_type = "Stock Adjustment";

    $conn->begin_transaction();

    try {
        $query = "INSERT INTO StockAdjustments (adjustment_date, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $adjustment_date, $description);
        $stmt->execute();
        $adjustment_id = $stmt->insert_id;

        $detail_query = "INSERT INTO StockAdjustmentDetails (adjustment_id, product_id, adjustment_type, quantity) VALUES (?, ?, ?, ?)";
        $detail_stmt = $conn->prepare($detail_query);

        foreach ($products as $index => $product_id) {
            $quantity = $quantities[$index];

            $stock_check_query = "SELECT stock FROM Products WHERE id = ?";
            $stock_check_stmt = $conn->prepare($stock_check_query);
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
                $detail_stmt->execute();

                $update_stock_query = "UPDATE Products SET stock = stock + ? WHERE id = ?";
                $update_stock_stmt = $conn->prepare($update_stock_query);
                $update_stock_stmt->bind_param("ii", $quantity, $product_id);
                $update_stock_stmt->execute();
            } else {
                throw new Exception("Product ID $product_id not found.");
            }
        }

        $conn->commit();
        $_SESSION['message'] = "Stock adjustment added successfully.";
        header("Location: view_adjustment.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        //$_SESSION['error'] = "Error adding adjustment: " . $e->getMessage();
        //header("Location: add_adjustment.php");
        exit();
    }
}
?>
