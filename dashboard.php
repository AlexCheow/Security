<?php
session_start();
include 'connection.php'; // Database connection
include 'header_sidebar.php'; // Include header and sidebar

// Check if the user is authorized
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: unauthorized.php");
    exit();
}

// Fetch data for the area chart from the database
$query = "SELECT DATE(timestamp) as date, COUNT(*) as count FROM logs GROUP BY DATE(timestamp) ORDER BY date";
$result = $conn->query($query);

if (!$result) {
    die("Database query failed: " . $conn->error); // Debugging line
}

$dates = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['date'];
    $counts[] = $row['count'];
}

// Fetch data for the activity log table
$logQuery = "SELECT * FROM logs";
$logResult = $conn->query($logQuery);

if (!$logResult) {
    die("Log query failed: " . $conn->error); // Debugging line
}


// Fetch product names and stock quantities for the bar chart
$productNames = [];
$productQuantities = [];

$productQuery = "SELECT name, stock FROM Products";
$productResult = $conn->query($productQuery);

while ($productRow = $productResult->fetch_assoc()) {
    $productNames[] = $productRow['name'];
    $productQuantities[] = $productRow['stock'];
}

// Pass PHP data to JavaScript
echo "<script>
        var productNames = " . json_encode($productNames) . ";
        var productQuantities = " . json_encode($productQuantities) . ";
      </script>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/dashboard_styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
    <div id="layoutSidenav_content" style="margin-left: 11%; margin-top:3%">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard</h1>
                </div>
                
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Activity Area Chart
                            </div>
                            <div class="card-body">
                                <canvas id="myAreaChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i>
                                Product Graph
                            </div>
                            <div class="card-body">
                                <canvas id="myBarChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Activity Logs
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                    <th>Timestamp</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php if ($logResult->num_rows > 0): ?>
                                    <?php while ($row = $logResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                                            <td><?php echo htmlspecialchars($row['action']); ?></td>
                                            <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No activity logs found</td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    
    <script>
        // Convert PHP arrays to JavaScript
        const dates = <?php echo json_encode($dates); ?>;
        const counts = <?php echo json_encode($counts); ?>;

        // Chart.js configuration for the area chart
        var ctx = document.getElementById("myAreaChart").getContext("2d");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates, // X-axis labels (dates)
                datasets: [{
                    label: "Activity Count",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: counts // Y-axis data (counts)
                }]
            },
            options: {
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, .125)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Bar Chart Example - displaying product stock quantities
var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: productNames, // Product names from PHP
        datasets: [{
            label: "Stock Quantity",
            backgroundColor: "rgba(2,117,216,1)",
            borderColor: "rgba(2,117,216,1)",
            data: productQuantities // Stock quantities from PHP
        }],
    },
    options: {
        scales: {
            xAxes: [{
                time: {
                    unit: 'product'
                },
                gridLines: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 10
                }
            }],
            yAxes: [{
                ticks: {
                    min: 0,
                    maxTicksLimit: 5,
                    beginAtZero: true
                },
                gridLines: {
                    display: true
                }
            }],
        },
        legend: {
            display: false
        }
    }
});

    </script>

    

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
