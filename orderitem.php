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

// Generate a unique bill number
$billNumber = "BILL" . time();

// Fetch Dishes from Database
$query = "SELECT dish_id, image, name, price FROM dishes";
$result = $conn->query($query);
$dishes = [];
while ($row = $result->fetch_assoc()) {
    $dishes[] = $row;
}
$result->free();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Billing System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; overflow-y: auto; }
        .dish-card { transition: transform 0.3s; margin-bottom: 10px; }
        .dish-card:hover { transform: scale(1.05); }
        .bill-popup { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); overflow-y: auto; }
        .bill-content { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: 50px auto; max-height: 80vh; overflow-y: auto; }
        .logo { width: 100px; height: 100px; margin: 0 auto; display: block; }
        .container { max-height: 100vh; overflow-y: auto; }
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
    <!-- Include the JavaScript file for updating bill data format -->
    <script src="js/updateBillDataFormat.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center">Bill Report</h3>
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
        <div class="container mt-4">
            <h2 class="text-center">Restaurant Billing System</h2>
            <div class="row">
                <?php foreach ($dishes as $dish) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card dish-card shadow-sm">
                            <img src="<?= $dish['image'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $dish['name'] ?></h5>
                                <p class="card-text">₹<?= number_format($dish['price'], 2) ?></p>
                                <button class="btn btn-danger remove" data-id="<?= $dish['dish_id'] ?>">-</button>
                                <span class="quantity" data-id="<?= $dish['dish_id'] ?>">0</span>
                                <button class="btn btn-success add" data-id="<?= $dish['dish_id'] ?>" data-price="<?= $dish['price'] ?>">+</button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="text-center">
                <button class="btn btn-primary" id="generateBill">Generate Bill</button>
            </div>
        </div>

        <!-- Bill Summary Popup -->
        <div class="bill-popup" id="billPopup">
            <div class="bill-content">
                <img src="restaurant_logo.png" alt="Restaurant Logo" class="logo">
                <h4 class="text-center">The Kozhi Restaurant</h4>
                <h5 class="text-center">Near CEOA School,</h5>
                <h5 class="text-center">Anandha Nagar, Kosakulam Main Road,</h5>
                <h6 class="text-center">Madurai - 625017.</h6>
                <p class="text-center">Date: <span id="billDate"></span> | Time: <span id="billTime"></span></p>
                <p class="text-center">Bill No: <?= $billNumber ?></p>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
                    </thead>
                    <tbody id="billItems"></tbody>
                </table>
                <p><strong>Subtotal:</strong> ₹<span id="subtotal">0.00</span></p>
                <p><strong>Tax (5%):</strong> ₹<span id="tax">0.00</span></p>
                <p><strong>Total:</strong> ₹<span id="total">0.00</span></p>
                <p><b>Thank you !!! Visit Again !!! </b></p>
                <p>Warm Welcome Your Valuable </p>
                <p>feedback</p>
                <button class="btn btn-primary" id="printBill">Print</button>
                <button class="btn btn-danger" id="closePopup">Close</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        let cart = {};

        // Increment quantity
        $('.add').click(function() {
            let id = $(this).data('id');
            let price = parseFloat($(this).data('price'));
            cart[id] = (cart[id] || { qty: 0, price: price, name: $(this).siblings('.card-title').text() });
            cart[id].qty++;
            updateCart();
            updateQuantityDisplay(id);
        });

        // Decrement quantity
        $('.remove').click(function() {
            let id = $(this).data('id');
            if (cart[id] && cart[id].qty > 0) {
                cart[id].qty--;
                if (cart[id].qty === 0) delete cart[id];
                updateCart();
                updateQuantityDisplay(id);
            }
        });

        // Update quantity display
        function updateQuantityDisplay(id) {
            $(`.quantity[data-id="${id}"]`).text(cart[id] ? cart[id].qty : 0);
        }

        // Update cart and bill summary
        function updateCart() {
            let subtotal = 0;
            $('#billItems').empty();
            for (let id in cart) {
                let item = cart[id];
                subtotal += item.qty * item.price;
                $('#billItems').append(`<tr><td>${item.name}</td><td>${item.qty}</td><td>₹${(item.qty * item.price).toFixed(2)}</td></tr>`);
            }
            let tax = subtotal * 0.05;
            let total = subtotal + tax;
            $('#subtotal').text(subtotal.toFixed(2));
            $('#tax').text(tax.toFixed(2));
            $('#total').text(total.toFixed(2));
        }

        // Show bill popup
        $('#generateBill').click(function() {
            let date = new Date();
            // Format date as YYYY-MM-DD
            let formattedDate = date.toISOString().split('T')[0];
            $('#billDate').text(formattedDate);
            $('#billTime').text(date.toLocaleTimeString());
            $('#billPopup').show();
        });

        // Print bill
        $('#printBill').click(function() {
            window.print();

            // Save bill to database
            let billData = {
                billNumber: '<?= $billNumber ?>',
                date: $('#billDate').text(), // Already in YYYY-MM-DD format
                time: $('#billTime').text(),
                items: [],
                subtotal: $('#subtotal').text(),
                tax: $('#tax').text(),
                total: $('#total').text()
            };

            for (let id in cart) {
                billData.items.push({
                    name: cart[id].name,
                    qty: cart[id].qty,
                    price: cart[id].price
                });
            }

            $.post('save_bill.php', { billData: JSON.stringify(billData) }, function(response) {
                alert('Bill saved successfully!');
                // Refresh the page after saving the bill
                location.reload();
            });
        });

        // Close popup and refresh the page
        $('#closePopup').click(function() {
            $('#billPopup').hide();
            location.reload(); // Refresh the page
        });
    </script>
</body>
</html>