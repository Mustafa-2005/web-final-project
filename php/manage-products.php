<?php
require_once 'connection.php';
session_start();


// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            addProduct($conn);
            break;
        case 'update':
            updateProduct($conn);
            break;
        case 'delete':
            deleteProduct($conn);
            break;
        case 'update_stock':
            updateStock($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['product_id'])) {
        // Fetch single product
        getProduct($conn, $_GET['product_id']);
    } else {
        // Fetch products list
        getProducts($conn);
    }
}

// Function to add new product
function addProduct($conn) {
    $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
    $categore = filter_var($_POST['categore'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $in_stock = filter_var($_POST['in_stock'], FILTER_SANITIZE_NUMBER_INT);
    $product_price = filter_var($_POST['product_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Handle image upload
    $image_name = handleImageUpload();

    $stmt = $conn->prepare("INSERT INTO store (product_name, categore, description, image_name, in_stock, product_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssid", $product_name, $categore, $description, $image_name, $in_stock, $product_price);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add product']);
    }
    $stmt->close();
}

// Function to update product details
function updateProduct($conn) {
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $product_name = filter_var($_POST['product_name'], FILTER_SANITIZE_STRING);
    $categore = filter_var($_POST['categore'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $in_stock = filter_var($_POST['in_stock'], FILTER_SANITIZE_NUMBER_INT);
    $product_price = filter_var($_POST['product_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $sql = "UPDATE store SET product_name=?, categore=?, description=?, in_stock=?, product_price=?";
    $params = [$product_name, $categore, $description, $in_stock, $product_price];
    $types = "sssid";

    // Update image if new one is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = handleImageUpload();
        $sql .= ", image_name=?";
        $params[] = $image_name;
        $types .= "s";
    }

    $sql .= " WHERE product_id=?";
    $params[] = $product_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
    }
    $stmt->close();
}

// Function to delete product
function deleteProduct($conn) {
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Delete old image
    $stmt = $conn->prepare("SELECT image_name FROM store WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = "../img/" . $row['image_name'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM store WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
    }
    $stmt->close();
}

// Function to update stock
function updateStock($conn) {
    $product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $in_stock = filter_var($_POST['in_stock'], FILTER_SANITIZE_NUMBER_INT);
    
    $stmt = $conn->prepare("UPDATE store SET in_stock = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $in_stock, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Stock updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update stock']);
    }
    $stmt->close();
}

// Function to get products list
function getProducts($conn) {
    $stmt = $conn->prepare("SELECT * FROM store ORDER BY product_id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $products]);
    $stmt->close();
}

// Function to get a single product
function getProduct($conn, $product_id) {
    $stmt = $conn->prepare("SELECT * FROM store WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
    }
    $stmt->close();
}

// Function to handle image upload
function handleImageUpload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception('Error uploading image');
    }

    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('File type not allowed');
    }

    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        throw new Exception('File size too large');
    }

    $image_name = uniqid() . '_' . $file['name'];
    $upload_path = "../img/" . $image_name;

    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload image');
    }

    return $image_name;
}

$conn->close();
?> 