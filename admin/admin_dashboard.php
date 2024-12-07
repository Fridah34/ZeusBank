<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php'); // Redirect to login if not admin
    exit();
}

// Include database connection
if (!file_exists('../config/db.php')) {
    die('Database configuration file not found.');
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customer_id'])) {
    $customer_id = intval($_POST['delete_customer_id']);
    $delete_sql = "DELETE FROM users WHERE id = ? AND role = 'customer'";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt) {
        $delete_stmt->bind_param('i', $customer_id);
        if ($delete_stmt->execute()) {
            $message = "Customer deleted successfully.";
        } else {
            $message = "Failed to delete customer.";
        }
    } else {
        $message = "Failed to prepare deletion query.";
    }
}

// Fetch all customers from the database
$sql = "SELECT id, full_name, username, email, phone_number, address, account_balance, created_at FROM users WHERE role = 'customer'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database query failed: " . $conn->error);
}
$stmt->execute();
$customers = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Customers</title>
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
                </ul>
            </nav>
        </div>
    </header>
    <main>
    <div class="dashboard-container">
        <h2>Admin Dashboard - Manage Customers</h2>

        <?php if (isset($message)): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>
        
        <h3>Customer List</h3>
        <?php if ($customers->num_rows > 0): ?>
            <table border="1" cellspacing="0" cellpadding="10">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Account Balance</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($customer = $customers->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($customer['id']); ?></td>
                            <td><?= htmlspecialchars($customer['full_name']); ?></td>
                            <td><?= htmlspecialchars($customer['username']); ?></td>
                            <td><?= htmlspecialchars($customer['email']); ?></td>
                            <td><?= htmlspecialchars($customer['phone_number']); ?></td>
                            <td><?= htmlspecialchars($customer['address']); ?></td>
                            <td><?= htmlspecialchars($customer['account_balance']); ?></td>
                            <td><?= htmlspecialchars($customer['created_at']); ?></td>
                            <td>

                            <!-- Inline Delete Form -->
                            <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_customer_id" value="<?= $customer['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                                </form>
                                <a href="edit_customer.php?id=<?= $customer['id']; ?>">Edit</a>
                                <a href="delete_customer.php?id=<?= $customer['id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
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
