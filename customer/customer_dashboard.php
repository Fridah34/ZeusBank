<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: login.php'); // Redirect to login if not logged in as a customer
    exit();
}

// Include database connection
require_once '../config/db.php';

// Fetch customer details
$customer_id = $_SESSION['user_id'];
$sql = "SELECT full_name, account_balance FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
} else {
    die("Failed to fetch customer details.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="/bank_project/views/style.css">
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
    <div class="customer-dashboard-container">
        <h2>Welcome, <?= htmlspecialchars($customer['full_name']); ?>!</h2>
        <p>Your Current Account Balance: <strong>$<?= number_format($customer['account_balance'], 2); ?></strong></p>

        <div class="cards-container">
             <!-- Create Account Card -->
             <div class="card">
                <h3>Create New Account</h3>
                <p>Choose the account type (Savings, Loan, Business) and create a new account.</p>
                <a href="create_account.php">Create Now</a>
            </div>
            <!-- View Account Card -->
            <div class="card">
                <h3>View Account</h3>
                <p>Click to view your account details, including balance and account history.</p>
                <a href="view_account.php">View My Account</a>
            </div>

            <!--  Transfer Funds -->
            <div class="card">
                <h3>Transfer Funds</h3>
                <p>Easily transfer money to other accounts securely and quickly.</p>
                <a href="transfer_funds.php">Transfer Now</a>
            </div>

            <!--  Transaction History -->
            <div class="card">
                <h3>Transaction History</h3>
                <p>Track all your past transactions to stay updated with your account activity.</p>
                <a href="transaction_history.php">View History</a>
            </div>


            <!--Bill Payments -->
            <div class="card">
                <h3>Pay Bills</h3>
                <p>Conveniently pay your bills online with just a few clicks.</p>
                <a href="pay_bills.php">Pay Now</a>
            </div>
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
