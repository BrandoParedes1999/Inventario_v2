<?php

// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Verifica que haya sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token_sesion'])) {
    header("Location: index.php?expirada=1");
    exit;
}

// Verifica token válido en base de datos
include 'conexion.php';
$sql = "SELECT id FROM usuarios WHERE usuario = ? AND session_token = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $_SESSION['usuario'], $_SESSION['token_sesion']);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    // Token no válido: forzar cierre de sesión
    $_SESSION = [];
    session_destroy();
    header("Location: index.php?expirada=1");
    exit;
}
?>
