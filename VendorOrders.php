<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");

$vendor_id = $_SESSION['vendor_id'];

if (isset($_GET['update_status']) && isset($_GET['order_id']) && isset($_GET['new_status'])) {
    $order_id = (int)$_GET['order_id'];
    $new_status = $_GET['new_status'];
    $allowed = ['processing', 'completed', 'cancelled'];
    if (in_array($new_status, $allowed)) {
        $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
        header("Location: VendorOrders.php");
        exit();
    }
}

$orders = $conn->query("
    SELECT DISTINCT o.id, o.order_date, o.total_amount, o.status, o.shipping_address,
           c.name as customer_name
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN customers c ON o.customer_id = c.id
    WHERE oi.vendor_id = $vendor_id
    ORDER BY o.order_date DESC
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Manage Orders - Vendor Dashboard</title>
    <link rel="stylesheet" href="LadangStyle.css">
    <style>
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .orders-table th,
        .orders-table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .orders-table th {
            background: #2e7d32;
            color: white;
            font-weight: 600;
        }

        .orders-table tr:hover {
            background: #f1f8e9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff3e0;
            color: #e65100;
        }

        .status-processing {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }

        .status-select {
            padding: 6px 10px;
            border-radius: 30px;
            border: 1px solid #c8e6c9;
            background: white;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .back-button {
            display: inline-block;
            margin-top: 25px;
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 10px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .back-button:hover {
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            transform: translateY(-2px);
        }

        .empty-orders {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 32px;
            color: #1b5e20;
        }

        @media (max-width: 768px) {

            .orders-table th,
            .orders-table td {
                padding: 8px 6px;
                font-size: 0.85rem;
            }

            .status-select {
                padding: 4px 6px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1>📦 Manage My Store Orders</h1>
        <?php if ($orders->num_rows == 0): ?>
            <div class="empty-orders">
                <p>No orders have been placed for your products yet.</p>
            </div>
        <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($o = $orders->fetch_assoc()):
                        $status_class = '';
                        switch ($o['status']) {
                            case 'pending':
                                $status_class = 'status-pending';
                                break;
                            case 'processing':
                                $status_class = 'status-processing';
                                break;
                            case 'completed':
                                $status_class = 'status-completed';
                                break;
                            case 'cancelled':
                                $status_class = 'status-cancelled';
                                break;
                        }
                    ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= htmlspecialchars($o['customer_name']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($o['order_date'])) ?></td>
                            <td>RM <?= number_format($o['total_amount'], 2) ?></td>
                            <td><span class="status-badge <?= $status_class ?>"><?= ucfirst($o['status']) ?></span></td>
                            <td>
                                <select class="status-select" onchange="if(this.value) window.location='?update_status=1&order_id=<?= $o['id'] ?>&new_status='+this.value">
                                    <option value="">Change status</option>
                                    <option value="processing" <?= $o['status'] == 'processing' ? 'disabled' : '' ?>>Processing</option>
                                    <option value="completed" <?= $o['status'] == 'completed' ? 'disabled' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'disabled' : '' ?>>Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <button class="back-button" onclick="goToDashboard()">← Back to Dashboard</button>
    </div>
    <div id="footer"></div>
    <script>
        function goToDashboard() {
            if (window.location.href.includes('VendorOrders.php')) {
                window.location.href = 'VendorDashboard.php';
            } else {
                window.location.href = 'VendorDashboard.php';
            }
        }

        document.querySelector('.back-button')?.addEventListener('click', function(e) {
            window.location.href = 'VendorDashboard.php';
        });

        fetch("Navbar.html")
            .then(r => r.text())
            .then(d => document.getElementById('navbar').innerHTML = d);

        fetch("Footer.html")
            .then(r => r.text())
            .then(d => document.getElementById('footer').innerHTML = d);
    </script>
</body>

</html>