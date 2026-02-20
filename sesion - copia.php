<?php

// Configurar cookie para que expire al cerrar navegador
//session_set_cookie_params([
  //  'lifetime' => 0,
  //  'path' => '/',
  //  'domain' => '',
  //  'secure' => isset($_SERVER['HTTPS']),
  //  'httponly' => true,
  //  'samesite' => 'Lax'
//]);

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

// Verificar token único para evitar sesiones simultáneas
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

    if ($token_db !== $token_sesion) {
        // Token no coincide, cerrar sesión y redirigir
        session_unset();
        session_destroy();
        header("Location: index.html?error=session_expired");
        exit();
    }
} else {
    // No hay token, cerrar sesión
    session_unset();
    session_destroy();
    header("Location: index.html");
    exit();
}

// Restringir acceso a páginas Usuario y Empresa (si la página es alguna de ellas)
$pagina = basename($_SERVER['PHP_SELF']);
$paginasRestringidas = ['usuarios.php', 'empresa.php']; // ajusta nombres reales

if (in_array($pagina, $paginasRestringidas)) {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
        // Redirigir o mostrar mensaje
        header("Location: acceso_denegado.php"); 
        exit();
    }
}
?>
