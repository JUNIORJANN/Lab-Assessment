<?php session_start(); require_once 'LadangLinkDB.php';
if (!isset($_SESSION['customer_id'])) header("Location: CustomerLogin.php");
$customer_id = $_SESSION['customer_id'];
$orders = $conn->query("SELECT * FROM orders WHERE customer_id=$customer_id ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head><title>My Orders</title><link rel="stylesheet" href="LadangStyle.css"></head>
<body>
<div id="navbar"></div>
<div class="container">
    <h1>My Orders</h1>
    <?php if($orders->num_rows == 0): ?>
        <p>No orders yet.</p>
    <?php else: ?>
        <table class="cart-table">
            <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr>
            <?php while($o = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= $o['order_date'] ?></td>
                    <td>RM <?= number_format($o['total_amount'],2) ?></td>
                    <td><?= $o['status'] ?></td>
                    <td><a href="OrderConfirmation.php?id=<?= $o['id'] ?>">View</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>
<div id="footer"></div>
<script>fetch("Navbar.html").then(r=>r.text()).then(d=>document.getElementById('navbar').innerHTML=d);fetch("Footer.html").then(r=>r.text()).then(d=>document.getElementById('footer').innerHTML=d);</script>
</body>
</html>