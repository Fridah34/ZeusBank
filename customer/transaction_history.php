<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once '../config/db.php';

// Fetch the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Query to fetch transaction history
$sql = "
    SELECT 
        t.id AS transaction_id,
        t.sender_account,
        t.recipient_account,
        t.transaction_type,
        t.amount,
        t.transaction_date
    FROM 
        transactions t
    INNER JOIN 
        accounts a ON (t.sender_account = a.account_number OR t.recipient_account = a.account_number)
    WHERE 
        a.user_id = ?
    ORDER BY 
        t.transaction_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
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
            max-width: 900px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        .back-link:hover {
            text-decoration: underline;
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
    <div class="history-container">
        <h2>Transaction History</h2>

        <!-- Display Transaction History -->
        <?php if (empty($transactions)): ?>
            <p style="text-align:center; color: #333;">No transactions found for your accounts.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Sender Account</th>
                        <th>Recipient Account</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['transaction_id']); ?></td>
                            <td><?= htmlspecialchars($transaction['sender_account']); ?></td>
                            <td><?= htmlspecialchars($transaction['recipient_account']); ?></td>
                            <td><?= ucfirst(htmlspecialchars($transaction['transaction_type'])); ?></td>
                            <td>$<?= number_format($transaction['amount'], 2); ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Link to go back to the dashboard -->
        <a href="customer_dashboard.php" class="back-link">Back to Dashboard</a>
        <a href="logout.php" class="back-link">Logout</a>
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
