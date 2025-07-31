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

// Handle form submission to update the dish
$message = ""; // Variable to store feedback messages

// Update the dish details if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_dish"])) {
    // Get updated form data
    $dish_id = (int) $_POST['dish_id'];
    $name = $conn->real_escape_string($_POST["dish_name"]);
    $price = (float) $_POST["price"];
    $description = $conn->real_escape_string($_POST["description"]);

    // Handle image upload
    $imagePath = null;
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
                    $imagePath = $targetFilePath;
                } else {
                    $message = "<div class='alert alert-danger'>Error uploading image.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Image size must be less than 2MB.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
        }
    }

    // Update the dish details in the database
    if (empty($message)) {
        if ($imagePath) {
            $stmt = $conn->prepare("UPDATE dishes SET name = ?, price = ?, description = ?, image = ? WHERE dish_id = ?");
            $stmt->bind_param("sdssi", $name, $price, $description, $imagePath, $dish_id);
        } else {
            $stmt = $conn->prepare("UPDATE dishes SET name = ?, price = ?, description = ? WHERE dish_id = ?");
            $stmt->bind_param("sdsi", $name, $price, $description, $dish_id);
        }

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Dish updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Delete the dish if delete button is clicked
if (isset($_GET['delete_dish_id'])) {
    $delete_dish_id = (int) $_GET['delete_dish_id'];

    // Fetch the image path before deleting the dish
    $stmt = $conn->prepare("SELECT image FROM dishes WHERE dish_id = ?");
    $stmt->bind_param("i", $delete_dish_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imagePath = $row['image'];
        // Delete the image file if it exists
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $stmt = $conn->prepare("DELETE FROM dishes WHERE dish_id = ?");
    $stmt->bind_param("i", $delete_dish_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Dish deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch all dishes for selection
$dishes = [];
$sql = "SELECT * FROM dishes";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dishes[] = $row;
    }
}

// Fetch selected dish details if dish_id is provided (this is after selecting from dropdown)
$dish = null;
if (isset($_GET['dish_id']) && !empty($_GET['dish_id'])) {
    $dish_id = (int) $_GET['dish_id'];
    $stmt = $conn->prepare("SELECT * FROM dishes WHERE dish_id = ?");
    $stmt->bind_param("i", $dish_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $dish = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-danger'>Dish not found.</div>";
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
    <title>Edit Dish</title>
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
        .dish-image {
            max-width: 150px;
            height: auto;
            border-radius: 5px;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .delete-btn:hover {
            background-color: #e53935;
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
        <h2 class="my-4">Edit Dish</h2>

        <!-- Display feedback message -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Form to select a dish and edit -->
        <form method="GET" action="" class="mb-4">
            <div class="mb-3">
                <label for="dish_id" class="form-label">Select a Dish:</label>
                <select name="dish_id" id="dish_id" class="form-select" required onchange="this.form.submit()">
                    <option value="">Select a Dish</option>
                    <?php foreach ($dishes as $dish_item): ?>
                        <option value="<?php echo $dish_item['dish_id']; ?>" <?php echo isset($dish_id) && $dish_id == $dish_item['dish_id'] ? 'selected' : ''; ?>>
                            <?php echo $dish_item['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (isset($dish)): ?>
            <!-- Display form to edit the selected dish details -->
            <form method="POST" action="" enctype="multipart/form-data" class="form-container">
                <input type="hidden" name="dish_id" value="<?php echo $dish['dish_id']; ?>">
                <div class="mb-3">
                    <input type="text" class="form-control" name="dish_name" value="<?php echo $dish['name']; ?>" placeholder="Dish Name" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="price" value="<?php echo $dish['price']; ?>" step="0.01" placeholder="Price" required>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" name="description" placeholder="Description" required><?php echo $dish['description']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="dish_image" class="form-label">Dish Image:</label>
                    <input type="file" class="form-control" name="dish_image" accept="image/*">
                </div>
                <?php if (!empty($dish['image'])): ?>
                    <img src="<?php echo $dish['image']; ?>" alt="Dish Image" class="dish-image mb-3">
                <?php endif; ?>
                <button type="submit" name="update_dish" class="btn btn-primary">Update Dish</button>
            </form>

            <!-- Delete button for the selected dish -->
            <div class="mt-3">
                <a href="editdish.php?delete_dish_id=<?php echo $dish['dish_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this dish?')">Delete Dish</a>
            </div>
        <?php endif; ?>

        <a href="dish.php" class="btn btn-success mt-4">Back to Dishes List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>