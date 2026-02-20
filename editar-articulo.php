<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $articulo = $_POST['articulo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $numero_serie = $_POST['numero_serie'];
    $categoria = $_POST['categoria'];
    $fecha_adquisicion = $_POST['fecha_adquisicion'];

    // Obtener imagen actual y factura actual
    $sql_actual = "SELECT imagen, factura FROM articulo WHERE id = $id";
    $resultado_actual = $conexion->query($sql_actual);
    $imagen_actual = '';
    $factura_actual = '';
    if ($resultado_actual && $resultado_actual->num_rows > 0) {
        $fila = $resultado_actual->fetch_assoc();
        $imagen_actual = $fila['imagen'];
        $factura_actual = $fila['factura'];
    }

    // =========================
    // PROCESAR IMAGEN NUEVA
    // =========================
    $target_dir = "img/productos/";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
        $extensiones_validas = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($tipo_archivo, $extensiones_validas)) {
            echo "Solo se permiten imágenes JPG o PNG.";
            exit();
        }

        $nombre_original = basename($_FILES['imagen']['name']);
        $nombre_limpio = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $nombre_original);
        $ruta_imagen = $target_dir . uniqid() . '_' . $nombre_limpio;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
            echo "Error al subir la nueva imagen";
            $ruta_imagen = $imagen_actual;
        }
    } else {
        $ruta_imagen = $imagen_actual;
    }

    // =========================
    // PROCESAR FACTURA PDF
    // =========================
    $facturaPath = $factura_actual; // valor por defecto: mantener la anterior
    if (isset($_FILES['factura']) && $_FILES['factura']['error'] == UPLOAD_ERR_OK) {
        $factura_tmp = $_FILES['factura']['tmp_name'];
        $factura_nombre = basename($_FILES['factura']['name']);
        $factura_ext = strtolower(pathinfo($factura_nombre, PATHINFO_EXTENSION));

        if ($factura_ext === "pdf") {
            $nuevoNombre = uniqid("factura_") . "." . $factura_ext;
            $rutaDestino = "facturas/" . $nuevoNombre;
            if (move_uploaded_file($factura_tmp, $rutaDestino)) {
                $facturaPath = $rutaDestino;
            }
        }
    }

    // =========================
    // ACTUALIZAR EN BD
    // =========================
    $sql = "UPDATE articulo SET 
                articulo = '$articulo',
                marca = '$marca',
                modelo = '$modelo',
                numero_serie = '$numero_serie',
                categoria = '$categoria',
                factura = '$facturaPath',
                fecha_adquisicion = '$fecha_adquisicion',
                imagen = '$ruta_imagen'
            WHERE id = $id";

    if ($conexion->query($sql)) {
        header("Location: dashboard.php?msg=actualizado");
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>
