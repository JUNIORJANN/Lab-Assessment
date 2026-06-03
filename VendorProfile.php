<?php session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");
$vendor_id = $_SESSION['vendor_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $description = $_POST['description'];
    $contact_info = $_POST['contact_info'];
    $stmt = $conn->prepare("UPDATE vendors SET vendor_name=?, description=?, contact_info=? WHERE id=?");
    $stmt->bind_param("sssi", $vendor_name, $description, $contact_info, $vendor_id);
    $stmt->execute();
    $_SESSION['vendor_name'] = $vendor_name;
    header("Location: VendorDashboard.php");
    exit();
}
$result = $conn->query("SELECT * FROM vendors WHERE id = $vendor_id");
$vendor = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Edit Store Profile</h2>
        <form method="POST">
            <label>Store Name</label>
            <input type="text" name="vendor_name" value="<?= htmlspecialchars($vendor['vendor_name']) ?>" required>
            <label>Description</label><textarea name="description"><?= htmlspecialchars($vendor['description']) ?></textarea>
            <label>Contact Information (phone, address, email)</label>
            <textarea name="contact_info" rows="4"><?= htmlspecialchars($vendor['contact_info']) ?></textarea>
            <button type="submit">Update Profile</button>
        </form>
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