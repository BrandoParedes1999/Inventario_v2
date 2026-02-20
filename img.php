<?php
// Procesar el formulario para agregar datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_articulo = $_POST['tipo_articulo'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $no_serie = $_POST['no_serie'];
    $categoria = $_POST['categoria'];
    $factura = $_POST['factura'];
    $fecha_adquisicion = $_POST['fecha_adquisicion'];
    $cantidad = $_POST['cantidad'];

    // Manejo de archivos (imagen y QR)
    //$imagen = $_FILES['imagen']['name'];
    $qr = $_FILES['qr']['name'];
    // Cargar la imagen
    $target_dir = "img/productos/"; // Carpeta donde se guardará la imagen
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


    $target_dir = "img/productos/";

    // Validar la imagen antes de moverla
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
    $tipo_archivo = mime_content_type($_FILES['imagen']['tmp_name']);
    $extensiones_validas = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($tipo_archivo, $extensiones_validas)) {
        echo "Solo se permiten imágenes en formato JPG o PNG.";
        exit();
    }
    } else {
    echo "Debes seleccionar una imagen válida.";
    exit();
    }

    // Si pasó la validación, subir la imagen igual que antes
    
    move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file);

    $target_file = $target_dir . basename($_FILES["imagen"]["name"]); // Ruta completa del archivo
    move_uploaded_file($_FILES['imagen']['tmp_name'], $target_dir.$imagen);
    move_uploaded_file($_FILES['qr']['tmp_name'], $target_dir.$qr);

    $sql_insert = "INSERT INTO stock (tipo_articulo, modelo, marca, no_serie, categoria, imagen, factura, fecha_adquisicion, cantidad, qr)
                   VALUES ('$tipo_articulo', '$modelo', '$marca', '$no_serie', '$categoria', '$target_file', '$factura', '$fecha_adquisicion', '$cantidad', '$qr')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Artículo agregado exitosamente'); window.location.href='stock.php';</script>";
    } else {
        echo "Error al insertar los datos: " . $conn->error;
    }
}

// Eliminar registro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM stock WHERE id=$id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Artículo eliminado exitosamente'); window.location.href='stock.php';</script>";
    } else {
        echo "Error al eliminar los datos: " . $conn->error;
    }
}
?>