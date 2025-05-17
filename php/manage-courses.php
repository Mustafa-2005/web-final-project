<?php
require_once 'connection.php';
session_start();

header('Content-Type: application/json');

// Handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            addCourse($conn);
            break;
        case 'update':
            updateCourse($conn);
            break;
        case 'delete':
            deleteCourse($conn);
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['course_id'])) {
        // Fetch single course
        getCourse($conn, $_GET['course_id']);
    } else {
        // Fetch courses list
        getCourses($conn);
    }
}

// Function to get a single course
function getCourse($conn, $course_id) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Course not found']);
    }
    $stmt->close();
}

// Function to get all courses
function getCourses($conn) {
    $stmt = $conn->prepare("SELECT * FROM courses ORDER BY course_id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $courses]);
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

// Function to add new course
function addCourse($conn) {
    try {
        $course_title = filter_var($_POST['course_title'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
        $course_price = filter_var($_POST['course_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Handle image upload if exists
        $image_name = handleImageUpload();

        $sql = "INSERT INTO courses (course_title, description, duration, course_price";
        $params = [$course_title, $description, $duration, $course_price];
        $types = "ssid";

        if ($image_name !== null) {
            $sql .= ", image_name";
            $params[] = $image_name;
            $types .= "s";
        }

        $sql .= ") VALUES (" . str_repeat("?,", count($params)-1) . "?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add course']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Function to update course
function updateCourse($conn) {
    try {
        $course_id = filter_var($_POST['course_id'], FILTER_SANITIZE_NUMBER_INT);
        $course_title = filter_var($_POST['course_title'], FILTER_SANITIZE_STRING);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
        $course_price = filter_var($_POST['course_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $sql = "UPDATE courses SET course_title=?, description=?, duration=?, course_price=?";
        $params = [$course_title, $description, $duration, $course_price];
        $types = "ssid";

        // Update image if new one is uploaded
        $image_name = handleImageUpload();
        if ($image_name !== null) {
            // Delete old image first
            $stmt = $conn->prepare("SELECT image_name FROM courses WHERE course_id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $old_image = $row['image_name'];
                if ($old_image && file_exists("../img/" . $old_image)) {
                    unlink("../img/" . $old_image);
                }
            }
            $stmt->close();

            $sql .= ", image_name=?";
            $params[] = $image_name;
            $types .= "s";
        }

        $sql .= " WHERE course_id=?";
        $params[] = $course_id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update course']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Function to delete course
function deleteCourse($conn) {
    try {
        $course_id = filter_var($_POST['course_id'], FILTER_SANITIZE_NUMBER_INT);
        
        // Start transaction
        $conn->begin_transaction();
        
        // Delete course image if exists
        $stmt = $conn->prepare("SELECT image_name FROM courses WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $image_path = "../img/" . $row['image_name'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt->close();
        
        // Delete related records from user_courses
        $user_courses_stmt = $conn->prepare("DELETE FROM user_courses WHERE course_id = ?");
        $user_courses_stmt->bind_param("i", $course_id);
        $user_courses_stmt->execute();
        $user_courses_stmt->close();
        
        // Delete the course
        $course_stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $course_stmt->bind_param("i", $course_id);
        
        if ($course_stmt->execute()) {
            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully']);
        } else {
            throw new Exception('Failed to delete course');
        }
        $course_stmt->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

$conn->close();
?> 