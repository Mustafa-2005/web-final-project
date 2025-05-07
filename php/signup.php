<?php
include 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $address=trim($_POST["address"]);
    $password = $_POST["password"];

    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email is already registered!";
        exit;
    }

    
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
