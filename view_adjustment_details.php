<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Check if the user is authorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
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

// Fetch adjustment details from the database using prepared statements
$query = "SELECT sa.id AS adjustment_id, sa.adjustment_date, sa.description, sad.product_id, 
          p.name AS product_name, COALESCE(sad.adjustment_type, 'Stock Adjustment') AS adjustment_type, sad.quantity 
          FROM StockAdjustments sa 
          JOIN StockAdjustmentDetails sad ON sa.id = sad.adjustment_id 
          JOIN Products p ON sad.product_id = p.id 
          WHERE sa.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $adjustment_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="View Adjustment Details" />
    <meta name="author" content="Admin" />
    <title>Adjustment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
    <!-- Page content -->
    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Adjustment Details</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-1"></i>
                        Details for Adjustment ID: <?php echo htmlspecialchars($adjustment_id); ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Adjustment Type</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['adjustment_type'] ?: 'Stock Adjustment'); ?></td>
                                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No details found for this adjustment</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <a href="view_adjustment.php" class="btn btn-secondary mt-3">Back to Adjustments</a>
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
