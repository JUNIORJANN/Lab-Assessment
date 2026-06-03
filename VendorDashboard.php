<?php session_start();
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html"); ?>
<!DOCTYPE html>
<html>

<head>
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="dashboard-container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['vendor_name']); ?>!</h1>
        <div class="dashboard-menu">
            <div class="dashboard-card">
                <h3>Add Product</h3><a href="AddProduct.php">+ New Product</a>
            </div>
            <div class="dashboard-card">
                <h3>My Products</h3><a href="MyProduct.php">Manage Products</a>
            </div>
            <div class="dashboard-card">
                <h3>Store Profile</h3><a href="VendorProfile.php">Edit Profile</a>
            </div>
            <div class="dashboard-card">
                <h3>Orders</h3><a href="VendorOrders.php">Manage Orders</a>
            </div>
            <div class="dashboard-card">
                <h3>Announcements</h3><a href="VendorAnnouncements.php">Post & View</a>
            </div>
            <div class="dashboard-card">
                <h3>Logout</h3><a href="Logout.php">Sign Out</a>
            </div>
        </div>
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