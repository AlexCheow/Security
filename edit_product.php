<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Check if product_id is provided in the URL
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details from the database
    $query = "SELECT * FROM Products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Check if the product exists
    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        header("Location: view_products.php");
        exit();
    }
} else {
    $_SESSION['error'] = "No product ID provided.";
    header("Location: view_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Product</h1>

                <!-- Product Edit Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-box me-1"></i>
                        Product Details
                    </div>
                    <div class="card-body">
                        <form method="POST" action="edit_product_code.php?product_id=<?php echo $product_id; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                            </div>

                            <!-- Buttons -->
                            <button type="submit" class="btn btn-primary">Update Product</button>
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
