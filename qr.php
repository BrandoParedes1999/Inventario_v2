<?php
// CORRECCIÓN: solo tenía include 'conexion.php' — cualquiera podía generar QRs
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check(); // Cualquier usuario autenticado puede ver QRs

require __DIR__ . '/phpqrcode/qrlib.php';

ob_clean();

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Content-Type: text/plain');
    echo "ID no proporcionado.";
    exit;
}

$stmt = $conexion->prepare(
    "SELECT articulo, modelo, marca, numero_serie, categoria, fecha_adquisicion
     FROM articulo WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($articulo, $modelo, $marca, $serie, $categoria, $fecha);

if ($stmt->fetch()) {
    $stmt->close();

    $contenidoQR = "Artículo: $articulo\nModelo: $modelo\nMarca: $marca\nSerie: $serie\nCategoría: $categoria\nFecha: $fecha";

    header('Content-Type: image/png');
    QRcode::png($contenidoQR);
    exit;
} else {
    $stmt->close();
    header('Content-Type: text/plain');
    echo "Artículo no encontrado.";
}