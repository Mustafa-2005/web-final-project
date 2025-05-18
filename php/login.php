<?php
require 'connection.php'; 
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === 'admin') {
                header(header: "Location: ../html/admin-dashboard.html");
                exit;
            } else {   
                header("Location: ../html/after-login.html");
                exit;
        }}else{
         echo "<script>alert('Incorrect email or password. Please try again.');window.history.back();</script>"; 
         exit;       
    }
        
    } else {
         echo "<script>alert('Incorrect email or password. Please try again.');window.history.back();</script>";        
        }
    

    $stmt->close();
    $conn->close();
}
?>


