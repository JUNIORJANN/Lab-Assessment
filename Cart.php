<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['customer_id'])) header("Location: CustomerLogin.php");

$customer_id = $_SESSION['customer_id'];

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    // Check if already in cart
    $check = $conn->query("SELECT id FROM cart WHERE customer_id=$customer_id AND product_id=$product_id");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE customer_id=$customer_id AND product_id=$product_id");
    } else {
        $conn->query("INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, $product_id, $quantity)");
    }
    header("Location: Cart.php");
    exit();
}

// Update quantity
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $id => $qty) {
        $qty = max(1, (int)$qty);
        $conn->query("UPDATE cart SET quantity=$qty WHERE id=$id AND customer_id=$customer_id");
    }
    header("Location: Cart.php");
}

// Remove item
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id=$id AND customer_id=$customer_id");
    header("Location: Cart.php");
}

$cart_items = $conn->query("SELECT c.*, p.product_name, p.price, p.image, v.vendor_name, v.id as vendor_id 
    FROM cart c JOIN products p ON c.product_id=p.id JOIN vendors v ON p.vendor_id=v.id 
    WHERE c.customer_id=$customer_id");
$total = 0;
?>
<!DOCTYPE html>
<html>

<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1>Shopping Cart</h1>
        <?php if ($cart_items->num_rows == 0): ?>
            <p>EMPTY <a href="Vendors.php">Start shopping</a></p>
        <?php else: ?>
            <form method="POST">
                <table class="cart-table">
                    <tr>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                    <?php while ($item = $cart_items->fetch_assoc()):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><img src="images/products/<?= $item['image'] ?: 'default.png' ?>" width="50"> <?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['vendor_name']) ?></td>
                            <td>RM <?= number_format($item['price'], 2) ?></td>
                            <td><input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" style="width:60px"></td>
                            <td>RM <?= number_format($subtotal, 2) ?></td>
                            <td><a href="?remove=<?= $item['id'] ?>" onclick="return confirm('Remove item?')">Remove</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <p><strong>Total: RM <?= number_format($total, 2) ?></strong></p>
                <button type="submit" name="update">Update Cart</button>
                <a href="Checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </form>
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