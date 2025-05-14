<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your order.");
}
$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}
$order_id = $_GET['order_id'];

// Fetch order info
$sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found.");
}
$order = $order_result->fetch_assoc();

// Fetch order items
$order_items_sql = "SELECT * FROM order_item WHERE order_id = ?";
$order_items_stmt = $conn->prepare($order_items_sql);
$order_items_stmt->bind_param("i", $order_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();

$order_items = [];
$total_price = 0;

while ($item = $order_items_result->fetch_assoc()) {
    $itemName = $item['item_name'];  // Get item name directly from the order_item table
    $price = $item['price'];         // Get the price from the order_item table
    $quantity = $item['quantity'];   // Get quantity from the order_item table
    $total = $item['total_price'];   // Get total price for the item from the order_item table

    // If for some reason item name is missing, fall back to unknown item
    if (empty($itemName)) {
        $itemName = "Unknown Item";
    }

    $item['item_name'] = $itemName;
    $item['price'] = $price;
    $item['subtotal'] = $total;
    $order_items[] = $item;
    $total_price += $total;  // Adding the total price for the entire order
}

// Close the database connections
$order_items_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GalaxyX - Order Confirmation</title>
  <link rel="stylesheet" href="../css/confirmation.css" />
  <link rel="icon" href="../img/pls7.png">
</head>
<body>

<header class="header">
  <div class="logo">GALAXYX</div>
  <nav>
    <a href="../html/after-login.html">Home</a>
    <a href="trip.php">Trips</a>
    <a href="store.php">Store</a>
    <a href="../html/vr-experience.html">VR</a>
    <a href="courses.php">Courses</a>
    <a href="../html/cart.html">Cart (<span id="cart-count">0</span>)</a>
    <a href="../html/user-dashboard.html">My Account</a>
  </nav>
</header>

<section class="confirmation-section">
  <h1>Order Confirmed!</h1>
  <p>Thank you for your order. It's being processed, and you'll receive an email shortly.</p>

  <div class="order-summary">
    <h2>Order Summary</h2>
    <p><strong>Order Number:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>

    <h3>Items Ordered</h3>
    <ul>
      <?php foreach ($order_items as $item): ?>
        <li>
          <?php echo htmlspecialchars($item['item_name']); ?> — 
          Quantity: <?php echo $item['quantity']; ?> — 
          $<?php echo number_format($item['price'], 2); ?> each — 
          Subtotal: $<?php echo number_format($item['subtotal'], 2); ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>
  </div>

  <div class="order-status">
    <h3>Order Status: <span class="status"><?php echo ucfirst(htmlspecialchars($order['status'])); ?></span></h3>
  </div>

    <p>If you have any questions or need assistance, feel free to reach out to us at <a href="mailto:support@galaxyx.com" class="contact-link">support@galaxyx.com</a>. We're here to help!</p>
  <a href="../html/after-login.html" class="btn">Back to Home</a>
</section>

<footer class="footer">
  <p>© 2025 GalaxyX. All Rights Reserved.</p>
</footer>

<script src="../js/cart.js"></script>
</body>
</html>
