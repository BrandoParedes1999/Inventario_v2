<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_articulo = $_POST['articulo'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $no_serie = $_POST['serie'];
    $categoria = $_POST['categoria'];
    $fecha_adquisicion = $_POST['fecha_compra'];
    
    // Manejo de imagen
    $imagen = $_FILES['imagen']['name'];
    $target_dir_imagen = "img/productos/";
    $imagen_path = $target_dir_imagen . basename($imagen);
    move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_path);

    // Manejo del PDF factura
    if (isset($_FILES['factura']) && $_FILES['factura']['error'] == 0) {
        $allowed = ['application/pdf'];
        $fileType = mime_content_type($_FILES['factura']['tmp_name']);

        if (!in_array($fileType, $allowed)) {
            die("Solo se permiten archivos PDF para la factura.");
        }

        $target_dir_factura = "facturas/";  // Carpeta de facturas
        if (!is_dir($target_dir_factura)) {
            mkdir($target_dir_factura, 0755, true);
        }
        
        $factura_name = uniqid() . '_' . basename($_FILES['factura']['name']);
        $factura_path = $target_dir_factura . $factura_name;

        if (!move_uploaded_file($_FILES['factura']['tmp_name'], $factura_path)) {
            die("Error al subir la factura.");
        }
    } else {
        die("Debes seleccionar un archivo PDF para la factura.");
    }

    $cantidad = 1;
    $estatus = 0;

    // Insertar todo en DB, incluye la ruta del PDF
    $sql_insert = "INSERT INTO articulo (articulo, modelo, marca, numero_serie, categoria, fecha_adquisicion, cantidad, imagen, factura, estatus)
                   VALUES ('$tipo_articulo', '$modelo', '$marca', '$no_serie', '$categoria', '$fecha_adquisicion', '$cantidad', '$imagen_path', '$factura_path', '$estatus')";

    if ($conexion->query($sql_insert) === TRUE) {
        echo "<script>alert('Artículo agregado exitosamente'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error al insertar los datos: " . $conexion->error;
    }
}
?>
