<?php
$conexion = new mysqli("localhost", "root", "", "inventario");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>


