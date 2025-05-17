<?php
require_once 'connection.php';

header('Content-Type: application/json');



try {
    // Get total users
    $users_query = $conn->query("SELECT COUNT(*) as count FROM users");
    $users_count = $users_query->fetch_assoc()['count'];

    // Get active orders
    $orders_query = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'processing'");
    $active_orders = $orders_query->fetch_assoc()['count'];

    // Get total products
    $products_query = $conn->query("SELECT COUNT(*) as count FROM store");
    $products_count = $products_query->fetch_assoc()['count'];

    // Get total revenue
    $revenue_query = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
    $total_revenue = $revenue_query->fetch_assoc()['total'] ?? 0;

    // Get recent activity (last 10 actions)
    $activity_query = $conn->query("
       ( SELECT 'Order' AS type, 
         order_data AS timestamp, 
         CONCAT('New order #', order_id, ' - $', total_amount) AS description FROM orders )
     UNION ALL
       ( SELECT 'User' AS type, 
         NULL AS timestamp, 
         CONCAT('New user: ', name) AS description FROM users
)
ORDER BY timestamp DESC IS NULL, timestamp DESC
LIMIT 10;
");

    $recent_activity = [];
    while ($activity = $activity_query->fetch_assoc()) {
        $recent_activity[] = $activity;
    }

    echo json_encode([
        'status' => 'success',
        'users' => $users_count,
        'orders' => $active_orders,
        'products' => $products_count,
        'revenue' => number_format($total_revenue, 2),
        'recent_activity' => $recent_activity
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error fetching dashboard data'
    ]);
}

$conn->close();
?> 