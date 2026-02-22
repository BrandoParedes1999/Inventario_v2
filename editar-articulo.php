<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// ─── Validar CSRF ─────────────────────────────────────────────────
// CORRECCIÓN: el handler no validaba el token CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=csrf');
    exit;
}

$id                = intval($_POST['id']               ?? 0);
$articulo          = trim($_POST['articulo']           ?? '');
$marca             = trim($_POST['marca']              ?? '');
$modelo            = trim($_POST['modelo']             ?? '');
$numero_serie      = trim($_POST['numero_serie']       ?? '');
$categoria         = trim($_POST['categoria']          ?? '');
$fecha_adquisicion = trim($_POST['fecha_adquisicion']  ?? '');

if ($id <= 0 || empty($articulo)) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=datos_invalidos');
    exit;
}

// ── Obtener datos actuales ────────────────────────────────────────
$stmtSel = $conexion->prepare("SELECT imagen, factura FROM articulo WHERE id = ?");
$stmtSel->bind_param("i", $id);
$stmtSel->execute();
$res = $stmtSel->get_result()->fetch_assoc();
$stmtSel->close();

if (!$res) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=no_encontrado');
    exit;
}

$imagen_path  = $res['imagen'];
$factura_path = $res['factura'];

// ── Procesar nueva imagen ─────────────────────────────────────────
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tipo_mime     = mime_content_type($_FILES['imagen']['tmp_name']);
    $mimes_validos = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($tipo_mime, $mimes_validos)) {
        die("Solo se permiten imágenes JPG o PNG.");
    }

    $ext        = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nuevo      = uniqid() . '_' . time() . '.' . $ext;
    $ruta_nueva = 'uploads/imagenes/' . $nuevo;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], UPLOAD_IMAGENES . $nuevo)) {
        $imagen_path = $ruta_nueva;
    }
}

// ── Procesar nueva factura PDF ────────────────────────────────────
if (isset($_FILES['factura']) && $_FILES['factura']['error'] === UPLOAD_ERR_OK) {
    if (mime_content_type($_FILES['factura']['tmp_name']) === 'application/pdf') {
        $nuevo_pdf = uniqid('factura_') . '.pdf';
        if (move_uploaded_file($_FILES['factura']['tmp_name'], UPLOAD_FACTURAS . $nuevo_pdf)) {
            $factura_path = 'uploads/facturas/' . $nuevo_pdf;
        }
    }
}

// ── Actualizar ────────────────────────────────────────────────────
$stmtUpd = $conexion->prepare(
    "UPDATE articulo
     SET articulo = ?, marca = ?, modelo = ?, numero_serie = ?,
         categoria = ?, factura = ?, fecha_adquisicion = ?, imagen = ?
     WHERE id = ?"
);
$stmtUpd->bind_param(
    "ssssssssi",
    $articulo, $marca, $modelo, $numero_serie,
    $categoria, $factura_path, $fecha_adquisicion, $imagen_path, $id
);

if ($stmtUpd->execute()) {
    $stmtUpd->close();
    header('Location: ' . BASE_URL . 'dashboard.php?msg=actualizado');
    exit;
} else {
    $err = $stmtUpd->error;
    $stmtUpd->close();
    die("Error al actualizar: " . htmlspecialchars($err));
}