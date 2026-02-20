<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['articulo_id'] ?? 0);
    $motivo_restauracion = $conexion->real_escape_string($_POST['motivo_restauracion'] ?? 'Restauración sin motivo');
    $usuario_id = intval($_POST['usuario_id'] ?? 0);

    // Restaurar en bajas_articulos
    $sql_baja = "SELECT id FROM bajas_articulos WHERE articulo_id = $id AND fecha_baja IS NOT NULL ORDER BY fecha_baja DESC LIMIT 1";
    $resultado_baja = $conexion->query($sql_baja);
    if ($resultado_baja && $resultado_baja->num_rows > 0) {
        $baja = $resultado_baja->fetch_assoc();
        $baja_id = $baja['id'];
        $conexion->query("UPDATE bajas_articulos SET motivo_restauracion = '$motivo_restauracion', fecha_restauracion = NOW() WHERE id = $baja_id");
    }

    // Marcar como devuelto en asignaciones
    $check_asignacion = $conexion->query("SELECT id FROM asignaciones WHERE articulo_id = $id AND fecha_devolucion IS NULL ORDER BY fecha DESC LIMIT 1");
    if ($check_asignacion && $check_asignacion->num_rows > 0) {
        $row_asignacion = $check_asignacion->fetch_assoc();
        $asignacion_id = $row_asignacion['id'];
        $conexion->query("UPDATE asignaciones SET fecha_devolucion = NOW() WHERE id = $asignacion_id");
    }

    // Cambiar artículo a disponible
    $conexion->query("UPDATE articulo SET estatus = 0 WHERE id = $id");

    // Actualizar asignación con estatus = 0
    if ($usuario_id > 0) {
        $sql_asignacion = "SELECT id FROM asignaciones WHERE articulo_id = $id AND usuario_id = $usuario_id ORDER BY fecha DESC LIMIT 1";
        $result = $conexion->query($sql_asignacion);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $asignacion_id = $row['id'];
            $conexion->query("UPDATE asignaciones SET estatus = 0 WHERE id = $asignacion_id");
        }
    }

    header("Location: dashboard.php?msg=restaurado");
    exit;
}
?>
