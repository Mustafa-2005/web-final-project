<?php
$host = 'localhost';
$dbname = 'galaxy x';
$username = 'root';
$password = '';

try {
    $conn =mysqli_connect($host,$username,$password,$dbname);
} catch (mysqli_connect_error) {
    echo "Connection failed:";}
?>