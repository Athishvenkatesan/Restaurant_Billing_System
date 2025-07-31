<?php
session_start();

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Decode the bill data from the frontend
$billData = json_decode($_POST['billData'], true);

// Extract bill details
$billNumber = $billData['billNumber'];
$date = $billData['date']; // Ensure this is in YYYY-MM-DD format
$time = $billData['time']; // Ensure this is in HH:MM:SS format
$subtotal = $billData['subtotal'];
$tax = $billData['tax'];
$total = $billData['total'];

// Debugging: Check if date and time are correctly received
if (empty($date)) {
    die("Error: Date is empty.");
}
if (empty($time)) {
    die("Error: Time is empty.");
}

// Insert bill into database
$stmt = $conn->prepare("INSERT INTO bills (bill_number, date, time, subtotal, tax, total) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("ssssss", $billNumber, $date, $time, $subtotal, $tax, $total);
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}
$billId = $stmt->insert_id;
$stmt->close();

// Insert bill items into database
foreach ($billData['items'] as $item) {
    $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, item_name, quantity, price, date) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("isids", $billId, $item['name'], $item['qty'], $item['price'], $date);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();
echo "Bill saved successfully!";
?>