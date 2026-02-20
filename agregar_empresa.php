<?php
include 'conexion.php';
include 'sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conexion->real_escape_string(trim($_POST['nombre_empresa']));

    $logo_nombre = null;
    if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
        $archivoTmp = $_FILES['logo_empresa']['tmp_name'];
        $nombreOriginal = $_FILES['logo_empresa']['name'];
        $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $logo_nombre = uniqid('logo_') . "." . $ext;
        $destino = "logos/" . $logo_nombre;

        if (!is_dir('logos')) {
            mkdir('logos', 0755, true);
        }

        if (!move_uploaded_file($archivoTmp, $destino)) {
            die("Error al subir el logo.");
        }
    }

    $sql = "INSERT INTO empresa (nombre, logo) VALUES ('$nombre', " . ($logo_nombre ? "'$logo_nombre'" : "NULL") . ")";
    if ($conexion->query($sql) === TRUE) {
        header("Location: empresa.php");
        exit;
    } else {
        die("Error al guardar empresa: " . $conexion->error);
    }
} else {
    die("Método no permitido.");
}

?>
