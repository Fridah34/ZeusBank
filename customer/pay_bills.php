<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: login.php'); // Redirect to login if not logged in as a customer
    exit();
}

// Database connection
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

// Define success and error messages
$success_message = '';
$error_message = '';

// Handle bill payment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $bill_type = $_POST['bill_type'];
    $amount = $_POST['amount'];

    // Validate inputs
    if (empty($bill_type) || empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $error_message = 'Please fill out all fields with valid values.';
    } else {
        // Process the payment (example: deduct amount from user's account)
        $sql = "UPDATE users SET account_balance = account_balance - ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('di', $amount, $user_id);
            if ($stmt->execute()) {
                // Log the payment transaction
                $transaction_sql = "INSERT INTO transactions (user_id, transaction_type,bill_type, amount, description, transaction_date,status) VALUES (?, 'bill payment', ?, ?,?, NOW(),'completed')";
                if($transaction_stmt = $conn->prepare($transaction_sql)) {
                    $description = "Payment for $bill_type bill";
                    $transaction_stmt->bind_param('issd', $user_id,$bill_type, $amount, $description);
                    $transaction_stmt->execute();
                    
                    // Set success message
                    $success_message = 'Your bill payment was successful!';
                } else {
                    $error_message = 'Failed to log transaction.';
                }
            } else {
                $error_message = 'Failed to process the payment.';
            }
        } else {
            $error_message = 'Database error: Could not prepare statement.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Bills</title>
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
    <div class="paybills-container">
        <section class="paybills-section">
        <h2 class="section-title">Pay Your Bills</h2>

        <!-- Display success or error messages -->
         <div class="messages">
        <?php if (!empty($success_message)) : ?>
            <div class="message success"><?= htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)) : ?>
            <div class="message error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Bill Payment Form -->
        <form action="pay_bills.php" method="POST" class="paybills-form">
              <div class="paybills-form-group">
                   <label for="sender_account">Sender Account</label>
                   <input type="text" id="sender_account" name="sender_account" placeholder="Enter your account number" required>
              </div>
            <div class="paybills-form-group">
                <label for="bill_type">Bill Type</label>
                <input type="text" id="bill_type" name="bill_type" placeholder="e.g., Electricity, Internet" required>
            </div>
            <div class="paybills-form-group">
                <label for="amount">Amount ($)</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount to pay" required min="1" step="any">
            </div>

            <div class="paybills-form-group">
            <button type="submit" class="btn-submit">Pay Bill</button>
            <a href="customer_dashboard.php" class="back-link">Back to Dashboard</a>
            </div>
        </form>
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
</body>
</html>
