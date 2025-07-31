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
$message = ""; // Variable to store feedback messages
$material = null;

// Handle form submission to update stock balance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_usage"])) {
    // Get form data
    $materialId = (int) $_POST["material_id"];
    $usageQuantity = (int) $_POST["usage_quantity"];

    // Get the current stock for the material
    $stmt = $conn->prepare("SELECT * FROM raw_materials WHERE material_id = ?");
    $stmt->bind_param("i", $materialId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $material = $result->fetch_assoc();
        $newStockQuantity = $material['stock_quantity'] - $usageQuantity;

        if ($newStockQuantity < 0) {
            $message = "<div class='alert alert-danger'>Error: Not enough stock available for usage.</div>";
        } else {
            // Update the stock quantity after usage
            $updateStmt = $conn->prepare("UPDATE raw_materials SET stock_quantity = ? WHERE material_id = ?");
            $updateStmt->bind_param("ii", $newStockQuantity, $materialId);
            if ($updateStmt->execute()) {
                // Insert usage history for tracking
                $usageDate = date('Y-m-d'); // Today's date
                $insertStmt = $conn->prepare("INSERT INTO usage_history (material_id, usage_quantity, usage_date) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iis", $materialId, $usageQuantity, $usageDate);
                $insertStmt->execute();

                $message = "<div class='alert alert-success'>Stock updated successfully! Remaining stock: $newStockQuantity</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
            $updateStmt->close();
        }
    }
    $stmt->close();
}

// Handle form submission to update threshold value
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_threshold"])) {
    $materialId = (int) $_POST["material_id"];
    $newThreshold = (int) $_POST["new_threshold"];

    // Update the threshold value
    $stmt = $conn->prepare("UPDATE raw_materials SET threshold = ? WHERE material_id = ?");
    $stmt->bind_param("ii", $newThreshold, $materialId);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Threshold updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
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

// If material ID is provided, fetch the material details to edit
if (isset($_GET["material_id"]) && !empty($_GET["material_id"])) {
    $materialId = $_GET["material_id"];
    $stmt = $conn->prepare("SELECT * FROM raw_materials WHERE material_id = ?");
    $stmt->bind_param("i", $materialId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $material = $result->fetch_assoc();
    }
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Raw Material</title>
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
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .table-container {
            margin-top: 20px;
        }
        .warning {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Raw Materials</h3>
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
        <h2 class="my-4">Edit Raw Material</h2>

        <!-- Display feedback message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Select material to edit usage -->
        <form method="GET" action="" class="mb-4">
            <div class="mb-3">
                <label for="material_id" class="form-label">Select Material</label>
                <select name="material_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Select Material</option>
                    <?php foreach ($materials as $item): ?>
                        <option value="<?php echo $item["material_id"]; ?>" <?php echo isset($material) && $material['material_id'] == $item["material_id"] ? 'selected' : ''; ?>>
                            <?php echo $item["name"]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (!isset($_GET["material_id"]) || empty($_GET["material_id"])): ?>
            <p class="warning">Please select a material to update.</p>
        <?php elseif ($material): ?>
            <h3>Update Usage for: <?php echo $material['name']; ?></h3>
            <p>Current Stock: <?php echo $material['stock_quantity']; ?> <?php echo $material['unit']; ?></p>

            <!-- Form to update usage and balance -->
            <form method="POST" action="" class="form-container">
                <input type="hidden" name="material_id" value="<?php echo $material['material_id']; ?>">
                <div class="mb-3">
                    <label for="usage_quantity" class="form-label">Usage Quantity</label>
                    <input type="number" class="form-control" name="usage_quantity" placeholder="Usage Quantity" required>
                </div>
                <button type="submit" name="update_usage" class="btn btn-primary">Update Usage</button>
            </form>
        <?php endif; ?>

        <!-- Form to update threshold value -->
        <div class="form-container mt-5">
            <h3>Update Threshold Value</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="material_id" class="form-label">Select Material</label>
                    <select name="material_id" class="form-select" required>
                        <option value="">Select Material</option>
                        <?php foreach ($materials as $item): ?>
                            <option value="<?php echo $item["material_id"]; ?>">
                                <?php echo $item["name"]; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="new_threshold" class="form-label">New Threshold Value</label>
                    <input type="number" class="form-control" name="new_threshold" placeholder="New Threshold" required>
                </div>
                <button type="submit" name="update_threshold" class="btn btn-warning">Update Threshold</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>