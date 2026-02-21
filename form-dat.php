<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin(); // Solo administradores pueden agregar artículos

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

// Validar CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=csrf');
    exit;
}

$tipo_articulo     = trim($_POST['articulo']     ?? '');
$modelo            = trim($_POST['modelo']       ?? '');
$marca             = trim($_POST['marca']        ?? '');
$no_serie          = trim($_POST['serie']        ?? '');
$categoria         = trim($_POST['categoria']    ?? '');
$fecha_adquisicion = trim($_POST['fecha_compra'] ?? '');

if (empty($tipo_articulo) || empty($modelo) || empty($marca)) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=campos_vacios');
    exit;
}

// ── Imagen ────────────────────────────────────────────────────────
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    die("Debes seleccionar una imagen.");
}

$tipo_mime     = mime_content_type($_FILES['imagen']['tmp_name']);
$mimes_validos = ['image/jpeg', 'image/png', 'image/jpg'];

if (!in_array($tipo_mime, $mimes_validos)) {
    die("Solo se permiten imágenes JPG o PNG.");
}

$target_dir_imagen = UPLOAD_IMAGENES;
$ext_imagen        = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
$nombre_imagen     = uniqid() . '_' . time() . '.' . $ext_imagen;
$imagen_path       = 'uploads/imagenes/' . $nombre_imagen;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target_dir_imagen . $nombre_imagen)) {
    die("Error al subir la imagen.");
}

// ── Factura PDF ───────────────────────────────────────────────────
if (!isset($_FILES['factura']) || $_FILES['factura']['error'] !== UPLOAD_ERR_OK) {
    die("Debes seleccionar un archivo PDF para la factura.");
}

$tipo_factura = mime_content_type($_FILES['factura']['tmp_name']);
if ($tipo_factura !== 'application/pdf') {
    die("Solo se permiten archivos PDF para la factura.");
}

$nombre_factura = uniqid('factura_') . '.pdf';
$factura_path   = 'uploads/facturas/' . $nombre_factura;

if (!move_uploaded_file($_FILES['factura']['tmp_name'], UPLOAD_FACTURAS . $nombre_factura)) {
    die("Error al subir la factura.");
}

// ── Insertar con prepared statement ──────────────────────────────
$cantidad = 1;
$estatus  = ART_DISPONIBLE; // 0

$stmt = $conexion->prepare(
    "INSERT INTO articulo (articulo, modelo, marca, numero_serie, categoria, fecha_adquisicion, cantidad, imagen, factura, estatus)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "ssssssissi",
    $tipo_articulo, $modelo, $marca, $no_serie, $categoria,
    $fecha_adquisicion, $cantidad, $imagen_path, $factura_path, $estatus
);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ' . BASE_URL . 'dashboard.php?msg=articulo_agregado');
    exit;
} else {
    $err = $stmt->error;
    $stmt->close();
    die("Error al insertar los datos: " . htmlspecialchars($err));
}