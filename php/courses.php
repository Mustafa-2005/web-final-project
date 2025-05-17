<?php

require_once 'connection.php'; 

$courses_from_db = [];

$sql = "SELECT course_id, course_title, description, image_name, duration, course_price
        FROM courses
        ORDER BY course_id ASC"; 

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses_from_db[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GalaxyX - Courses</title>
  <link rel="stylesheet" href="../css/courses.css" />
  <link rel="icon" href="../img/pls1.png">
</head>
<body>

  <header class="header">
    <div class="logo">GALAXYX</div>
    <nav>
      <a href="../html/after-login.html">Home</a>
      <a href="trip.php">Trips</a> 
      <a href="store.php">Store</a> 
      <a href="vr-experience.html">VR</a>
      <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>">Courses</a>
      <a href="../html/cart.html">Cart (<span id="cart-count">0</span>)</a>
      <a href="../html/user-dashboard.html">My Account</a>
    </nav>
  </header>

  <section class="courses-section">
    <h1>Our Courses</h1>
    <div class="courses-container">

      <?php if (!empty($courses_from_db)): ?>
        <?php foreach ($courses_from_db as $course): ?>
          <?php
            $course_data_for_js = [
                "id"    => 'course_' . $course['course_id'], // Prefix ID for cart
                "name"  => $course['course_title'],
                "price" => (float)$course['course_price'],
                "image" => $course['image_name'] ?? '',
                "type"  => "course" // Item type for cart
            ];
          ?>
          <div class="course-item">
            <?php if (!empty($course['image_name'])): ?>
              <img src="../img/<?php echo htmlspecialchars($course['image_name']); ?>" alt="<?php echo htmlspecialchars($course['course_title']); ?>">
            <?php else: ?>
               <img src="../img/placeholder-course.png" alt="Course Image Unavailable">
            <?php endif; ?>
            <div class="course-info">
              <h2><?php echo htmlspecialchars($course['course_title']); ?></h2>
              <p><?php echo nl2br(htmlspecialchars($course['description'] ?? 'No description available.')); ?></p>
              
              <p>Duration: <?php  echo htmlspecialchars($course['duration']); ?> weeks</p>
              
              <span>$<?php echo number_format((float)$course['course_price'], 2); ?></span>
            </div>
            <button onclick='addToCart(<?php echo json_encode($course_data_for_js); ?>)' class="btn">Add to Cart</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No courses currently available. Please check back soon!</p>
      <?php endif; ?>

    </div>
  </section>

  <footer class="footer">
    <p>© 2025 GalaxyX. All Rights Reserved.</p>
  </footer>

  <script src="../js/cart.js"></script>
</body>
</html>