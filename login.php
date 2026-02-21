<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Si ya hay sesión activa → dashboard
if (Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// ─── Validar CSRF ─────────────────────────────────────────────────
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'index.php?error=csrf');
    exit;
}
unset($_SESSION['csrf_token']); // Token de un solo uso

// ─── Sanitizar inputs ─────────────────────────────────────────────
$usuario   = trim($_POST['usuario']   ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

if (empty($usuario) || empty($contrasena)) {
    header('Location: ' . BASE_URL . 'index.php?error=campos_vacios');
    exit;
}

// ─── Buscar usuario activo ────────────────────────────────────────
$stmt = $conexion->prepare(
    "SELECT id, usuario, contrasena, nombre_completo, rol
     FROM usuarios
     WHERE usuario = ? AND estatus = ?"
);
$estatus = USR_ACTIVO;
$stmt->bind_param("si", $usuario, $estatus);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

if ($resultado->num_rows === 0) {
    // No revelar si el usuario existe o no
    header('Location: ' . BASE_URL . 'index.php?error=credenciales');
    exit;
}

$row = $resultado->fetch_assoc();

// ─── Verificar contraseña ─────────────────────────────────────────
if (!password_verify($contrasena, $row['contrasena'])) {
    header('Location: ' . BASE_URL . 'index.php?error=credenciales');
    exit;
}

// ─── Generar token de sesión único ───────────────────────────────
$token = bin2hex(random_bytes(32));

$stmtToken = $conexion->prepare(
    "UPDATE usuarios SET session_token = ? WHERE id = ?"
);
$stmtToken->bind_param("si", $token, $row['id']);
$stmtToken->execute();
$stmtToken->close();

// ─── Registrar sesión ─────────────────────────────────────────────
session_regenerate_id(true); // Prevenir session fixation

$_SESSION['usuario']      = $row['usuario'];
$_SESSION['usuario_id']   = $row['id'];
$_SESSION['nombre']       = $row['nombre_completo'];
$_SESSION['rol']          = $row['rol'];
$_SESSION['token_sesion'] = $token;
$_SESSION['_initiated']   = true;

// ─── Redirigir ────────────────────────────────────────────────────
header('Location: ' . BASE_URL . 'dashboard.php');
exit;