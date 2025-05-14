<?php 
require_once 'connection.php'; 

$products_from_db = [];

$sql = "SELECT product_id, product_name, description, product_price, image_name, in_stock FROM store WHERE in_stock > 0";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products_from_db[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GalaxyX - Space Store</title>
  <link rel="stylesheet" href="../css/store.css" />
  <link rel="icon" href="../img/pls3.png">
</head>
<body>
  <header class="header">
    <div class="logo">GalaxyX</div>
    <nav>
      <a href="../html/after-login.html">Home</a>
      <a href="trip.php">Trips</a>
      <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>">Store</a>
      <a href="../html/vr-experience.html">VR</a>
      <a href="courses.php">Courses</a>
      <a href="../html/cart.html">Cart (<span id="cart-count">0</span>)</a>
      <a href="../html/user-dashboard.html">My Account</a>
    </nav>
  </header>

  <section class="store-section">
    <h1>Space Store</h1>
    <div class="product-cards">
      <?php if (!empty($products_from_db)): ?>
        <?php foreach ($products_from_db as $product): ?>
          <div class="product-card">
            <img src="../img/<?php echo htmlspecialchars($product['image_name'] ?? 'placeholder.png'); ?>" 
                 alt="<?php echo htmlspecialchars($product['product_name'] ?? 'Product image'); ?>">
            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <span>Price: $<?php echo number_format((float)$product['product_price'], 2); ?></span>
            <?php
              $product_data_for_js = [
                "id" => $product['product_id'],
                "name" => $product['product_name'],
                "price" => (float)$product['product_price'],
                "image" => $product['image_name'] ?? '',
                "type" => "product"
              ];
            ?>
            <button onclick='addToCart(<?php echo json_encode($product_data_for_js); ?>)'>Add to Cart</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found in the store at the moment, or all items are out of stock.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer class="footer">
    <p>© 2025 GalaxyX. All Rights Reserved.</p>
  </footer>

  <script src="../js/cart.js" defer></script>
</body>
</html>
