<?php
require_once 'connection.php'; 

$trips_from_db = [];

$sql = "SELECT trip_id, trip_name, description, image_name, view_details_link, duration, trip_price
        FROM trips
        ORDER BY trip_start_data ASC"; // Optional: Order by start date or price, etc.

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trips_from_db[] = $row;
    }
}
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GalaxyX - Space Trips</title>
  
  <link rel="stylesheet" href="../css/trips.css" />
  
  <link rel="icon" href="../img/pls5.png">
</head>
<body>

  
  <header class="header">
    <div class="logo">GALAXYX</div>
    <nav>
      <a href="../html/after-login.html">Home</a>
      <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>">Trips</a>
      <a href="store.php">Store</a> 
      <a href="vr-experience.html">VR</a>
      <a href="courses.php">Courses</a>
      <a href="../html/cart.html">Cart (<span id="cart-count">0</span>)</a>
      <a href="../html/user-dashboard.html">My Account</a>
    </nav>
  </header>

  <section class="trips-section">
    <h1>Available Space Trips</h1>
    <div class="trip-cards">

      <?php if (!empty($trips_from_db)): ?>
        <?php foreach ($trips_from_db as $trip): ?>
          <?php
            
            $trip_data_for_js = [
                "id"    => 'trip_' . $trip['trip_id'], // Prefix with 'trip_' to differentiate from products
                "name"  => $trip['trip_name'],
                "price" => (float)$trip['trip_price'],
                "image" => $trip['image_name'] ?? '', // Pass image_name; if null, pass empty string
                "type"  => "trip" // Add a type identifier
            ];
            // Determine the link for "View Details"
            $details_link = !empty($trip['view_details_link']) ? htmlspecialchars($trip['view_details_link']) : '#';
            // If view_details_link is not set, you could generate one, e.g., "trip-details.php?id=" . $trip['trip_id']
          ?>
          <div class="trip-card">
            <?php if (!empty($trip['image_name'])): ?>
              <!-- Adjust path to your images folder -->
              <img src="../img/<?php echo htmlspecialchars($trip['image_name']); ?>" alt="<?php echo htmlspecialchars($trip['trip_name']); ?>">
            <?php else: ?>
              <!-- <img src="../img/placeholder-trip.png" alt="Trip Image Unavailable"> -->
            <?php endif; ?>

            <h2><?php echo htmlspecialchars($trip['trip_name']); ?></h2>
            <p><?php echo htmlspecialchars($trip['description'] ?? 'No description available.'); // Display description ?></p>
            <span>Duration: <?php echo htmlspecialchars($trip['duration']); ?> Days</span>
            <span>Price: $<?php echo number_format((float)$trip['trip_price'], 2); ?></span>

            <a href="<?php echo $details_link; ?>" class="btn">View Details</a>
            <button onclick='addToCart(<?php echo json_encode($trip_data_for_js); ?>)'>Add to Cart</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No trips currently available. Please check back later!</p>
      <?php endif; ?>

    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <p>© 2025 GalaxyX. All Rights Reserved.</p>
  </footer>

  <!-- Cart Script - IMPORTANT: Adjust path as needed -->
  <script src="../js/cart.js"></script>
  <script>
    // Optional: Call initialization function from cart.js if needed
    // (function() {
    //   if (typeof initializeCartDisplay === 'function') {
    //     initializeCartDisplay();
    //   } else if (typeof updateCartCount === 'function') {
    //     updateCartCount();
    //   }
    // })();
  </script>
</body>
</html>