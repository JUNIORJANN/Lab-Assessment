<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ladanglink';

$conn = mysqli_connect('localhost', 'root', '', 'ladanglink', 3307);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>

