<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $motivo_baja = isset($_POST['motivo_baja']) ? $conexion->real_escape_string($_POST['motivo_baja']) : '';

    // Obtener datos del artículo
    $sqlArticulo = "SELECT articulo, marca, modelo, numero_serie, categoria FROM articulo WHERE id = $id";
    $resultado = $conexion->query($sqlArticulo);

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        // Insertar en la tabla de bajas
        $sqlInsert = "INSERT INTO bajas_articulos (
                          articulo_id, articulo, marca, modelo, numero_serie, categoria, motivo_baja
                      ) VALUES (
                          $id,
                          '{$row['articulo']}',
                          '{$row['marca']}',
                          '{$row['modelo']}',
                          '{$row['numero_serie']}',
                          '{$row['categoria']}',
                          '$motivo_baja'
                      )";

        if ($conexion->query($sqlInsert)) {
            // Marcar artículo como deshabilitado
            $sqlUpdate = "UPDATE articulo SET estatus = 2 WHERE id = $id";
            $conexion->query($sqlUpdate);

            header("Location: dashboard.php?msg=articulo_dado_de_baja");
            exit;
        } else {
            echo "Error al registrar la baja: " . $conexion->error;
        }
    } else {
        echo "Artículo no encontrado.";
    }
}
?>
