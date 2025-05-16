<?php
$host = 'localhost';
$dbname = 'galaxy x';
$username = 'root';
$password = '';

try {
    $conn =mysqli_connect($host,$username,$password,$dbname);
} catch (mysqli_sql_exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>