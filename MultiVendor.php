<?php
require_once 'LadangLinkDB.php';
$result = $conn->query("SELECT * FROM vendors WHERE is_admin = 0");
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Vendors - LadangLink</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>
<body>
    <div id="navbar"></div>
    <div class="container">
        <h1>Our Vendors</h1>
        <div class="vendor-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="vendor-card">
                    <img src="images/vendors/<?= $row['image'] ?: 'default.png' ?>" alt="<?= $row['vendor_name'] ?>">
                    <h3><?= htmlspecialchars($row['vendor_name']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <a href="VendorStore.php?id=<?= $row['id'] ?>">Visit Store</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div id="footer"></div>
    <script>
        fetch("Navbar.html")
            .then(res => res.text())
            .then(data => {
                document.getElementById('navbar').innerHTML = data;
                // Load dropdown.js dynamically after navbar is inserted
                let script = document.createElement('script');
                script.src = 'dropdown.js';
                document.body.appendChild(script);
            })
            .catch(err => console.error("Navbar load error:", err));

        fetch("Footer.html")
            .then(res => res.text())
            .then(data => document.getElementById('footer').innerHTML = data)
            .catch(err => console.error("Footer load error:", err));
    </script>
</body>
</html>