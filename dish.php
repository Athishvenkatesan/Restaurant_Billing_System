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

// Handle form submission
$message = ""; // Variable to store feedback messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_dish"])) {
    // Get form data
    $name = $conn->real_escape_string($_POST["dish_name"]);
    $price = (float) $_POST["price"];
    $description = $conn->real_escape_string($_POST["description"]);

    // Handle image upload
    if (isset($_FILES["dish_image"]) && $_FILES["dish_image"]["error"] == 0) {
        $targetDir = "uploads/"; // Directory to store uploaded images
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create the directory if it doesn't exist
        }

        $fileName = basename($_FILES["dish_image"]["name"]);
        $targetFilePath = $targetDir . uniqid() . "_" . $fileName; // Unique filename to avoid conflicts
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allow only certain file formats
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($fileType, $allowedTypes)) {
            // Check file size (max 2MB)
            if ($_FILES["dish_image"]["size"] <= 2 * 1024 * 1024) {
                // Upload file to server
                if (move_uploaded_file($_FILES["dish_image"]["tmp_name"], $targetFilePath)) {
                    // Insert new dish into the database with image path
                    $stmt = $conn->prepare("INSERT INTO dishes (name, price, description, image) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("sdss", $name, $price, $description, $targetFilePath);

                    if ($stmt->execute()) {
                        $message = "<div class='alert alert-success'>Dish added successfully!</div>";
                    } else {
                        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                    }
                    $stmt->close();
                } else {
                    $message = "<div class='alert alert-danger'>Error uploading image.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Image size must be less than 2MB.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Please select an image for the dish.</div>";
    }
}

// Fetch all dishes
$dishes = [];
$sql = "SELECT * FROM dishes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dishes[] = $row;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dishes Management</title>
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
        .dish-image {
            width: 150px; /* Medium size */
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Optional: Add rounded corners */
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
        <h3 class="text-center">Dishes</h3>
        <a href="index.php">Dashboard</a>
        <a href="dish.php">Dishes</a>
        <a href="editdish.php">Edit Dishes</a>
        <a href="rawmaterial.php">Raw Materials</a>
        <a href="orderitem.php">Bill Generate</a>
        <a href="report.php">Report</a>
        <a href="purchasing.php">Purchasing</a>
        <a href="vendor.php">Vendors</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Display feedback message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Form to add dishes -->
        <div class="form-container">
            <h3>Add New Dish</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="text" class="form-control" name="dish_name" placeholder="Dish Name" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="price" step="0.01" placeholder="Price" required>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" name="description" placeholder="Description" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control" name="dish_image" accept="image/*" required>
                </div>
                <button type="submit" name="add_dish" class="btn btn-primary">Add Dish</button>
            </form>
        </div>

        <!-- Display the dishes table -->
        <h2 class="my-4">Dishes Table</h2>
        <?php if (count($dishes) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dishes as $dish): ?>
                        <tr>
                            <td><?php echo $dish["dish_id"]; ?></td>
                            <td><?php echo $dish["name"]; ?></td>
                            <td>₹<?php echo number_format($dish["price"], 2); ?></td>
                            <td><?php echo $dish["description"]; ?></td>
                            <td>
                                <?php if (!empty($dish["image"])): ?>
                                    <img src="<?php echo $dish["image"]; ?>" alt="Dish Image" class="dish-image">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No dishes found in the database.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>