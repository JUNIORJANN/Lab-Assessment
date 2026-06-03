<?php
require_once 'LadangLinkDB.php';

$vendor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($vendor_id <= 0) {
    die("Invalid vendor ID.");
}

$stmt = $conn->prepare("SELECT * FROM vendors WHERE id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();

if (!$vendor || $vendor['is_admin'] == 1) {
    die("Vendor/Store not found.");
}

$today = date('Y-m-d');
$announcements = $conn->query("
    SELECT * FROM announcements 
    WHERE vendor_id = $vendor_id 
    AND (start_date IS NULL OR start_date <= '$today')
    AND (end_date IS NULL OR end_date >= '$today')
    ORDER BY created_at DESC LIMIT 3
");

$stmt2 = $conn->prepare("SELECT * FROM products WHERE vendor_id = ? AND (available_from IS NULL OR available_from <= CURDATE()) AND (available_until IS NULL OR available_until >= CURDATE())");
$stmt2->bind_param("i", $vendor_id);
$stmt2->execute();
$products = $stmt2->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($vendor['vendor_name']) ?> - Store</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1><?= htmlspecialchars($vendor['vendor_name']) ?></h1>
        <img src="images/vendors/<?= htmlspecialchars($vendor['image'] ?: 'default.png') ?>" width="150">
        <p><?= htmlspecialchars($vendor['description']) ?></p>

        <?php if (!empty($vendor['contact_info'])): ?>
            <div class="contact-info-box">
                <h3>Contact Information</h3>
                <p><?= nl2br(htmlspecialchars($vendor['contact_info'])) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($announcements->num_rows > 0): ?>
            <div class="announcements">
                <h3>Announcements</h3>
                <?php while ($a = $announcements->fetch_assoc()): ?>
                    <div><strong><?= htmlspecialchars($a['title']) ?></strong><br><?= nl2br(htmlspecialchars($a['message'])) ?></div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <h2>Products</h2>
        <div class="product-grid">
            <?php if ($products->num_rows > 0): ?>
                <?php while ($prod = $products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="images/products/<?= htmlspecialchars($prod['image'] ?: 'default.png') ?>">
                        <h3><?= htmlspecialchars($prod['product_name']) ?></h3>
                        <p>RM <?= number_format($prod['price'], 2) ?></p>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $prod['stock'] ?>" style="width:60px">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(res => res.text())
            .then(data => document.getElementById('navbar').innerHTML = data);

        fetch("Footer.html")
            .then(res => res.text())
            .then(data => document.getElementById('footer').innerHTML = data);
    </script>
</body>

</html>