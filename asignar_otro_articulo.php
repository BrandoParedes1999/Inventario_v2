<?php
include 'conexion.php';

$usuario_id = $_POST['usuario_id'];
$articulo_id = $_POST['articulo_id'];
$area = $_POST['area'];
$puesto = $_POST['puesto'];
$fecha = $_POST['fecha'];
$observaciones = $_POST['observaciones'];

// Guardar carpeta con nombre único
$carpeta = "evidencias/" . uniqid("asig_");
mkdir($carpeta, 0777, true);

$archivos_guardados = [];

if (!empty($_FILES['evidencia']['name'][0])) {
  foreach ($_FILES['evidencia']['tmp_name'] as $key => $tmp_name) {
    $nombreArchivo = basename($_FILES['evidencia']['name'][$key]);
    $rutaArchivo = "$carpeta/$nombreArchivo";
    if (move_uploaded_file($tmp_name, $rutaArchivo)) {
      $archivos_guardados[] = $nombreArchivo;
    }
  }
}

$evidencias_json = json_encode($archivos_guardados);

// Insertar en asignaciones
$stmt = $conexion->prepare("INSERT INTO asignaciones (usuario_id, articulo_id, area, puesto, fecha, observaciones, evidencia, carpeta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissssss", $usuario_id, $articulo_id, $area, $puesto, $fecha, $observaciones, $evidencias_json, $carpeta);
$stmt->execute();

// Cambiar estatus del artículo a asignado (1)
$conexion->query("UPDATE articulo SET estatus = 1 WHERE id = $articulo_id");

header("Location: usuarios.php");
