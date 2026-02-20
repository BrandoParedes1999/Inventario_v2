<?php
include 'conexion.php'; // $conexion mysqli

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['empresa_id'])) {
        header("Location: empresa.php?error=missing_id");
        exit;
    }

    $id = intval($_POST['empresa_id']);

    // Obtener el nombre del archivo del logo antes de borrar
    $sql_logo = "SELECT logo FROM empresa WHERE id = ?";
    $stmt_logo = $conexion->prepare($sql_logo);
    $stmt_logo->bind_param("i", $id);
    $stmt_logo->execute();
    $stmt_logo->bind_result($logo);
    $stmt_logo->fetch();
    $stmt_logo->close();

    // Eliminar de base de datos
    $sql = "DELETE FROM empresa WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Borrar imagen si existe
        $logo_path = "logos/" . $logo;
        if (!empty($logo) && file_exists($logo_path)) {
            unlink($logo_path);
        }
        header("Location: empresa.php?eliminado=1");
        exit;
    } else {
        echo "Error al eliminar empresa.";
    }

    $stmt->close();
    $conexion->close();
}
?>
