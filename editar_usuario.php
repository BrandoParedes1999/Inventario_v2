<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre_completo'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $estatus = $_POST['estatus'];

    $sql = "UPDATE usuarios SET nombre_completo=?, usuario=?, correo=?, rol=?, estatus=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssii", $nombre, $usuario, $correo, $rol, $estatus, $id);
    $stmt->execute();

    header("Location: usuarios.php"); // o el archivo donde cargas los usuarios
    exit();
}
