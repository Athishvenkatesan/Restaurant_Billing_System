$(document).ready(function () {
    let orderItems = [];

    function updateTotal() {
        let total = orderItems.reduce((sum, item) => sum + item.total_price, 0);
        let tax = total * 0.10; // 10% tax
        let finalTotal = total + tax;

        $("#totalAmount").text(finalTotal.toFixed(2));
    }

    $(".add").click(function () {
        let dishDiv = $(this).closest(".dish");
        let dish_id = dishDiv.data("id");
        let name = dishDiv.data("name");
        let price = parseFloat(dishDiv.data("price"));
        let quantitySpan = dishDiv.find(".quantity");
        let quantity = parseInt(quantitySpan.text()) + 1;
        quantitySpan.text(quantity);

        let existingItem = orderItems.find(item => item.dish_id === dish_id);
        if (existingItem) {
            existingItem.quantity = quantity;
            existingItem.total_price = quantity * price;
        } else {
            orderItems.push({ dish_id, name, quantity, total_price: price });
        }
        updateTotal();
    });

    $(".remove").click(function () {
        let dishDiv = $(this).closest(".dish");
        let dish_id = dishDiv.data("id");
        let quantitySpan = dishDiv.find(".quantity");
        let quantity = parseInt(quantitySpan.text());

        if (quantity > 0) {
            quantity -= 1;
            quantitySpan.text(quantity);

            let existingItem = orderItems.find(item => item.dish_id === dish_id);
            if (existingItem) {
                existingItem.quantity = quantity;
                existingItem.total_price = quantity * parseFloat(dishDiv.data("price"));
                if (quantity === 0) {
                    orderItems = orderItems.filter(item => item.dish_id !== dish_id);
                }
            }
        }
        updateTotal();
    });

    $("#placeOrder").click(function () {
        if (orderItems.length === 0) {
            alert("Please select at least one dish.");
            return;
        }

        let totalAmount = orderItems.reduce((sum, item) => sum + item.total_price, 0);
        let tax = totalAmount * 0.10;
        let finalAmount = totalAmount + tax;

        let billHTML = `<table border="1">
                            <tr><th>Dish</th><th>Quantity</th><th>Total Price ($)</th></tr>`;
        orderItems.forEach(item => {
            billHTML += `<tr><td>${item.name}</td><td>${item.quantity}</td><td>${(item.total_price).toFixed(2)}</td></tr>`;
        });
        billHTML += `<tr><td colspan="2" style="font-weight: bold; text-align: right;">Subtotal:</td><td>$${totalAmount.toFixed(2)}</td></tr>`;
        billHTML += `<tr><td colspan="2" style="font-weight: bold; text-align: right;">Tax (10%):</td><td>$${tax.toFixed(2)}</td></tr>`;
        billHTML += `<tr><td colspan="2" style="font-weight: bold; text-align: right;">Final Total:</td><td style="font-weight: bold;">$${finalAmount.toFixed(2)}</td></tr>`;
        billHTML += `</table>`;

        $("#billDetails").html(billHTML);
        $("#billModal").fadeIn();
    });

    $(".cancel-btn").click(function () {
        $("#billModal").fadeOut();
    });
});

function printBill() {
    let billContent = document.getElementById("billContent").innerHTML;
    let printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.write('<html><body>' + billContent + '</body></html>');
    printWindow.document.close();
    printWindow.print();
}
