<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: VendorLogin.html");
    exit();
}

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM vendors WHERE id = $id");
$vendor = $result->fetch_assoc();
if (!$vendor) die("Vendor not found.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $email = $_POST['email'];
    $description = $_POST['description'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $password_sql = "";
    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password = '$new_password'";
    }

    $image_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target = "images/vendors/";
        if (!is_dir($target)) mkdir($target, 0777, true);
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target . $image);
        $image_sql = ", image = '$image'";
    }

    $contact_info = $_POST['contact_info'] ?? '';

    // Use prepared statement to avoid SQL injection
    $update_sql = "UPDATE vendors SET vendor_name=?, email=?, description=?, contact_info=?, is_admin=?";
    $params = [$vendor_name, $email, $description, $contact_info, $is_admin];
    $types = "ssssi";

    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_sql .= ", password=?";
        $params[] = $new_password;
        $types .= "s";
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target = "images/vendors/";
        if (!is_dir($target)) mkdir($target, 0777, true);
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target . $image);
        $update_sql .= ", image=?";
        $params[] = $image;
        $types .= "s";
    }
    $update_sql .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Page</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Edit Vendor</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Name</label>
            <input type="text" name="vendor_name" value="<?= htmlspecialchars($vendor['vendor_name']) ?>" required>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($vendor['email']) ?>" required>
            <label>New Password (Empty to Keep Current)</label>
            <input type="password" name="password">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($vendor['description']) ?></textarea>
            <label>Contact Information</label>
            <textarea name="contact_info" rows="4" placeholder="Phone, email, address..."><?= htmlspecialchars($vendor['contact_info'] ?? '') ?></textarea>
            <label>Current Image</label><br>
            <img src="images/vendors/<?= $vendor['image'] ?: 'default.png' ?>" width="280"><br>
            <label>Change Image</label>
            <input type="file" name="image">
            <label><input type="checkbox" name="is_admin" <?= $vendor['is_admin'] ? 'checked' : '' ?>> Admin Privileges</label>
            <button type="submit">Update</button>
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