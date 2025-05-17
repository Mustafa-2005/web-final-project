<?php
require_once 'connection.php';
session_start();


header('Content-Type: application/json');

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            addUser($conn);
            break;
        case 'update':
            updateUser($conn);
            break;
        case 'delete':
            deleteUser($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['user_id'])) {
        // Get specific user data
        $user_id = filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("SELECT user_id, name, email, role, address FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['status' => 'success', 'data' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
        $stmt->close();
    } else {
        // Fetch users list
        getUsers($conn);
    }
}

// Add new user
function addUser($conn) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'customer';
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $address);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add user']);
    }
    $stmt->close();
}

// Update user details
function updateUser($conn) {
    $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'customer';
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);

    $sql = "UPDATE users SET name=?, email=?, role=?, address=?";
    $params = [$name, $email, $role, $address];
    $types = "ssss";

    // Update password if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password=?";
        $params[] = $password;
        $types .= "s";
    }

    $sql .= " WHERE user_id=?";
    $params[] = $user_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
    }
    $stmt->close();
}

// Delete user
function deleteUser($conn) {
    try {
        $conn->begin_transaction();
        
        $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $current_admin_id = $_SESSION['user_id'];
        
        // Check if trying to delete current admin
        if ($user_id == $current_admin_id) {
            throw new Exception('Cannot delete your own account');
        }

        // Check if user exists
        $check_stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if (!$result->fetch_assoc()) {
            throw new Exception('User not found');
        }
        $check_stmt->close();

        // Delete related records from payments through orders
        $order_items_stmt = $conn->prepare("
            DELETE oi FROM payments oi 
            INNER JOIN orders o ON oi.order_id = o.order_id 
            WHERE o.user_id = ?
        ");
        $order_items_stmt->bind_param("i", $user_id);
        $order_items_stmt->execute();
        $order_items_stmt->close();


        // Delete related records from order_item through orders
        $order_items_stmt = $conn->prepare("
            DELETE oi FROM order_item oi 
            INNER JOIN orders o ON oi.order_id = o.order_id 
            WHERE o.user_id = ?
        ");
        $order_items_stmt->bind_param("i", $user_id);
        $order_items_stmt->execute();
        $order_items_stmt->close();

        // Delete orders
        $orders_stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $orders_stmt->bind_param("i", $user_id);
        $orders_stmt->execute();
        $orders_stmt->close();

        // Delete the user (allowing deletion of admins except current one)
        $user_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND user_id != ?");
        $user_stmt->bind_param("ii", $user_id, $current_admin_id);
        if (!$user_stmt->execute() || $user_stmt->affected_rows === 0) {
            throw new Exception('Failed to delete user');
        }
        $user_stmt->close();

        // Commit the transaction
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);

    } catch (Exception $e) {
        // Rollback the transaction
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Get users list
function getUsers($conn) {
    $stmt = $conn->prepare("SELECT user_id, name, email, role, address FROM users ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $users]);
    $stmt->close();
}

$conn->close();
?> 