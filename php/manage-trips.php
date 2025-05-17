<?php
require_once 'connection.php';
session_start();


header('Content-Type: application/json');

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            addTrip($conn);
            break;
        case 'update':
            updateTrip($conn);
            break;
        case 'delete':
            deleteTrip($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['trip_id'])) {
        // Fetch single trip
        getTrip($conn, $_GET['trip_id']);
    } else {
        // Fetch trips list
        getTrips($conn);
    }
}

// Function to get a single trip
function getTrip($conn, $trip_id) {
    $stmt = $conn->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Trip not found']);
    }
    $stmt->close();
}

// Function to get all trips
function getTrips($conn) {
    $stmt = $conn->prepare("SELECT * FROM trips ORDER BY trip_id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $trips]);
    $stmt->close();
}

// Function to handle image upload
function handleImageUpload() {
    // If no image is uploaded, return null
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        return null;
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

// Function to add new trip
function addTrip($conn) {
    try {
        $trip_name = filter_var($_POST['trip_name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $destination = filter_var($_POST['destination'], FILTER_SANITIZE_STRING);
        $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
        $capacity = filter_var($_POST['capacity'], FILTER_SANITIZE_NUMBER_INT);
        $trip_price = filter_var($_POST['trip_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $trip_start_data = filter_var($_POST['trip_start_data'], FILTER_SANITIZE_STRING);

        // Handle image upload if exists
        $image_name = handleImageUpload();

        $sql = "INSERT INTO trips (trip_name, description, Destination, duration, capacity, trip_price, trip_start_data";
        $params = [$trip_name, $description, $destination, $duration, $capacity, $trip_price, $trip_start_data];
        $types = "sssiids";

        if ($image_name !== null) {
            $sql .= ", image_name";
            $params[] = $image_name;
            $types .= "s";
        }

        $sql .= ") VALUES (" . str_repeat("?,", count($params)-1) . "?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Trip added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add trip']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Function to update trip
function updateTrip($conn) {
    try {
        $trip_id = filter_var($_POST['trip_id'], FILTER_SANITIZE_NUMBER_INT);
        $trip_name = filter_var($_POST['trip_name'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $destination = filter_var($_POST['destination'], FILTER_SANITIZE_STRING);
        $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
        $capacity = filter_var($_POST['capacity'], FILTER_SANITIZE_NUMBER_INT);
        $trip_price = filter_var($_POST['trip_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $trip_start_data = filter_var($_POST['trip_start_data'], FILTER_SANITIZE_STRING);

        $sql = "UPDATE trips SET trip_name=?, description=?, Destination=?, duration=?, capacity=?, trip_price=?, trip_start_data=?";
        $params = [$trip_name, $description, $destination, $duration, $capacity, $trip_price, $trip_start_data];
        $types = "sssiids";

        // Update image if new one is uploaded
        $image_name = handleImageUpload();
        if ($image_name !== null) {
            $sql .= ", image_name=?";
            $params[] = $image_name;
            $types .= "s";
        }

        $sql .= " WHERE trip_id=?";
        $params[] = $trip_id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Trip updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update trip']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Function to delete trip
function deleteTrip($conn) {
    $trip_id = filter_var($_POST['trip_id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Delete old image
    $stmt = $conn->prepare("SELECT image_name FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = "../img/" . $row['image_name'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Trip deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete trip']);
    }
    $stmt->close();
}

$conn->close();
?> 