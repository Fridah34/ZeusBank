<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
session_start();

// Destroy all session data
session_unset();  // Unset all session variables
session_destroy();  // Destroy the session

// Redirect to the index page (home page)
header('Location: ../index.php');  // Replace 'index.php' with your homepage URL if it's different
exit();
?>
