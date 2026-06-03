<?php session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_SESSION['vendor_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $available_from = !empty($_POST['available_from']) ? $_POST['available_from'] : null;
    $available_until = !empty($_POST['available_until']) ? $_POST['available_until'] : null;
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target = "images/products/";
        if (!is_dir($target)) mkdir($target, 0777, true);
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target . $image);
    }
    $stmt = $conn->prepare("INSERT INTO products (vendor_id, product_name, price, description, image, stock, available_from, available_until) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssiss", $vendor_id, $product_name, $price, $description, $image, $stock, $available_from, $available_until);
    $stmt->execute();
    header("Location: MyProduct.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Product Name</label><input type="text" name="product_name" required>
            <label>Price (RM)</label><input type="number" step="0.01" name="price" required>
            <label>Stock Quantity</label><input type="number" name="stock" min="0" value="0" required>
            <label>Seasonal Availability (optional)</label>
            <input type="date" name="available_from"> to <input type="date" name="available_until">
            <label>Description</label><textarea name="description"></textarea>
            <label>Product Image</label><input type="file" name="image" accept="image/*">
            <button type="submit">Add Product</button>
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