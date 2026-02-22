<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
    exit;
}

// ─── Validar CSRF ─────────────────────────────────────────────────
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=csrf');
    exit;
}

$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$usuario         = trim($_POST['usuario']         ?? '');
$contrasena      = $_POST['contrasena']            ?? '';
$correo          = trim($_POST['correo']           ?? '');
$rol             = trim($_POST['rol']              ?? ROL_USUARIO);
$estatus         = USR_ACTIVO; // 0

if (empty($nombre_completo) || empty($usuario) || empty($contrasena) || empty($correo)) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=campos_vacios');
    exit;
}

if (!in_array($rol, [ROL_ADMIN, ROL_USUARIO], true)) {
    $rol = ROL_USUARIO;
}

// ─── Verificar duplicado ──────────────────────────────────────────
// CORRECCIÓN: la versión anterior usaba short-circuit evaluation con &&
//   $stmtCheck->...->num_rows > 0 && $stmtCheck->close() && header(...) && exit();
// header() devuelve void (null → falsy en contexto bool) → exit() NUNCA se ejecutaba
// → el código continuaba al INSERT → MySQL lanzaba error de llave duplicada → die()
// → el usuario veía error en lugar del mensaje correcto.
$stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmtCheck->bind_param("s", $usuario);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {
    $stmtCheck->close();
    header('Location: ' . BASE_URL . 'usuarios.php?error=usuario_existente');
    exit;
}
$stmtCheck->close();

$hash = password_hash($contrasena, PASSWORD_BCRYPT);

$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre_completo, usuario, contrasena, correo, rol, estatus)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssi", $nombre_completo, $usuario, $hash, $correo, $rol, $estatus);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . BASE_URL . 'usuarios.php?msg=usuario_creado');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error al crear usuario: " . htmlspecialchars($err));
}