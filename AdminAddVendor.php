<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: VendorLogin.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $description = $_POST['description'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $image = 'default.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target = "images/vendors/";
        if (!is_dir($target)) mkdir($target, 0777, true);
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target . $image);
    }

    $contact_info = $_POST['contact_info'] ?? '';
    $stmt = $conn->prepare("INSERT INTO vendors (vendor_name, email, password, description, image, is_admin, contact_info) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssis", $vendor_name, $email, $password, $description, $image, $is_admin, $contact_info);
    if ($stmt->execute()) {
        header("Location: AdminDashboard.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Vendor - Admin</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Add New Vendor</h2>
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Vendor Name</label>
            <input type="text" name="vendor_name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <label>Description</label>
            <textarea name="description"></textarea>
            <label>Contact Information</label>
            <textarea name="contact_info" rows="4" placeholder="Phone, email, address..."><?= htmlspecialchars($vendor['contact_info'] ?? '') ?></textarea>
            <label>Image</label>
            <input type="file" name="image">
            <label><input type="checkbox" name="is_admin"> Admin Privileges</label>
            <button type="submit">Create Vendor</button>
        </form>
        <a href="AdminDashboard.php">← Back to Dashboard</a>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(r => r.text())
            .then(d => document.getElementById('navbar').innerHTML = d);

        fetch("Footer.html")
            .then(r => r.text())
            .then(d => document.getElementById('footer').innerHTML = d);
    </script>
</body>

</html>