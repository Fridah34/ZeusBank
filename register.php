<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Debug: Check if the session is active
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "";
} else {
    echo "Debug: Session is not active.<br>";
}

$error = "";
$success = ""; // Initialize variables

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch and sanitize input
    $fullname = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['cpass']) ? trim($_POST['cpass']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $phonenumber = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $accountBalance = 0.00;

    // Validate inputs
    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($role) || empty($phonenumber) || empty($address)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email already exists
        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = "Username or email already exists!";
        } else {
            // Insert new user into database
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (full_name, username, email, phone_number, password, role, address, account_balance) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssd", $fullname, $username, $email, $phonenumber, $passwordHash, $role, $address, $accountBalance);

            if ($stmt->execute()) {
                $success = "Registration successful!";
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Zeus Bank</title>
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
                    <li><a href="login.php" class="btn">Login</a></li>
                    <li><a href="logs/logout.php" class="btn">Logout</a></li>
                    
                </ul>
            </nav>
        </div>
    </header>
    <main>
    <div class="zb-register-container">
        <div class="zb-register-box">
            <h2 class="zb-register-title">Create an Account</h2>
            <?php if (!empty($error)) echo "<p class='zb-error-message'>$error</p>"; ?>
            <?php if (!empty($success)) echo "<p class='zb-success-message'>$success</p>"; ?>
            <form method="POST" action="register.php" class="zb-register-form">
                <input type="text" placeholder="Full Name" class="zb-input" name="full_name" required>
                <input type="text" placeholder="User Name" class="zb-input" name="username" required>
                <input type="email" placeholder="Email" class="zb-input" name="email" required>
                <input type="password" placeholder="Password" class="zb-input" name="password" required>
                <input type="password" placeholder="Confirm Password" class="zb-input" name="cpass" required>
                <select name="role" class="zb-input" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="text" placeholder="Phone Number" class="zb-input" name="phone_number" required>
                <input type="text" placeholder="Address" class="zb-input" name="address" required>
                <input type="submit" value="Create an Account" class="zb-submit-btn" name="btn-save">
            </form>
            <p class="zb-register-link">Already have an account? <a href="login.php">Login here</a></p>
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