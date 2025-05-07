<?php
include 'connection.php'; 

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
            header("Location: ../html/login.html");
        }
        
    } else {
        echo "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>


