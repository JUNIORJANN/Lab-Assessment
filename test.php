<?php
$conn = mysqli_connect("localhost", "root", "");

if (!$conn) {
    die("Failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully!";
}
?>