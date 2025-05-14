<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to place an order.");
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $paymentMethod = $_POST['payment_method'];  // Assuming this is passed from the form
    $cartData = json_decode($_POST['cart_data'], true);

    if (empty($cartData)) {
        die("Cart is empty.");
    }

    // Calculate total
    $totalAmount = 0;
    foreach ($cartData as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    // Insert into `orders`
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'processing')");
    $stmt->bind_param("ids", $user_id, $totalAmount, $address);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Insert each item into `order_item`
    $stmt = $conn->prepare("INSERT INTO order_item (order_id, item_type, item_id, quantity, price, total_price, item_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($cartData as $item) {
        $type = $item['type'];  // e.g. 'product'
        $item_id = $item['id'];
        $item_name = $item['name']; // Assuming you have item names in the cart data
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total = $price * $quantity;
        $stmt->bind_param("isiiids", $order_id, $type, $item_id, $quantity, $price, $total, $item_name);
        $stmt->execute();
    }
    $stmt->close();

    // Now, insert into the payments table
    $paymentMethod = $_POST['payment_method'];  // Assuming 'payment_method' is passed from the form
    $amountPaid = $totalAmount;  // Assuming the total amount is paid in full
    $paymentDate = date('Y-m-d H:i:s');  // Current timestamp for the payment date

    // Insert payment data into the payments table
    $paymentStmt = $conn->prepare("INSERT INTO payments (order_id, user_id, amount_paid, payment_date, payment_method) VALUES (?, ?, ?, ?, ?)");
    $paymentStmt->bind_param("iiiss", $order_id, $user_id, $amountPaid, $paymentDate, $paymentMethod);
    $paymentStmt->execute();
    $paymentStmt->close();

    // Clear cart (client side JS should do it too)
    echo "<script>
        alert('Your order has been placed successfully and payment has been processed!');
        localStorage.removeItem('cart');
        window.location.href = '../php/confirmation.php?order_id=" . $order_id . "';
    </script>";
    exit;
}
?>
