<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['customer_id'])) header("Location: CustomerLogin.php");
$customer_id = $_SESSION['customer_id'];

$cart_items = $conn->query("SELECT c.*, p.product_name, p.price, p.vendor_id FROM cart c JOIN products p ON c.product_id=p.id WHERE c.customer_id=$customer_id");
if ($cart_items->num_rows == 0) header("Location: Cart.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $payment = $_POST['payment_method'];
    // Calculate total
    $total = 0;
    $items = [];
    while ($item = $cart_items->fetch_assoc()) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $items[] = $item;
    }
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $customer_id, $total, $address, $payment);
    $stmt->execute();
    $order_id = $conn->insert_id;
    // Insert order items and reduce stock
    foreach ($items as $item) {
        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, vendor_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiidd", $order_id, $item['product_id'], $item['vendor_id'], $item['quantity'], $item['price']);
        $stmt2->execute();
        // Reduce stock
        $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}");
    }
    // Clear cart
    $conn->query("DELETE FROM cart WHERE customer_id=$customer_id");
    header("Location: OrderConfirmation.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="form-container">
        <h2>Checkout</h2>
        <form method="POST">
            <label>Shipping Address</label>
            <textarea name="address" rows="3" required></textarea>
            <label>Payment Method</label>
            <select name="payment_method">
                <option>Cash on Delivery</option>
                <option>Bank Transfer (Manual)</option>
            </select>
            <button type="submit">Place Order</button>
        </form>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(r => r.text())
        then(d => document.getElementById('navbar').innerHTML = d);

        fetch("Footer.html")
        then(r => r.text())
            .then(d => document.getElementById('footer').innerHTML = d);
    </script>
</body>

</html>