<?php
require 'phpqrcode/qrlib.php';
include 'conexion.php';

ob_clean();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Buscar el artículo por ID
    $sql = "SELECT articulo, modelo, marca, numero_serie, categoria, fecha_adquisicion FROM articulo WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($articulo, $modelo, $marca, $serie, $categoria, $fecha);

    if ($stmt->fetch()) {
        // Aquí decides qué datos incluir en el QR
        $contenidoQR = "Artículo: $articulo\nModelo: $modelo\nMarca: $marca\nSerie: $serie\nCategoria: $categoria\nFecha: $fecha";

        header('Content-Type: image/png');

        QRcode::png($contenidoQR);
        exit;
    } else {
        header('Content-Type: text/plain');
        echo "Artículo no encontrado.";
    }

    $stmt->close();
} else {
    header('Content-Type: text/plain');
    echo "ID no proporcionado.";
}
?>