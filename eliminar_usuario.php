<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
    exit;
}

// ─── Validar CSRF ─────────────────────────────────────────────────
// CORRECCIÓN: el handler no validaba el token CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=csrf');
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=datos_invalidos');
    exit;
}

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