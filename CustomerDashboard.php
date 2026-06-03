<?php session_start();
if (!isset($_SESSION['customer_id'])) header("Location: CustomerLogin.php");
require_once 'LadangLinkDB.php';
$vendors = $conn->query("SELECT * FROM vendors WHERE is_admin = 0");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1 style="text-align:center;">Welcome, <?= htmlspecialchars($_SESSION['customer_name']); ?>!</h1>
        <div style="text-align:center; margin-bottom:20px;">
            <a href="Cart.php" class="view-btn">🛒 My Cart</a>
            <a href="MyOrders.php" class="view-btn">📦 My Orders</a>
        </div>
        <h2 style="text-align:center;">Our Collaboration Vendors</h2>
        <div class="vendor-grid">
            <?php while ($v = $vendors->fetch_assoc()): ?>
                <div class="vendor-card">
                    <img src="images/vendors/<?= $v['image'] ?: 'default.png' ?>">
                    <h3><?= htmlspecialchars($v['vendor_name']) ?></h3>
                    <p><?= htmlspecialchars($v['description']) ?></p>
                    <a href="VendorStore.php?id=<?= $v['id'] ?>" class="view-btn">View Store</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(r => r.text())
            .then(d => document.getElementById('navbar').innerHTML = d);

        fetch("Footer.html")
            .then(r => r.text())
        then(d => document.getElementById('footer').innerHTML = d);
    </script>
</body>

</html>