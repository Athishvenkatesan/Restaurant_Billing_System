<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$startDate = $endDate = "";
$whereClause = "";
$message = "";

// Handle date filtering
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["filter_report"])) {
    $startDate = $_POST["start_date"];
    $endDate = $_POST["end_date"];

    if (!empty($startDate) && !empty($endDate)) {
        // Validate date range
        if ($startDate > $endDate) {
            $message = "<div class='alert alert-danger'>Error: Start date cannot be greater than end date.</div>";
        } else {
            $whereClause = "WHERE b.date BETWEEN ? AND ?"; // Updated column name to `date`
        }
    } else {
        $message = "<div class='alert alert-danger'>Error: Please select both start and end dates.</div>";
    }
}

// Fetch report data
$reportData = [];
$sql = "SELECT bi.item_name, SUM(bi.quantity) AS total_quantity, SUM(bi.price * bi.quantity) AS total_price
        FROM bill_items bi
        JOIN bills b ON bi.bill_id = b.bill_id";

if (!empty($whereClause)) {
    $sql .= " $whereClause";
}

$sql .= " GROUP BY bi.item_name";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if (!empty($whereClause)) {
        $stmt->bind_param("ss", $startDate, $endDate);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $reportData[] = $row;
        }
    } else {
        $message = "<div class='alert alert-warning'>No records found for the selected date range.</div>";
    }
    $stmt->close();
} else {
    $message = "<div class='alert alert-danger'>Error preparing SQL statement.</div>";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dish Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
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
            margin-left: 270px; /* Adjust for sidebar width */
            flex: 1;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Report</h3>
        <a href="index.php">Dashboard</a>
        <a href="dish.php">Dishes</a>
        <a href="rawmaterial.php">Raw Materials</a>
        <a href="orderitem.php">Bill Generate</a>
        <a href="report.php">Report</a>
        <a href="purchasing.php">Purchasing</a>
        <a href="vendor.php">Vendors</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="text-center my-4">Dish Sales Report</h2>

        <!-- Display feedback message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Date Filter Form -->
        <div class="form-container">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $startDate; ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date:</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $endDate; ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="filter_report" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Display Report Data -->
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Dish Name</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData)): ?>
                        <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?php echo $row['item_name']; ?></td>
                                <td><?php echo $row['total_quantity']; ?></td>
                                <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>