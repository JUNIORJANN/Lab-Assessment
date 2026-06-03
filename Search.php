<?php
require_once 'LadangLinkDB.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($query)) {
    $search_term = "%$query%";
    $stmt = $conn->prepare("
        SELECT p.*, v.vendor_name, v.id as vendor_id 
        FROM products p
        JOIN vendors v ON p.vendor_id = v.id
        WHERE v.is_admin = 0 
        AND (p.product_name LIKE ? OR p.description LIKE ?)
        ORDER BY p.product_name
    ");
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>LadangLink</title>
    <link rel="stylesheet" href="LadangStyle.css">
    <style>
        .search-header {
            text-align: center;
            margin: 20px 0;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 30px;
        }
    </style>
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <div class="search-header">
            <h1>Search Results</h1>
            <?php if (!empty($query)): ?>
                <p>Showing results for: <strong><?= htmlspecialchars($query) ?></strong></p>
            <?php endif; ?>
        </div>

        <?php if (empty($query)): ?>
            <div class="no-results">
                <p>Please enter a search term.</p>
            </div>
        <?php elseif ($results->num_rows === 0): ?>
            <div class="no-results">
                <p>😕 No products found matching "<strong><?= htmlspecialchars($query) ?></strong>".</p>
                <p>Try different keywords or browse our <a href="Vendors.php">vendors</a>.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php while ($product = $results->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="images/products/<?= htmlspecialchars($product['image'] ?: 'default.png') ?>">
                        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p class="price">RM <?= number_format($product['price'], 2) ?></p>
                        <p><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</p>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" style="width:60px">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                        <p class="vendor-link">Sold by:
                            <a href="VendorStore.php?id=<?= $product['vendor_id'] ?>">
                                <?= htmlspecialchars($product['vendor_name']) ?>
                            </a>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
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