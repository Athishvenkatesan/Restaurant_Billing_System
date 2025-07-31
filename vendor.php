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

// Handle form submission for adding vendor
$message = ""; // Variable to store feedback messages
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $conn->real_escape_string($_POST['name']);
    $contact_person = $conn->real_escape_string($_POST['contact_person']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);

    // Check if vendor already exists (by name or email)
    $checkQuery = "SELECT * FROM vendors WHERE name = ? OR email = ?";
    $stmt_check = $conn->prepare($checkQuery);
    $stmt_check->bind_param("ss", $name, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Vendor already exists with the same name or email.</div>";
        $error = true;
    } else {
        // Prepare the SQL query to insert the data
        $insertQuery = "INSERT INTO vendors (name, contact_person, contact_number, email, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        
        // Bind parameters and execute the query
        $stmt->bind_param("sssss", $name, $contact_person, $contact_number, $email, $address);

        // Check if the query is successful
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Vendor added successfully!</div>";
            // Reset the form fields by emptying them
            $name = $contact_person = $contact_number = $email = $address = '';
        } else {
            $message = "<div class='alert alert-danger'>Error adding vendor: " . $conn->error . "</div>";
            $error = true;
        }
    }
}

// Fetch all vendors from the database
$vendorsQuery = "SELECT * FROM vendors";
$vendorsResult = $conn->query($vendorsQuery);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vendor</title>
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
        <h3 class="text-center">Vendor</h3>
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
        <?php if (!empty($message)) echo $message; ?>

        <!-- Vendor Form -->
        <div class="form-container">
            <h3>Add Vendor</h3>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Vendor Name:</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contact_person" class="form-label">Contact Person:</label>
                    <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?php echo htmlspecialchars($contact_person ?? '', ENT_QUOTES); ?>">
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Number:</label>
                    <input type="number" class="form-control" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($contact_number ?? '', ENT_QUOTES); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address:</label>
                    <textarea class="form-control" name="address" id="address" rows="4"><?php echo htmlspecialchars($address ?? '', ENT_QUOTES); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Vendor</button>
            </form>
        </div>

        <!-- Display All Vendors -->
        <h2 class="my-4">All Vendors</h2>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vendor ID</th>
                        <th>Vendor Name</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch and display all vendor records
                    if ($vendorsResult->num_rows > 0) {
                        while ($row = $vendorsResult->fetch_assoc()) {
                            echo "<tr>
                                    <td>".$row['vendor_id']."</td>
                                    <td>".$row['name']."</td>
                                    <td>".$row['contact_person']."</td>
                                    <td>".$row['contact_number']."</td>
                                    <td>".$row['email']."</td>
                                    <td>".$row['address']."</td>
                                    <td>".$row['date_added']."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No vendors found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>