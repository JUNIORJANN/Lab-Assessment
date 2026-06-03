<?php session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");
$id = $_GET['id'];
$vendor_id = $_SESSION['vendor_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $available_from = !empty($_POST['available_from']) ? $_POST['available_from'] : null;
    $available_until = !empty($_POST['available_until']) ? $_POST['available_until'] : null;
    $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, description=?, stock=?, available_from=?, available_until=? WHERE id=? AND vendor_id=?");
    $stmt->bind_param("sdssssii", $product_name, $price, $description, $stock, $available_from, $available_until, $id, $vendor_id);
    $stmt->execute();
    header("Location: MyProduct.php");
}
$result = $conn->query("SELECT * FROM products WHERE id = $id AND vendor_id = $vendor_id");
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Edit Product</h2>
        <form method="POST">
            <label>Product Name</label><input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            <label>Price (RM)</label><input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
            <label>Stock Quantity</label><input type="number" name="stock" value="<?= $product['stock'] ?>" min="0" required>
            <label>Seasonal Availability</label>
            <input type="date" name="available_from" value="<?= $product['available_from'] ?>"> to <input type="date" name="available_until" value="<?= $product['available_until'] ?>">
            <label>Description</label><textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
            <button type="submit">Update Product</button>
        </form>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(r => r.text())
            .then(d => document.getElementById('navbar').innerHTML = d);

        fetch("Footer.html")
        then(r => r.text())
            .then(d => document.getElementById('footer').innerHTML = d);
    </script>
</body>

</html>