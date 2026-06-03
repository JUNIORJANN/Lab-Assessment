<?php
session_start();
require_once 'LadangLinkDB.php';
if (!isset($_SESSION['vendor_id'])) header("Location: VendorLogin.html");
$vendor_id = $_SESSION['vendor_id'];

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM announcements WHERE id = $id AND vendor_id = $vendor_id");
    header("Location: VendorAnnouncements.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $stmt = $conn->prepare("INSERT INTO announcements (vendor_id, title, message, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $vendor_id, $title, $message, $start_date, $end_date);
    $stmt->execute();
    $success = "Announcement posted!";
}
$announcements = $conn->query("SELECT * FROM announcements WHERE vendor_id = $vendor_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Post Announcement</title>
    <link rel="stylesheet" href="LadangStyle.css">
</head>

<body>
    <div id="navbar"></div>
    <div class="announcements-container">
        <div class="announcement-form-card">
            <h2>📢 Post a New Announcement</h2>
            <?php if (isset($success)) echo "<div class='success-message'>✅ $success</div>"; ?>
            <form method="POST">
                <input type="text" name="title" placeholder="Announcement title" required>
                <textarea name="message" rows="5" placeholder="Your message..." required></textarea>
                <div style="display: flex; gap: 10px;">
                    <input type="date" name="start_date"> <span>Start date (optional)</span>
                    <input type="date" name="end_date"> <span>End date (optional)</span>
                </div>
                <button type="submit">Publish Announcement</button>
            </form>
        </div>
        <div class="announcement-list">
            <h2>📋 Previous Announcements</h2>
            <?php if ($announcements->num_rows == 0): ?>
                <div class="announcement-card" style="text-align:center;">No announcements yet.</div>
            <?php else: ?>
                <?php while ($a = $announcements->fetch_assoc()): ?>
                    <div class="announcement-card">
                        <div class="announcement-title"><?= htmlspecialchars($a['title']) ?></div>
                        <div class="announcement-date">
                            Posted: <?= $a['created_at'] ?>
                            <?php if ($a['start_date'] || $a['end_date']): ?>
                                <br>Valid: <?= $a['start_date'] ?: 'any' ?> – <?= $a['end_date'] ?: 'any' ?>
                            <?php endif; ?>
                        </div>
                        <div class="announcement-message"><?= nl2br(htmlspecialchars($a['message'])) ?></div>
                        <div style="margin-top:10px;">
                            <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Delete this announcement?')" style="color:#f44336;">🗑️ Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
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