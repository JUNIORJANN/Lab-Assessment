<?php session_start(); require_once 'LadangLinkDB.php'; if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");
$vendor_id = $_SESSION['vendor_id'];
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND vendor_id=?");
    $stmt->bind_param("ii", $id, $vendor_id);
    $stmt->execute();
    header("Location: MyProduct.php");
}
$result = $conn->query("SELECT * FROM products WHERE vendor_id = $vendor_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>My Products</title><link rel="stylesheet" href="LadangStyle.css"></head>
<body>
<div id="navbar"></div>
<div class="container">
    <h1>My Products</h1>
    <div class="product-grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="images/products/<?= $row['image'] ?: 'default.png' ?>">
                <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                <p>RM <?= number_format($row['price'], 2) ?></p>
                <p>Stock: <?= $row['stock'] ?></p>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <a href="EditProduct.php?id=<?= $row['id'] ?>">Edit</a> <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
    <a href="AddProduct.php" class="dashboard-card" style="display:inline-block;">+ Add New Product</a>
</div>
<div id="footer"></div>
<script>fetch("Navbar.html").then(r=>r.text()).then(d=>document.getElementById('navbar').innerHTML=d);fetch("Footer.html").then(r=>r.text()).then(d=>document.getElementById('footer').innerHTML=d);</script>
</body>
</html>