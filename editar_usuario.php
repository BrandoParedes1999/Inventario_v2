<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
    exit;
}

// ─── Validar CSRF ─────────────────────────────────────────────────
// CORRECCIÓN: el handler no validaba el token aunque el modal sí lo enviaba
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=csrf');
    exit;
}

$id      = intval($_POST['id']              ?? 0);
$nombre  = trim($_POST['nombre_completo']   ?? '');
$usuario = trim($_POST['usuario']           ?? '');
$correo  = trim($_POST['correo']            ?? '');
$rol     = trim($_POST['rol']               ?? ROL_USUARIO);
$estatus = intval($_POST['estatus']         ?? USR_ACTIVO);

if ($id <= 0 || empty($nombre) || empty($usuario)) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=datos_invalidos');
    exit;
}

if (!in_array($rol, [ROL_ADMIN, ROL_USUARIO], true)) {
    $rol = ROL_USUARIO;
}

if (!in_array($estatus, [USR_ACTIVO, USR_INACTIVO], true)) {
    $estatus = USR_ACTIVO;
}

$stmt = $conexion->prepare(
    "UPDATE usuarios
     SET nombre_completo = ?, usuario = ?, correo = ?, rol = ?, estatus = ?
     WHERE id = ?"
);
$stmt->bind_param("ssssii", $nombre, $usuario, $correo, $rol, $estatus, $id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . BASE_URL . 'usuarios.php?msg=usuario_actualizado');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error al actualizar: " . htmlspecialchars($err));
}