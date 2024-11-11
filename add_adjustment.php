<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Fetch products from the database for selection
$product_query = "SELECT id, name, stock FROM Products";
$product_result = $conn->query($product_query);

// Handle form submission to add a new stock adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adjustment_date = $_POST['adjustment_date'];
    $description = $_POST['description'];
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    
    $adjustment_type = "Stock Adjustment";

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert the stock adjustment into StockAdjustments table
        $query = "INSERT INTO StockAdjustments (adjustment_date, description) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $adjustment_date, $description);
        $stmt->execute();

        // Get the last inserted adjustment ID
        $adjustment_id = $stmt->insert_id;

        // Prepare query for inserting stock adjustment details
        $detail_query = "INSERT INTO StockAdjustmentDetails (adjustment_id, product_id, adjustment_type, quantity) VALUES (?, ?, ?, ?)";
        $detail_stmt = $conn->prepare($detail_query);

        foreach ($products as $index => $product_id) {
            $quantity = $quantities[$index];

            // Fetch the current stock for the product
            $stock_check_query = "SELECT stock FROM Products WHERE id = ?";
            $stock_check_stmt = $conn->prepare($stock_check_query);
            $stock_check_stmt->bind_param("i", $product_id);
            $stock_check_stmt->execute();
            $stock_result = $stock_check_stmt->get_result();
            $product = $stock_result->fetch_assoc();

            if ($product) {
                $current_stock = $product['stock'];

                // Check if the adjustment would cause a negative stock
                $new_stock = $current_stock - $quantity; // Since adjustment type is fixed as "subtract" in this scenario
                if ($new_stock < 0) {
                    throw new Exception("Adjustment would result in negative stock for product ID $product_id.");
                }

                // Insert adjustment detail
                $detail_stmt->bind_param("iisi", $adjustment_id, $product_id, $adjustment_type, $quantity);
                $detail_stmt->execute();

                // Update the stock in the Products table
                $update_stock_query = "UPDATE Products SET stock = stock - ? WHERE id = ?";
                $update_stock_stmt = $conn->prepare($update_stock_query);
                $update_stock_stmt->bind_param("ii", $quantity, $product_id);
                $update_stock_stmt->execute();
            } else {
                throw new Exception("Product ID $product_id not found.");
            }
        }

        // Commit the transaction
        $conn->commit();
        header("Location: view_adjustment.php"); // Redirect to view_adjustment.php on success
        exit();
    } catch (Exception $e) {
        $conn->rollback(); // Roll back the transaction if there's an error
        $error = "Error adding adjustment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Add Stock Adjustment" />
    <meta name="author" content="Admin" />
    <title>Add Stock Adjustment</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
    <!-- Page content -->
    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Add New Stock Adjustment</h1>

                <!-- Display error message if any -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Adjustment Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-boxes me-1"></i>
                        Stock Adjustment Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="add_adjustment.php">
                            <div class="mb-3">
                                <label for="adjustment_date" class="form-label">Adjustment Date</label>
                                <input type="date" class="form-control" id="adjustment_date" name="adjustment_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                            </div>

                            <!-- Dynamic Product Adjustment Fields -->
                            <div id="productAdjustments">
                                <div class="product-adjustment mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <select name="product[]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        <?php while ($product = $product_result->fetch_assoc()): ?>
                                            <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    
                                    <label for="adjustment_type" class="form-label mt-2">Adjustment Type</label>
                                    <input type="text" name="adjustment_type[]" class="form-control" value="Stock Adjustment" readonly>
                                    
                                    <label for="quantity" class="form-label mt-2">Quantity</label>
                                    <input type="number" name="quantity[]" class="form-control" min="1" required>
                                </div>
                            </div>

                            <!-- Button Layout -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="addProductAdjustment()">Add Another Product</button>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">Save Adjustment</button>
                                    <a href="view_adjustment.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Admin</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        // JavaScript to dynamically add product adjustment fields
        function addProductAdjustment() {
            const adjustmentDiv = document.createElement('div');
            adjustmentDiv.classList.add('product-adjustment', 'mb-3');
            adjustmentDiv.innerHTML = `
                <label class="form-label">Product</label>
                <select name="product[]" class="form-control" required>
                    <option value="">Select Product</option>
                    <?php 
                    $product_result->data_seek(0); // Reset the result pointer
                    while ($product = $product_result->fetch_assoc()): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label class="form-label mt-2">Adjustment Type</label>
                <input type="text" name="adjustment_type[]" class="form-control" value="Stock Adjustment" readonly>

                <label class="form-label mt-2">Quantity</label>
                <input type="number" name="quantity[]" class="form-control" min="1" required>
            `;
            document.getElementById('productAdjustments').appendChild(adjustmentDiv);
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
