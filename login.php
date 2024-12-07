<?php
session_start(); // Start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Display current directory
echo "";

// Include the database configuration file
echo "";
$dbPath = realpath('config/db.php');
if ($dbPath) {
    echo "";
    require_once 'config/db.php';
} else {
    die("Debug: db.php file not found! Please check the path.");
}

// Check if the session is active
echo "";

$error = "";

// Handle POST request for login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate username and password fields
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password!';
    } else {
        // Prepare SQL query to check if username exists in the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // User exists, verify password
            $user = $result->fetch_assoc();

            echo "Debug: User data from DB:<br>";
            print_r($user);

            if (password_verify($password, $user['password'])) {
                // Password is correct, start the session and store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['username'] = $user['username'];

                echo "Debug: Session data:<br>";
                print_r($_SESSION);

                // Redirect based on user role
                if (strtolower($user['role']) === 'admin') {
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } else if (strtolower($user['role']) === 'customer') {
                    header("Location: customer/customer_dashboard.php");
                    exit();
                } else {
                    $error = 'Unknown role! Please contact support.';
                }
            } else {
                $error = 'Invalid username or password!';
            }
        } else {
            $error = 'Invalid username or password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zeus Bank</title>
    <link rel="stylesheet" href="/bank_project/views/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header class="zb-header">
        <div class="zb-container">
            <h1 class="zb-title">Zeus Bank </h1>
            <nav class="zb-nav">
                <ul class="zb-nav-list">
                    <li><a href="register.php" class="btn">Register</a></li>
                    <li><a href="logs/logout.php" class="btn">Logout</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { ?>
                        <li><a href="admin.php" class="btn">Admin Dashboard</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>
    <div class="zb-login-container">
        <div class="zb-login-box">
            <h2 class="zb-login-title">Login</h2>
            <?php if (!empty($error)) echo "<p class='zb-error-message'>$error</p>"; ?>
            <form method="POST" action="login.php" class="zb-login-form">
                <label for="username" class="zb-form-label">Username:</label>
                <input type="text" name="username" id="username" class="zb-form-input" required><br>
                <label for="password" class="zb-form-label">Password:</label>
                <input type="password" name="password" id="password" class="zb-form-input" required><br>
                <input type="submit" value="Login" class="zb-form-submit">
            </form>
            <a href="index.php" class="zb-back-link">Back to Home</a>
        </div>
    </div>
    </main>
    <footer class="footer">
    <div class="social-icons">
        <a href="https://twitter.com" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Twitter" class="icon">
        </a>
        <a href="https://facebook.com" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook" class="icon">
        </a>
        <a href="https://instagram.com" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" alt="Instagram" class="icon">
        </a>
        <a href="https://linkedin.com" target="_blank">
            <img src="https://cdn-icons-png.flaticon.com/512/733/733561.png" alt="LinkedIn" class="icon">
        </a>
    </div>
    <p>&copy; 2024 Zeus Bank. All Rights Reserved.</p>
</footer>
    <script src="script.js"></script>

</body>
</html>