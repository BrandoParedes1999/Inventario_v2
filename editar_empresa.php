<?php 
include 'conexion.php'; // $conexion es tu conexión mysqli

// Verificar que se recibió el ID y el nombre
if (!isset($_POST['empresa_id'], $_POST['nombre_empresa'])) {
    header('Location: empresa.php?error=missing_data');
    exit;
}

$empresa_id = intval($_POST['empresa_id']);
$nombre = trim($_POST['nombre_empresa']);

// Obtener datos actuales para el logo
$sql = "SELECT logo FROM empresa WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $empresa_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: empresa.php?error=not_found');
    exit;
}

$empresa = $result->fetch_assoc();
$logo_actual = $empresa['logo'];

$nuevo_logo = $logo_actual;

// Procesar nuevo logo si se subió
if (isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['logo_empresa']['tmp_name'];
    $fileName = $_FILES['logo_empresa']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($fileExtension, $allowedfileExtensions)) {
        $nuevoNombreArchivo = 'logo_' . $empresa_id . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = 'logos/';  // O la carpeta que uses para logos
        $dest_path = $uploadFileDir . $nuevoNombreArchivo;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Borrar logo anterior si existe
            if ($logo_actual && file_exists($uploadFileDir . $logo_actual)) {
                unlink($uploadFileDir . $logo_actual);
            }
            $nuevo_logo = $nuevoNombreArchivo;
        } else {
            header('Location: empresa.php?error=upload_failed');
            exit;
        }
    } else {
        header('Location: empresa.php?error=invalid_extension');
        exit;
    }
}

// Actualizar nombre y logo
$sql_update = "UPDATE empresa SET nombre = ?, logo = ? WHERE id = ?";
$stmt_update = $conexion->prepare($sql_update);
$stmt_update->bind_param('ssi', $nombre, $nuevo_logo, $empresa_id);

if ($stmt_update->execute()) {
    header('Location: empresa.php?success=updated');
} else {
    header('Location: empresa.php?error=update_failed');
}

$stmt->close();
$stmt_update->close();
$conexion->close();
