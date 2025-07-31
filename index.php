<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch counts for dashboard cards
$dishesCount = $conn->query("SELECT COUNT(*) AS count FROM dishes")->fetch_assoc()['count'];
$rawMaterialsCount = $conn->query("SELECT COUNT(*) AS count FROM raw_materials")->fetch_assoc()['count'];
$vendorsCount = $conn->query("SELECT COUNT(*) AS count FROM vendors")->fetch_assoc()['count'];
$purchasesCount = $conn->query("SELECT COUNT(*) AS count FROM purchases")->fetch_assoc()['count'];
$billsCount = $conn->query("SELECT COUNT(*) AS count FROM bills")->fetch_assoc()['count'];

// Fetch raw materials and dishes data
$materialsQuery = "SELECT * FROM raw_materials";
$materialsResult = $conn->query($materialsQuery);

$dishesQuery = "SELECT * FROM dishes";
$dishesResult = $conn->query($dishesQuery);

$totalRevenueQuery = "SELECT SUM(total) AS total_revenue FROM bills";
$totalRevenueResult = $conn->query($totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'];

$totalPurchasedAmountQuery = "SELECT SUM(total_amount) AS total_purchased_amount FROM purchases";
$totalPurchasedAmountResult = $conn->query($totalPurchasedAmountQuery);
$totalPurchasedAmount = $totalPurchasedAmountResult->fetch_assoc()['total_purchased_amount'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh; /* Full height */
            overflow-y: auto; /* Scrollable if content exceeds height */
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px; /* Same as sidebar width */
            flex: 1;
            padding: 20px;
        }
        .dashboard-header {
            background-color: #ffffff;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .highlight-section {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .highlight-box {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            width: 30%;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .highlight-box h3 {
            margin: 10px 0;
            font-size: 24px;
        }
        .highlight-box p {
            font-size: 18px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Dashboard</h3>
        <a href="dish.php">Dishes</a>
        <a href="rawmaterial.php">Raw Materials</a>
        <a href="orderitem.php">Order Items</a>
        <a href="report.php">Report</a>
        <a href="purchasing.php">Purchasing</a>
        <a href="vendor.php">Vendors</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header">
            <h2>Restaurant Management System</h2>
        </div>
        <div class="container mt-4">
            <h3>Overview</h3>
            <div class="row">
                <!-- Dishes Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <h4>Dishes</h4>
                        <p class="display-4"><?php echo $dishesCount; ?></p>
                        <a href="dish.php" class="btn btn-primary">View Dishes</a>
                    </div>
                </div>

                <!-- Raw Materials Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <h4>Raw Materials</h4>
                        <p class="display-4"><?php echo $rawMaterialsCount; ?></p>
                        <a href="rawmaterial.php" class="btn btn-primary">View Raw Materials</a>
                    </div>
                </div>

                <!-- Vendors Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <h4>Vendors</h4>
                        <p class="display-4"><?php echo $vendorsCount; ?></p>
                        <a href="vendor.php" class="btn btn-primary">View Vendors</a>
                    </div>
                </div>

                <!-- Purchases Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <h4>Purchases</h4>
                        <p class="display-4"><?php echo $purchasesCount; ?></p>
                        <a href="purchasing.php" class="btn btn-primary">View Purchases</a>
                    </div>
                </div>

                <!-- Bills Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center p-4">
                        <h4>Bills</h4>
                        <p class="display-4"><?php echo $billsCount; ?></p>
                        <a href="report.php" class="btn btn-primary">View Bills</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Highlight Section -->
        <div class="highlight-section">
            <div class="highlight-box">
                <h3>Total Revenue</h3>
                <p>₹<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
            <div class="highlight-box">
                 <h3>Total Purchased Amount</h3>
                 <p>₹<?php echo number_format($totalPurchasedAmount, 2); ?></p>
            </div>
            <div class="highlight-box">
                <h3>Total Bills</h3>
                <p><?php echo $billsCount; ?></p>
            </div>
        </div>

        <!-- Raw Materials Table -->
        <h2>Raw Materials Table</h2>
        <?php if ($materialsResult->num_rows > 0): ?>
            <table id="materialsTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Stock Quantity</th>
                        <th>Threshold</th>
                        <th>Unit</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($material = $materialsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $material["material_id"]; ?></td>
                            <td><?php echo $material["name"]; ?></td>
                            <td><?php echo $material["stock_quantity"]; ?></td>
                            <td><?php echo $material["threshold"]; ?></td>
                            <td><?php echo $material["unit"]; ?></td>
                            <td><?php echo $material["date_added"]; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No materials found in the database.</p>
        <?php endif; ?>

        <!-- Dishes Table -->
        <h2>Dishes Table</h2>
        <?php if ($dishesResult->num_rows > 0): ?>
            <table id="dishesTable" class="display">
                <thead>
                    <tr>
                        <th>Dish ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($dish = $dishesResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $dish["dish_id"]; ?></td>
                            <td><?php echo $dish["name"]; ?></td>
                            <td><?php echo $dish["price"]; ?></td>
                            <td><?php echo $dish["description"]; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No dishes found in the database.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#materialsTable').DataTable();
            $('#dishesTable').DataTable();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>