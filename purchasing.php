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

// Handle form submission for purchasing material
$message = ""; // Variable to store feedback messages
$messageClass = ""; // Variable to store message class (success/error)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendor_id = (int) $_POST['vendor_id'];
    $material_id = (int) $_POST['material_id'];
    $quantity = (float) $_POST['quantity'];
    $unit_price = (float) $_POST['unit_price'];

    // Check if quantity and unit_price are valid numbers and greater than 0
    if ($quantity > 0 && $unit_price > 0) {
        // Check if a purchase record already exists for the vendor and material
        $checkQuery = "SELECT * FROM purchases WHERE vendor_id = ? AND material_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $vendor_id, $material_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // If record exists, display message that the purchase already exists
            $message = "Purchase record already exists for this vendor and material.";
            $messageClass = "alert-danger"; // Bootstrap class for error messages
        } else {
            // If no record exists, insert the purchase record
            $insertQuery = "INSERT INTO purchases (vendor_id, material_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iidd", $vendor_id, $material_id, $quantity, $unit_price);

            if ($stmt->execute()) {
                $message = "Purchase recorded successfully!";
                $messageClass = "alert-success"; // Bootstrap class for success messages
            } else {
                $message = "Error recording purchase: " . $conn->error;
                $messageClass = "alert-danger"; // Bootstrap class for error messages
            }
        }
    } else {
        $message = "Please enter valid values for quantity and unit price.";
        $messageClass = "alert-danger"; // Bootstrap class for error messages
    }
}

// Get all vendors and materials from the database for the dropdowns
$vendorsQuery = "SELECT * FROM vendors";
$vendorsResult = $conn->query($vendorsQuery);

$materialsQuery = "SELECT * FROM raw_materials";
$materialsResult = $conn->query($materialsQuery);

// Fetch all purchase records from the database
$purchasesQuery = "SELECT p.*, v.name AS vendor_name, rm.name AS material_name 
                   FROM purchases p
                   JOIN vendors v ON p.vendor_id = v.vendor_id
                   JOIN raw_materials rm ON p.material_id = rm.material_id";
$purchasesResult = $conn->query($purchasesQuery);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchasing Page</title>
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Purchasing</h3>
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
        <!-- Message display for success or error -->
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageClass; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Purchase Form -->
        <div class="form-container">
            <h3>Record Purchase</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="vendor" class="form-label">Select Vendor:</label>
                    <select name="vendor_id" id="vendor" class="form-select" required>
                        <?php
                        if ($vendorsResult->num_rows > 0) {
                            while ($row = $vendorsResult->fetch_assoc()) {
                                echo "<option value='".$row['vendor_id']."'>".$row['name']."</option>";
                            }
                        } else {
                            echo "<option value=''>No vendors available</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="material" class="form-label">Select Material:</label>
                    <select name="material_id" id="material" class="form-select" required>
                        <?php
                        if ($materialsResult->num_rows > 0) {
                            while ($row = $materialsResult->fetch_assoc()) {
                                echo "<option value='".$row['material_id']."'>".$row['name']."</option>";
                            }
                        } else {
                            echo "<option value=''>No materials available</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="unit_price" class="form-label">Unit Price (per item):</label>
                    <input type="number" class="form-control" name="unit_price" id="unit_price" min="0.01" step="0.01" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Record Purchase</button>
            </form>
        </div>

        <!-- Display All Purchase Records -->
        <h2 class="my-4">All Purchase Records</h2>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Purchase ID</th>
                        <th>Vendor Name</th>
                        <th>Material Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                        <th>Date Purchased</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($purchasesResult->num_rows > 0) {
                        while ($row = $purchasesResult->fetch_assoc()) {
                            $totalPrice = $row['quantity'] * $row['unit_price'];
                            echo "<tr>
                                    <td>".$row['purchase_id']."</td>
                                    <td>".$row['vendor_name']."</td>
                                    <td>".$row['material_name']."</td>
                                    <td>".$row['quantity']."</td>
                                    <td>₹".number_format($row['unit_price'], 2)."</td>
                                    <td>₹".number_format($totalPrice, 2)."</td>
                                    <td>".$row['purchase_date']."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No purchase records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>