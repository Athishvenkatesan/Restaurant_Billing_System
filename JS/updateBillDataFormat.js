// Show bill popup
$('#generateBill').click(function() {
    let date = new Date();
    let formattedDate = date.toISOString().split('T')[0]; // YYYY-MM-DD
    let formattedTime = date.toTimeString().split(' ')[0]; // HH:MM:SS

    $('#billDate').text(formattedDate);
    $('#billTime').text(formattedTime);
    $('#billPopup').show();
});

// Print bill and save to database
$('#printBill').click(function() {
    window.print();

    // Save bill to database
    let billData = {
        billNumber: '<?= $billNumber ?>',
        date: $('#billDate').text(), // Ensure this is in YYYY-MM-DD format
        time: $('#billTime').text(), // Ensure this is in HH:MM:SS format
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
        location.reload(); // Refresh the page
    });
});