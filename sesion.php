<?php
// Iniciar sesión solo si no está activa aún
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a BD si es necesario para verificar token
require_once 'conexion.php';

// Evitar que navegador guarde en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Validar que exista una sesión activa
if (!isset($_SESSION['usuario'])) {
    session_unset();
    session_destroy();
    header("Location: index.php?error=sin_sesion");
    exit();
}

// Verificar token de sesión único contra la BD
if (isset($_SESSION['usuario_id'], $_SESSION['token_sesion'])) {
    $user_id = $_SESSION['usuario_id'];
    $token_sesion = $_SESSION['token_sesion'];

    $sql = "SELECT session_token FROM usuarios WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($token_db);
    $stmt->fetch();
    $stmt->close();

    // Si los tokens no coinciden, cerrar sesión
    if ($token_db !== $token_sesion) {
        session_unset();
        session_destroy();
        header("Location: index.php?error=session_expirada");
        exit();
    }
} else {
    session_unset();
    session_destroy();
    header("Location: index.php?error=no_token");
    exit();
}

// Restricción por rol (solo si aplica)
$pagina = basename($_SERVER['PHP_SELF']);
$paginasRestringidas = ['usuarios.php', 'empresa.php'];

if (in_array($pagina, $paginasRestringidas)) {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        header("Location: acceso_denegado.php"); 
        exit();
    }
}
