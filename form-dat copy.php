<?php
include 'conexion.php';
// Procesar el formulario para agregar datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_articulo = $_POST['articulo'];
    $modelo = $_POST['marca'];
    $marca = $_POST['modelo'];
    $no_serie = $_POST['serie'];
    $categoria = $_POST['categoria'];
    $factura = $_POST['factura'];
    $fecha_adquisicion = $_POST['fecha_compra'];
    //$cantidad = $_POST['cantidad'];

    //variables autorellenadas
    $cantidad = "1";
    $estatus = "0";

    // Manejo de archivos (imagen y QR)
    $imagen = $_FILES['imagen']['name'];
    //$qr = $_FILES['qr']['name'];

    // Cargar la imagen
    $target_dir = "img/productos/"; // Carpeta donde se guardará la imagen
    $imagen_path = $target_dir . basename($imagen); //Ruta completa del archivo
    
    //$qr_path = $target_dir . basename($qr);

    move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_path);
    //move_uploaded_file($_FILES['qr']['tmp_name'], $qr);

    $sql_insert = "INSERT INTO articulo (articulo, modelo, marca, numero_serie, categoria, fecha_adquisicion, cantidad, imagen, estatus)
                   VALUES ('$tipo_articulo', '$modelo', '$marca', '$no_serie', '$categoria', '$fecha_adquisicion', '$cantidad', '$imagen_path', '$estatus')";

    if ($conexion->query($sql_insert) === TRUE) {
        echo "<script>alert('Artículo agregado exitosamente');</script>";
    } else {
        echo "Error al insertar los datos: " . $conexion->error;
    }
}

// Eliminar registro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_update = "UPDATE FROM articulo WHERE id=$id";
    if ($conexion->query($sql_update) === TRUE) {
        echo "<script>alert('Artículo eliminado exitosamente'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error al eliminar los datos: " . $conexion->error;
    }
}
?>

