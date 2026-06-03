<?php
session_start();
require_once 'LadangLinkDB.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, vendor_name, password, is_admin FROM vendors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['vendor_id'] = $row['id'];
            $_SESSION['vendor_name'] = $row['vendor_name'];
            $_SESSION['is_admin'] = $row['is_admin'];
            if ($row['is_admin'] == 1) header("Location: AdminDashboard.php");
            else header("Location: VendorDashboard.php");
            exit();
        }
    }
    echo "<script>alert('Invalid email or password'); window.location='VendorLogin.html';</script>";
}
?>