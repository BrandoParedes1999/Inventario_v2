<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

// Limpiar token en BD antes de destruir sesión
if (!empty($_SESSION['usuario_id'])) {
    $uid   = (int) $_SESSION['usuario_id'];
    // CORRECCIÓN: bind_param espera string, no null
    $empty = '';
    $stmt  = $conexion->prepare("UPDATE usuarios SET session_token = ? WHERE id = ?");
    $stmt->bind_param("si", $empty, $uid);
    $stmt->execute();
    $stmt->close();
}

Session::destroy();

header('Location: ' . BASE_URL . 'index.php');
exit;