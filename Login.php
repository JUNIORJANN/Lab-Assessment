```php
<?php
session_start();
require_once 'db.php'; // central DB connection

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate inputs
    if (empty($_POST['username']) || empty($_POST['password'])) {
        die("Please fill in all fields.");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare SQL (PREVENT SQL INJECTION)
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // VERIFY HASHED PASSWORD
        if (password_verify($password, $user['password'])) {

            // Regenerate session ID (prevent session hijacking)
            session_regenerate_id(true);

            // Store session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'vendor') {
                header("Location: VendorDashboard.php");
            } else {
                header("Location: CustomerDashboard.php");
            }
            exit();

        } else {
            echo "Invalid password.";
        }

    } else {
        echo "User not found.";
    }

    $stmt->close();
}
?>
```
