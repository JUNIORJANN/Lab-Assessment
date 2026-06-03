<?php
session_start();
require_once 'LadangLinkDB.php';

if (!isset($_SESSION['vendor_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: VendorLogin.html");
    exit();
}

$vendors = $conn->query("SELECT * FROM vendors ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard - Manage Vendors</title>
    <link rel="stylesheet" href="LadangStyle.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .admin-table th,
        .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .admin-table th {
            background-color: #2e7d32;
            color: white;
        }

        .admin-table tr:hover {
            background-color: #f1f8e9;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.8rem;
            margin: 0 2px;
        }

        .edit-btn {
            background: #ff9800;
            color: #1b3b1a;
        }

        .delete-btn {
            background: #f44336;
            color: white;
        }

        .add-btn {
            background: #2e7d32;
            color: white;
            display: inline-block;
            margin-bottom: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        h1 {
            color: #2e7d32;
        }
    </style>
</head>

<body>
    <div id="navbar"></div>
    <div class="container">
        <h1>Admin Dashboard & Vendor Management</h1>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>E-MAIL</th>
                    <th>DESCRIPTION</th>
                    <th>CONTACT INFO</th>
                    <th>IMAGE</th>
                    <th>ADMIN</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <a href="AdminAddVendor.php" class="action-btn add-btn">+ Add New Vendor</a>

            <tbody>
                <?php while ($v = $vendors->fetch_assoc()): ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><?= htmlspecialchars($v['vendor_name']) ?></td>
                        <td><?= htmlspecialchars($v['email']) ?></td>
                        <td><?= htmlspecialchars(substr($v['description'], 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars(substr($v['contact_info'], 0, 60)) ?>...</td>
                        <td><img src="images/vendors/<?= $v['image'] ?: 'default.png' ?>" width="40" height="40" style="border-radius: 50%;"></td>
                        <td><?= $v['is_admin'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="AdminEditVendor.php?id=<?= $v['id'] ?>" class="action-btn edit-btn">Edit</a>
                            <a href="AdminDeleteVendor.php?id=<?= $v['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete this vendor?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


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