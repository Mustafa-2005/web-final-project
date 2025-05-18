<?php
include 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];

    // ==== Input Validation ====

    // التحقق من الاسم
    if (strlen($username) < 3) {
    echo "<script>alert('Name must be at least 3 characters long.'); window.history.back();</script>";
        exit;
    }

    // التحقق من كلمة السر
    if (strlen($password) < 5 || !preg_match('/[a-zA-Z]/', $password)) {
        echo "<script>alert('Password must be at least 5 characters long and contain at least one letter.'); window.history.back();</script>";
        exit;
    }

    // التحقق من البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit;
    }

    // ==== Check if email already exists ====
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email is already registered!'); window.history.back();</script>";
        exit;
    }

    // ==== Hash password and insert new user ====
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, address, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $address, $hashed_password);

    if ($stmt->execute()) {
        header("Location:../html/login.html");
        exit;
    } else {
        echo "Something went wrong.";
    }

    $stmt->close();
    $conn->close();
}
?>
