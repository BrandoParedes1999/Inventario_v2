<?php
session_start();

if (isset($_SESSION['ruta_pdf']) && file_exists($_SESSION['ruta_pdf'])) {
    $file = $_SESSION['ruta_pdf'];
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($file) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    readfile($file);
    exit();
} else {
    echo "El archivo PDF no fue encontrado.";
}
