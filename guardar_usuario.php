<?php
include 'conexion.php';

$nombre_completo = $_POST['nombre_completo'];
$usuario = $_POST['usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
$correo = $_POST['correo'];
$rol = $_POST['rol'];
$estatus = $_POST['estatus'];


$sql = "INSERT INTO usuarios (nombre_completo, usuario, contrasena, correo, rol, estatus)
        VALUES ('$nombre_completo', '$usuario', '$contrasena', '$correo', '$rol', '$estatus')";

if (mysqli_query($conexion, $sql)) {
    header("Location: usuarios.php");
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
}

?>