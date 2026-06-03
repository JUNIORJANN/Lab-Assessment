<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'LadangLinkDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required. <a href='customer_register.html'>Go back</a>");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
    if (!$check) {
        die("Database prepare error: " . $conn->error);
    }
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already registered. <a href='CustomerLogin.php'>Login here</a>");
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO customers (name, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    if ($stmt->execute()) {
        header("Location: CustomerLogin.php?registered=1");
        exit();
    } else {
        die("Insert error: " . $stmt->error);
    }
    $stmt->close();
} else {
    header("Location: CustomerRegister.html");
    exit();
}
?>