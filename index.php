<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and handle errors
//session_start();
//include('db.php');

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
       header("Location: admin/admin_dashboard.php");
        exit(); // Stop further execution
    } else {
       header("Location: customer/customer_dashboard.php");
       exit(); // Stop further execution
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeus Bank</title>
    <link rel="stylesheet"type="text/css" href="/bank_project/views/style2.css">
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
                    <li><a href="register.php" class="btn">Register</a></li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') { ?>
                        <li><a href="admin.php" class="btn">Admin Dashboard</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="zb-hero-section">
            <div class="zb-container">
                <h2 class="zb-hero-title">Welcome to Zeus Bank</h2>
                <p class="zb-hero-text">Your one-stop solution for secure and efficient banking.</p>
                <a href="login.php" class="zb-btn-primary">Login to Your Account</a>
            </div>
        </section>

        <section class="zb-features">
            <div class="zb-container">
                <h3 class="zb-section-title">Our Features</h3>
                <div class="zb-feature-cards">
                    <div class="zb-feature-card">
                        <i class="fas fa-lock zb-feature-icon lock-icon"></i>
                        <h4 class="zb-feature-title">Secure Banking</h4>
                        <p>Access your account from anywhere, securely.</p>
                    </div>
                    <div class="zb-feature-card">
                        <i class="fas fa-money-bill-wave zb-feature-icon deposit-icon"></i>
                        <h4 class="zb-feature-title">Quick Deposits</h4>
                        <p>Deposit funds instantly and safely.</p>
                    </div>
                    <div class="zb-feature-card">
                        <i class="fas fa-receipt zb-feature-icon history-icon"></i>
                        <h4  class="zb-feature-title">Transaction History</h4>
                        <p>View your past transactions at any time.</p>
                    </div>
                    <div class="zb-feature-card">
                        <i class="fas fa-user-cog zb-feature-icon admin-icon"></i>
                        <h4  class="zb-feature-title">Admin Monitoring</h4>
                        <p>Admins can monitor user activities securely.</p>
                    </div>
                </div>
            </div>
        </section>
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
