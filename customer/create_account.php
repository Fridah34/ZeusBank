<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: login.php'); // Redirect to login if not logged in as a customer
    exit();
}

// Include database connection
require_once '../config/db.php';

// Fetch logged-in customer details
$customer_id = $_SESSION['user_id'];  // Make sure this is set
$sql = "SELECT full_name, username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    if (!$customer) {
        die("Customer details not found.");
    }
} else {
    die("Failed to fetch customer details.");
}

// Handle account creation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_type = $_POST['account_type'];
    $initial_balance = $_POST['account_balance'];

    // Validate input
    if (empty($account_type) || empty($initial_balance) || !is_numeric($initial_balance) || $initial_balance <= 0) {
        $error = "Please select a valid account type and provide a valid initial deposit.";
    } else {
        // Generate a random account number (you could also use a more sophisticated method)
        $account_number = strtoupper(uniqid('ACC'));

        // Insert the new account into the database
        $sql = "INSERT INTO accounts (account_number, account_type, account_balance, user_id, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssdi', $account_number, $account_type, $initial_balance, $customer_id);  // Use the correct customer_id here
            if ($stmt->execute()) {
                $success = "Account created successfully!";
            } else {
                $error = "Failed to create account. Please try again.";
            }
        } else {
            $error = "Error in account creation. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Account</title>
    <link rel="stylesheet" href="/bank_project/views/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
</head>
<body>
<header class="zb-header">
        <div class="zb-container">
            <h1 class="zb-title">Zeus Bank </h1>
            <nav class="zb-nav">
                <ul class="zb-nav-list">
                    <li><a href="../login.php" class="btn">Login</a></li>
                    <li><a href="../register.php" class="btn">Register</a></li>
                    <li><a href="../logs/logout.php" class="btn">Logout</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { ?>
                        <li><a href="admin.php" class="btn">Admin Dashboard</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>
    <div class="account-container">
        <h2>Create a New Account</h2>

        <!-- Display success or error message -->
        <?php if (isset($error)): ?>
            <div class="error"><?= $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>

        <!-- Account Creation Form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="account_type">Account Type</label>
                <select name="account_type" id="account_type" required>
                    <option value="">Select Account Type</option>
                    <option value="Savings">Savings</option>
                    <option value="Loan">Loan</option>
                    <option value="Business">Business</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>

            <div class="form-group">
                <label for="account_balance">Initial Deposit</label>
                <input type="number" name="account_balance" id="account_balance" min="1" required>
            </div>

            <input type="submit" value="Create Account">
        </form>

        <div class="back-links">
        <a href="customer_dashboard.php">Back to Dashboard</a>
        <a href="logout.php">Logout</a>
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
