<?php
require_once 'connection.php';
session_start();

// Mock user ID (replace with actual session login check)
$user_id = $_SESSION['user_id'] ?? 1; // fallback for demo

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, address=?  WHERE user_id=?");
    $stmt->bind_param("sssi", $name, $email, $address, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../html/user-dashboard.html");
    exit();
}

// Handle fetch request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT  name, email, address  FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    echo json_encode($result);
}

$conn->close();
?>
