<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=datos_invalidos');
    exit;
}

// No permitir que un admin se elimine a sí mismo
if ($id === Session::userId()) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=no_autoeliminar');
    exit;
}

$estatus = USR_INACTIVO; // 1
$stmt    = $conexion->prepare("UPDATE usuarios SET estatus = ? WHERE id = ?");
$stmt->bind_param("ii", $estatus, $id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . BASE_URL . 'usuarios.php?msg=usuario_desactivado');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error: " . htmlspecialchars($err));
}