<?php
// CORRECCIÓN: migrado de include 'sesion.php' al nuevo sistema unificado
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'empresa.php');
    exit;
}

$nombre = trim($_POST['nombre_empresa'] ?? '');

if (empty($nombre)) {
    header('Location: ' . BASE_URL . 'empresa.php?error=campos_vacios');
    exit;
}

$logo_nombre = null;

if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
    $archivoTmp     = $_FILES['logo_empresa']['tmp_name'];
    $tipo_mime      = mime_content_type($archivoTmp);
    $mimes_validos  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($tipo_mime, $mimes_validos)) {
        header('Location: ' . BASE_URL . 'empresa.php?error=invalid_extension');
        exit;
    }

    $ext         = pathinfo($_FILES['logo_empresa']['name'], PATHINFO_EXTENSION);
    $logo_nombre = uniqid('logo_') . '.' . $ext;
    $destino     = UPLOAD_LOGOS . $logo_nombre;

    if (!move_uploaded_file($archivoTmp, $destino)) {
        header('Location: ' . BASE_URL . 'empresa.php?error=upload_failed');
        exit;
    }
}

$stmt = $conexion->prepare(
    "INSERT INTO empresa (nombre, logo) VALUES (?, ?)"
);
$stmt->bind_param("ss", $nombre, $logo_nombre);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . BASE_URL . 'empresa.php?msg=empresa_creada');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error al guardar empresa: " . htmlspecialchars($err));
}