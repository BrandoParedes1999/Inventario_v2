<?php
/**
 * asignar_otro_articulo.php
 * NOTA: Este archivo es una versión anterior de la asignación.
 * La funcionalidad ha sido reemplazada por crear_pdf.php.
 * Se mantiene protegido por seguridad.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'usuarios.php');
    exit;
}

$usuario_id    = intval($_POST['usuario_id']    ?? 0);
$articulo_id   = intval($_POST['articulo_id']   ?? 0);
$area          = trim($_POST['area']            ?? '');
$puesto        = trim($_POST['puesto']          ?? '');
$fecha         = trim($_POST['fecha']           ?? date('Y-m-d'));
$observaciones = trim($_POST['observaciones']   ?? '');

if ($usuario_id <= 0 || $articulo_id <= 0) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=datos_invalidos');
    exit;
}

// Guardar carpeta con nombre único
$carpeta = UPLOAD_EVIDENCIAS . uniqid("asig_");
if (!mkdir($carpeta, 0755, true)) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=upload_dir');
    exit;
}

$archivos_guardados = [];
if (!empty($_FILES['evidencia']['name'][0])) {
    foreach ($_FILES['evidencia']['tmp_name'] as $key => $tmp_name) {
        $nombreArchivo = basename($_FILES['evidencia']['name'][$key]);
        $rutaArchivo   = $carpeta . '/' . $nombreArchivo;
        if (move_uploaded_file($tmp_name, $rutaArchivo)) {
            $archivos_guardados[] = $nombreArchivo;
        }
    }
}

$evidencias_json = json_encode($archivos_guardados);

$stmtIns = $conexion->prepare(
    "INSERT INTO asignaciones (usuario_id, articulo_id, area, puesto, fecha, observaciones, evidencia, carpeta)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$carpetaRel = str_replace(UPLOAD_EVIDENCIAS, 'uploads/evidencias/', $carpeta);
$stmtIns->bind_param(
    "iisssss s",
    $usuario_id, $articulo_id, $area, $puesto, $fecha,
    $observaciones, $evidencias_json, $carpetaRel
);
$stmtIns->execute();
$stmtIns->close();

// Cambiar estatus del artículo a asignado
$estatus = ART_ASIGNADO; // 1
$stmtUpd = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ?");
$stmtUpd->bind_param("ii", $estatus, $articulo_id);
$stmtUpd->execute();
$stmtUpd->close();

header('Location: ' . BASE_URL . 'usuarios.php?msg=asignado');
exit;