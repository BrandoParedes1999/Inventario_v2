<?php
// CORRECCIÓN: archivo no tenía ninguna autenticación — cualquiera podía editar empresas
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if (!isset($_POST['empresa_id'], $_POST['nombre_empresa'])) {
    header('Location: ' . BASE_URL . 'empresa.php?error=missing_data');
    exit;
}

$empresa_id = intval($_POST['empresa_id']);
$nombre     = trim($_POST['nombre_empresa']);

if ($empresa_id <= 0 || empty($nombre)) {
    header('Location: ' . BASE_URL . 'empresa.php?error=datos_invalidos');
    exit;
}

// Obtener logo actual
$stmtSel = $conexion->prepare("SELECT logo FROM empresa WHERE id = ?");
$stmtSel->bind_param('i', $empresa_id);
$stmtSel->execute();
$res = $stmtSel->get_result()->fetch_assoc();
$stmtSel->close();

if (!$res) {
    header('Location: ' . BASE_URL . 'empresa.php?error=not_found');
    exit;
}

$nuevo_logo = $res['logo'];

// Procesar nuevo logo si se subió
if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
    $archivoTmp    = $_FILES['logo_empresa']['tmp_name'];
    $tipo_mime     = mime_content_type($archivoTmp);
    $mimes_validos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($tipo_mime, $mimes_validos)) {
        header('Location: ' . BASE_URL . 'empresa.php?error=invalid_extension');
        exit;
    }

    $ext              = pathinfo($_FILES['logo_empresa']['name'], PATHINFO_EXTENSION);
    $nuevoNombreArch  = 'logo_' . $empresa_id . '_' . time() . '.' . $ext;
    $destino          = UPLOAD_LOGOS . $nuevoNombreArch;

    if (move_uploaded_file($archivoTmp, $destino)) {
        // Borrar logo anterior si existe
        if (!empty($res['logo']) && file_exists(UPLOAD_LOGOS . $res['logo'])) {
            unlink(UPLOAD_LOGOS . $res['logo']);
        }
        $nuevo_logo = $nuevoNombreArch;
    } else {
        header('Location: ' . BASE_URL . 'empresa.php?error=upload_failed');
        exit;
    }
}

$stmtUpd = $conexion->prepare("UPDATE empresa SET nombre = ?, logo = ? WHERE id = ?");
$stmtUpd->bind_param('ssi', $nombre, $nuevo_logo, $empresa_id);

if ($stmtUpd->execute()) {
    $stmtUpd->close();
    header('Location: ' . BASE_URL . 'empresa.php?msg=empresa_actualizada');
    exit;
} else {
    $err = $stmtUpd->error;
    $stmtUpd->close();
    header('Location: ' . BASE_URL . 'empresa.php?error=update_failed');
    exit;
}