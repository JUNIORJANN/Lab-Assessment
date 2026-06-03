<?php
require_once 'LadangLinkDB.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $description = $_POST['description'] ?? '';

    $image = 'default.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target = "images/vendors/";
        if (!is_dir($target)) mkdir($target, 0777, true);
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target . $image);
    }

    $stmt = $conn->prepare("INSERT INTO vendors (vendor_name, email, password, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $vendor_name, $email, $password, $description, $image);
    if ($stmt->execute()) {
        header("Location: VendorLogin.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>