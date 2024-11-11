<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Handle form submission to add a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = 0; // Set stock to 0 initially

    // Insert the new product into the database
    $query = "INSERT INTO Products (name, description, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $name, $description, $price, $stock);

    if ($stmt->execute()) {
        // Redirect to view_products.php after successful addition
        header("Location: view_products.php");
        exit();
    } else {
        $error = "Error adding product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Add New Product" />
    <meta name="author" content="Admin" />
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
    <!-- Page content -->
    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Add New Product</h1>

                <!-- Display error message if any -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Product Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-box me-1"></i>
                        Product Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="add_product.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <!-- Hidden stock field set to 0 -->
                            <input type="hidden" name="stock" value="0">
                            
                            <button type="submit" class="btn btn-primary">Add Product</button>
                            <a href="view_products.php" class="btn btn-secondary">Cancel</a>
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
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
