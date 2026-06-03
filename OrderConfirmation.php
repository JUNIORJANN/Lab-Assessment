<?php session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['customer_id']) || !isset($_GET['id'])) header("Location: CustomerDashboard.php");
$order_id = (int)$_GET['id'];
$customer_id = $_SESSION['customer_id'];
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND customer_id=$customer_id")->fetch_assoc();
if (!$order) die("Order not found.");
$items = $conn->query("SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$order_id");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1>Thank you for your order!</h1>
        <p>Order #<?= $order_id ?> placed on <?= $order['order_date'] ?></p>
        <p>Total: RM <?= number_format($order['total_amount'], 2) ?></p>
        <p>Shipping to: <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
        <p>Payment: <?= $order['payment_method'] ?></p>
        <a href="CustomerDashboard.php">Continue Shopping</a>
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