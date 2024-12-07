<?php
session_start();

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once '../config/db.php';

// Fetch accounts owned by the logged-in user
$user_id = $_SESSION['user_id'];  // Using user_id instead of customer_id
$sql = "SELECT account_number, account_balance FROM accounts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);  // Change customer_id to user_id
$stmt->execute();
$result = $stmt->get_result();
$accounts = $result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_account = $_POST['sender_account'];
    $recipient_account = $_POST['recipient_account'];
    $transfer_amount = $_POST['transfer_amount'];

    // Validate inputs
    if (empty($sender_account) || empty($recipient_account) || empty($transfer_amount)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($transfer_amount) || $transfer_amount <= 0) {
        $error = "Please enter a valid transfer amount.";
    } elseif ($sender_account === $recipient_account) {
        $error = "You cannot transfer funds to the same account.";
    } else {
        // Check sender's balance
        $sql = "SELECT account_balance FROM accounts WHERE account_number = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $sender_account, $user_id);  // Change customer_id to user_id
        $stmt->execute();
        $result = $stmt->get_result();
        $sender = $result->fetch_assoc();

        if (!$sender) {
            $error = "Sender account not found.";
        } elseif ($sender['account_balance'] < $transfer_amount) {
            $error = "Insufficient funds to complete the transfer.";
        } else {
            // Check if recipient exists
            $sql = "SELECT account_number FROM accounts WHERE account_number = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $recipient_account);
            $stmt->execute();
            $result = $stmt->get_result();
            $recipient = $result->fetch_assoc();

            if (!$recipient) {
                $error = "Recipient account not found.";
            } else {
                // Begin transaction
                $conn->begin_transaction();
                try {
                    // Deduct from sender
                    $sql = "UPDATE accounts SET account_balance = account_balance - ? WHERE account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ds', $transfer_amount, $sender_account);
                    $stmt->execute();

                    // Add to recipient
                    $sql = "UPDATE accounts SET account_balance = account_balance + ? WHERE account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ds', $transfer_amount, $recipient_account);
                    $stmt->execute();

                    // Get sender's user_id
                    $sql = "SELECT user_id FROM accounts WHERE account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $sender_account);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $sender_user = $result->fetch_assoc();
                    if ($sender_user) {
                        $sender_user_id = $sender_user['user_id'];  // Correct user_id for the transaction
                    } else {
                        // Handle error: sender account not found
                        $error = "Sender account not found.";
                        exit();
                    }

                    // Record the transaction
                    $sql = "INSERT INTO transactions (sender_account, recipient_account, transaction_type, amount, transaction_date, user_id) 
                            VALUES (?, ?, 'transfer', ?, NOW(), ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssdi', $sender_account, $recipient_account, $transfer_amount, $sender_user_id);
                    $stmt->execute();

                    // Commit the transaction
                    $conn->commit();
                    $success = "Transfer completed successfully!";
                } catch (Exception $e) {
                    // Rollback if an error occurs
                    $conn->rollback();
                    $error = "Transfer failed. Please try again. Error: " . $e->getMessage();
                }
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
    <title>Transfer Funds</title>
    <link rel="stylesheet" href="/bank_project/views/style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-size: 16px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error, .success {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
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
    <div class="container">
        <div class="transfer-header">
        <h2>Transfer Funds</h2>
    </div>

        <!-- Display success or error message -->
        <?php if (isset($error)): ?>
            <div class="error"><?= $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>

        <!-- Transfer Funds Form -->
        <form method="POST" action="">
            <div class="transfer-form-group">
                <label for="sender_account">Sender Account</label>
                <select name="sender_account" id="sender_account" required>
                    <option value="">Select Your Account</option>
                    <?php foreach ($accounts as $account): ?>
                        <option value="<?= $account['account_number']; ?>">
                            <?= $account['account_number']; ?> (Balance: $<?= number_format($account['account_balance'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="transfer-form-group">
                <label for="recipient_account">Recipient Account</label>
                <input type="text" name="recipient_account" id="recipient_account" required>
            </div>

            <div class="transfer-form-group">
                <label for="transfer_amount">Amount to Transfer</label>
                <input type="number" name="transfer_amount" id="transfer_amount" min="1" required>
            </div>

            <input type="submit" value="Transfer">
        </form>

        <div>
        <a href="customer_dashboard.php">Back to Dashboard</a>
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
