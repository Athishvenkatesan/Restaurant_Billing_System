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

// Initialize message variable
$message = "";

// Handle form submission for adding new material
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_material"])) {
    $name = $conn->real_escape_string($_POST["material_name"]);
    $stockQuantity = (int) $_POST["stock_quantity"];
    $threshold = (int) $_POST["threshold"];
    $unit = $conn->real_escape_string($_POST["unit"]);
    $dateAdded = date("Y-m-d"); // Current date

    // Check if material already exists
    $checkSql = $conn->prepare("SELECT * FROM raw_materials WHERE name = ?");
    $checkSql->bind_param("s", $name);
    $checkSql->execute();
    $result = $checkSql->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-warning'>Material already exists. Please update the stock instead.</div>";
    } else {
        // Insert new material
        $insertSql = $conn->prepare("INSERT INTO raw_materials (name, stock_quantity, threshold, unit, date_added) VALUES (?, ?, ?, ?, ?)");
        $insertSql->bind_param("siiss", $name, $stockQuantity, $threshold, $unit, $dateAdded);
        if ($insertSql->execute()) {
            $message = "<div class='alert alert-success'>Material added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
        $insertSql->close();
    }
    $checkSql->close();
}

// Handle form submission for adding stock to existing material
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_stock"])) {
    $materialId = (int) $_POST["material_id"];
    $additionalStock = (int) $_POST["additional_stock"];

    // Update stock quantity
    $updateSql = $conn->prepare("UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE material_id = ?");
    $updateSql->bind_param("ii", $additionalStock, $materialId);
    if ($updateSql->execute()) {
        $message = "<div class='alert alert-success'>Stock added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $updateSql->close();
}

// Fetch all raw materials
$materials = [];
$sql = "SELECT * FROM raw_materials";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $materials[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raw Materials Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .form-container { max-width: 500px; margin: 0 auto; }
        .table-container { margin-top: 20px; }
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
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Raw-Material</h3>
        <a href="index.php">Dashboard</a>
        <a href="dish.php">Dishes</a>
        <a href="rawmaterial.php">Raw Materials</a>
        <a href="editrawmaterial.php">Edit Raw Materials</a>
        <a href="orderitem.php">Bill Generate</a>
        <a href="report.php">Report</a>
        <a href="purchasing.php">Purchasing</a>
        <a href="vendor.php">Vendors</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Display feedback message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Form to add new material -->
        <div class="form-container">
            <h3>Add Raw Material</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" class="form-control" name="material_name" placeholder="Material Name" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="stock_quantity" placeholder="Stock Quantity" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="threshold" placeholder="Threshold" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="unit" placeholder="Unit (e.g., kg, liters)" required>
                </div>
                <button type="submit" name="add_material" class="btn btn-primary">Add Material</button>
            </form>
        </div>

        <!-- Form to add stock to existing material -->
        <div class="form-container">
            <h3>Add Stock to Existing Material</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <select class="form-control" name="material_id" required>
                        <option value="">Select Material</option>
                        <?php foreach ($materials as $material): ?>
                            <option value="<?php echo $material['material_id']; ?>"><?php echo $material['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="additional_stock" placeholder="Add Stock Quantity" required>
                </div>
                <button type="submit" name="add_stock" class="btn btn-primary">Add Stock</button>
            </form>
        </div>

        <!-- Display the raw materials table -->
        <div class="table-container">
            <h2>Raw Materials Table</h2>
            <?php if (count($materials) > 0): ?>
                <table class="table table-bordered">
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
                        <?php foreach ($materials as $material): ?>
                            <tr>
                                <td><?php echo $material["material_id"]; ?></td>
                                <td><?php echo $material["name"]; ?></td>
                                <td><?php echo $material["stock_quantity"]; ?></td>
                                <td><?php echo $material["threshold"]; ?></td>
                                <td><?php echo $material["unit"]; ?></td>
                                <td><?php echo $material["date_added"]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No materials found in the database.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkThreshold(name, threshold) {
            if (threshold < 5) {
                setTimeout(function() {
                    var confirmUpdate = confirm("Warning: The threshold for '" + name + "' is below 5. Do you want to update it?");
                    if (confirmUpdate) {
                        window.location.href = "editrawmaterial.php";
                    }
                }, 500);
            }
        }
        <?php foreach ($materials as $material): ?>
            checkThreshold("<?php echo $material['name']; ?>", <?php echo $material['threshold']; ?>);
        <?php endforeach; ?>
    </script>
</body>
</html>