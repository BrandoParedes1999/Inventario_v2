<?php
include 'conexion.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mensaje si ya hay sesión activa
if (isset($_SESSION['usuario'])) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Sesión activa</title>
        <link href='css/sb-admin-2.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light d-flex align-items-center justify-content-center' style='height: 100vh;'>
        <div class='card shadow p-4'>
            <h4 class='text-danger'>Ya hay una sesión activa</h4>
            <p>El usuario <strong>{$_SESSION['usuario']}</strong> ya está logueado.</p>
            <p>Por favor, <a href='logout.php'>cierre la sesión</a> antes de ingresar con otra cuenta.</p>
        </div>
    </body>
    </html>";
    exit();
}

// Procesar formulario de login si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $estatus = 0;

    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND estatus = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $usuario, $estatus);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $hash = $row['contrasena'];

        if (password_verify($contrasena, $hash)) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['nombre'] = $row['nombre_completo'] ?? $usuario;
            $_SESSION['token_sesion'] = $token;
            $_SESSION['usuario_id'] = $row['id'];

            $sqlUpdate = "UPDATE usuarios SET session_token = ? WHERE id = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $token, $row['id']);
            $stmtUpdate->execute();
            $stmtUpdate->close();

            header("Location: dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado o inactivo.";
    }
}
?>
