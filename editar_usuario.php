<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
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

// Validar que el rol sea uno de los permitidos
// CORRECCIÓN: el modal enviaba 'Admin' en lugar de 'Administrador'
if (!in_array($rol, [ROL_ADMIN, ROL_USUARIO], true)) {
    $rol = ROL_USUARIO;
}

// Validar estatus
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