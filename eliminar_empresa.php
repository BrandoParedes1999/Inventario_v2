<?php
// CORRECCIÓN: archivo no tenía ninguna autenticación — cualquiera podía borrar empresas
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'empresa.php');
    exit;
}

if (!isset($_POST['empresa_id'])) {
    header('Location: ' . BASE_URL . 'empresa.php?error=missing_id');
    exit;
}

$id = intval($_POST['empresa_id']);

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'empresa.php?error=datos_invalidos');
    exit;
}

// Obtener logo antes de borrar para eliminar el archivo físico
$stmtSel = $conexion->prepare("SELECT logo FROM empresa WHERE id = ?");
$stmtSel->bind_param("i", $id);
$stmtSel->execute();
$res  = $stmtSel->get_result()->fetch_assoc();
$stmtSel->close();

if (!$res) {
    header('Location: ' . BASE_URL . 'empresa.php?error=not_found');
    exit;
}

$stmt = $conexion->prepare("DELETE FROM empresa WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();

    // Borrar imagen si existe
    if (!empty($res['logo']) && file_exists(UPLOAD_LOGOS . $res['logo'])) {
        unlink(UPLOAD_LOGOS . $res['logo']);
    }

    header('Location: ' . BASE_URL . 'empresa.php?msg=empresa_eliminada');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error al eliminar empresa: " . htmlspecialchars($err));
}