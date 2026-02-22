<?php
// CORRECCIÓN: solo tenía session_start() sin validar sesión real
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check();

// Puede venir por sesión (flujo original) o por parámetro GET
$file = null;

if (isset($_SESSION['ruta_pdf']) && !empty($_SESSION['ruta_pdf'])) {
    $file = $_SESSION['ruta_pdf'];
    unset($_SESSION['ruta_pdf']); // Limpiar después de usar
} elseif (isset($_GET['pdf']) && !empty($_GET['pdf'])) {
    // Sanitizar: solo rutas dentro de uploads/responsivas/
    $solicitado = $_GET['pdf'];
    $base       = realpath(__DIR__ . '/uploads/responsivas/');
    $candidato  = realpath(__DIR__ . '/' . $solicitado);

    // Solo permitir archivos dentro de uploads/responsivas/
    if ($candidato !== false && str_starts_with($candidato, $base)) {
        $file = $candidato;
    }
}

if ($file && file_exists($file)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}

// Archivo no encontrado
http_response_code(404);
echo '<h3>PDF no encontrado.</h3>';
echo '<a href="' . BASE_URL . 'dashboard.php">Regresar al inventario</a>';