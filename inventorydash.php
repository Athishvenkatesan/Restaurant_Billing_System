<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get count of raw materials
$materialQuery = "SELECT COUNT(*) as material_count, SUM(stock_quantity) as total_stock FROM raw_materials";
$materialResult = $conn->query($materialQuery);
$materialData = $materialResult->fetch_assoc();

// Query to get count of dishes
$dishQuery = "SELECT COUNT(*) as dish_count, AVG(price) as average_price FROM dishes";
$dishResult = $conn->query($dishQuery);
$dishData = $dishResult->fetch_assoc();

// Query to get the count of raw materials that are below the threshold
$criticalMaterialQuery = "SELECT COUNT(*) as critical_count FROM raw_materials WHERE stock_quantity <= threshold";
$criticalMaterialResult = $conn->query($criticalMaterialQuery);
$criticalMaterialData = $criticalMaterialResult->fetch_assoc();

// Query to get count of vendors
$vendorQuery = "SELECT COUNT(*) as vendor_count FROM vendors";
$vendorResult = $conn->query($vendorQuery);
$vendorData = $vendorResult->fetch_assoc();

// Query to get total purchases details
$purchasingQuery = "SELECT COUNT(*) as total_purchases, SUM(quantity * unit_price) as total_purchase_value FROM purchases";
$purchasingResult = $conn->query($purchasingQuery);
$purchasingData = $purchasingResult->fetch_assoc();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #333;
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
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }
        .button:hover {
            background-color: #45a049;
        }
        /* Highlight Section Styles */
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
            width: 22%;
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
    </style>
</head>
<body>
    <h2 align="center">Inventory Dashboard</h2>

    <!-- Highlight Section for Counts -->
    <div class="highlight-section">
        <div class="highlight-box" style="background-color: #FF5733;">
            <h3>Raw Materials</h3>
            <p>Total Materials: <?php echo $materialData['material_count']; ?></p>
            <p>Available Stock: <?php echo $materialData['total_stock']; ?> kg</p>
            <p>Critical Materials: <?php echo $criticalMaterialData['critical_count']; ?></p>
        </div>

        <div class="highlight-box" style="background-color: #3498DB;">
            <h3>Dishes</h3>
            <p>Total Dishes: <?php echo $dishData['dish_count']; ?></p>
            <p>Average Price: $<?php echo number_format($dishData['average_price'], 2); ?></p>
            <p>Popular Dish: <?php echo "Example Dish"; // Replace with actual logic if needed ?></p>
        </div>

        <div class="highlight-box" style="background-color: #F39C12;">
            <h3>Vendors</h3>
            <p>Total Vendors: <?php echo $vendorData['vendor_count']; ?></p>
        </div>

        <div class="highlight-box" style="background-color: #E74C3C;">
            <h3>Purchasing</h3>
            <p>Total Purchases: <?php echo $purchasingData['total_purchases']; ?></p>
            <p>Total Purchase Value: $<?php echo number_format($purchasingData['total_purchase_value'], 2); ?></p>
        </div>
    </div>
    <br>

    <hr>
    <!-- Navigation Links -->
    <h4 align="center">Navigation Links</h4>
    <a href="rawmaterial.php" class="button">Raw Material</a>
    <a href="dish.php" class="button">Dish</a>
    <a href="vendor.php" class="button">Vendor</a>
    <a href="purchasing.php" class="button">Purchasing</a>
    <a href="orderitem.php" class="button">Bill Generate</a>
    <a href="report.php" class="button">Report</a>
    <a href="inventorydash.php" class="button">Inventory Dashboard</a>
    <br><br>
    <hr>

    <h2>Raw Materials Table</h2>
    <?php
    // Reconnect to the database for the table data
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Query to get all raw materials data
    $materialsQuery = "SELECT * FROM raw_materials";
    $materialsResult = $conn->query($materialsQuery);

    if ($materialsResult->num_rows > 0):
    ?>
        <table id="materialsTable">
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

    <h2>Dishes Table</h2>
    <?php
    // Query to get all dishes data
    $dishesQuery = "SELECT * FROM dishes";
    $dishesResult = $conn->query($dishesQuery);

    if ($dishesResult->num_rows > 0):
    ?>
        <table id="dishesTable">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script>
        // Initialize DataTables for both tables
        $(document).ready(function() {
            $('#materialsTable').DataTable();
            $('#dishesTable').DataTable();
        });
    </script>
</body>
</html>
