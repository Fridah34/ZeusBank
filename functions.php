<?php
function isLoggedIn() {
    session_start();
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>
