<?php

include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Check if the user is authenticated and authorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    // Redirect unauthorized users to an error or login page
    header("Location: unauthorized.php");
    exit();
}

// Generate CSRF token if not already generated
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch products for selection
$product_query = "SELECT id, name FROM Products";
$product_result = $conn->query($product_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Add Stock Adjustment</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

    <?php include 'header_sidebar.php'; ?>

    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Add New Stock Adjustment</h1>

                <!-- Display error message if any -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-boxes me-1"></i>
                        Stock Adjustment Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="add_adjustment_code.php">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                            <div class="mb-3">
                                <label for="adjustment_date" class="form-label">Adjustment Date</label>
                                <input type="date" class="form-control" id="adjustment_date" name="adjustment_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
                            </div>

                            <div id="productAdjustments">
                                <div class="product-adjustment mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <select name="product[]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        <?php
                                        // Fetch products from database for selection
                                        $product_result->data_seek(0); // Reset pointer
                                        while ($product = $product_result->fetch_assoc()): ?>
                                            <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    
                                    <label for="adjustment_type" class="form-label mt-2">Adjustment Type</label>
                                    <input type="text" name="adjustment_type[]" class="form-control" value="Stock Adjustment" readonly>
                                    
                                    <label for="quantity" class="form-label mt-2">Quantity</label>
                                    <input type="number" name="quantity[]" class="form-control" min="1" required>
                                </div>
                            </div>

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
        function addProductAdjustment() {
            const adjustmentDiv = document.createElement('div');
            adjustmentDiv.classList.add('product-adjustment', 'mb-3');
            adjustmentDiv.innerHTML = `
                <label class="form-label">Product</label>
                <select name="product[]" class="form-control" required>
                    <option value="">Select Product</option>
                    <?php
                    $product_result->data_seek(0); // Reset pointer
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
