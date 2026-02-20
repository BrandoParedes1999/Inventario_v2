<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = [];

// Eliminar cookie de sesión (opcional, por seguridad extra)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header("Location: index.php");
exit();
