<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Define default start and end dates
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Modify the query to filter by date range if specified
$query = "SELECT * FROM StockAdjustments";
if ($startDate && $endDate) {
    $query .= " WHERE adjustment_date BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate) {
    $query .= " WHERE adjustment_date >= '$startDate'";
} elseif ($endDate) {
    $query .= " WHERE adjustment_date <= '$endDate'";
}
$query .= " ORDER BY adjustment_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="View Stock Adjustments" />
    <meta name="author" content="Admin" />
    <title>View Stock Adjustments</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
    <!-- Page content -->
    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Stock Adjustment List</h1>

                <!-- Add New Adjustment Button -->
                <div class="mb-4">
                    <a href="add_adjustment.php" class="btn btn-primary">Add New Adjustment</a>
                </div>

                <!-- Filter Section with Date Range -->
                <form method="GET" action="view_adjustment.php" class="mb-4 d-flex align-items-center">
                    <label for="startDate" class="form-label me-2">From:</label>
                    <input type="date" id="startDate" name="startDate" value="<?php echo htmlspecialchars($startDate); ?>" class="form-control me-2" style="width: 200px;">
                    <label for="endDate" class="form-label me-2">To:</label>
                    <input type="date" id="endDate" name="endDate" value="<?php echo htmlspecialchars($endDate); ?>" class="form-control me-2" style="width: 200px;">
                    <button type="submit" class="btn btn-secondary">Go</button>
                </form>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Stock Adjustments
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Adjustment ID</th>
                                    <th>Adjustment Date</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo (int) $row['id']; ?></td>
                                            <td><?php echo $row['adjustment_date']; ?></td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td>
                                                <a href="view_adjustment_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No adjustments found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script>
        // Initialize the DataTable
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple");
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>