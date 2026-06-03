<?php
require_once 'LadangLinkDB.php';
$result = $conn->query("SELECT id, vendor_name, description, image FROM vendors WHERE is_admin = 0");
$vendors = [];
while ($row = $result->fetch_assoc()) {
    $vendors[] = $row;
}
header('Content-Type: application/json');
echo json_encode($vendors);
?>