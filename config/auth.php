<?php
if (session_status() === PHP_SESSION_NONE) {
    // Configuramos cookies seguras
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '', 
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}