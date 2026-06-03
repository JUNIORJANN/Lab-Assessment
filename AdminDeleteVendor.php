<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: VendorLogin.html");
    exit();
}

$id = $_GET['id'] ?? 0;
if ($id == $_SESSION['vendor_id']) {
    die("You cannot delete your own admin account.");
}

$stmt = $conn->prepare("DELETE FROM vendors WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: AdminDashboard.php");
exit();
?>