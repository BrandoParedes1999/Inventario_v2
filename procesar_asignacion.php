<?php
include 'conexion.php';
require_once('tcpdf/tcpdf.php');

session_start();

// Datos del formulario
$usuario_id = intval($_POST['usuario_id']);
$area = $_POST['area'] ?? '';
$puesto = $_POST['puesto'] ?? '';
$fecha = $_POST['fecha'] ?? date('Y-m-d');
$articulos = $_POST['articulos'] ?? [];
$fotos = $_FILES['fotos'] ?? null;

// Obtener nombre del usuario
$sqlUser = $conexion->query("SELECT nombre_completo FROM usuarios WHERE id = $usuario_id");
$userData = $sqlUser->fetch_assoc();
$nombre_usuario = $userData ? $userData['nombre_completo'] : 'Desconocido';

// Crear carpeta para responsiva y fotos
//$timestamp = time();
$carpeta = "responsivas/" . $nombre_usuario . "_" . $fecha;
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Subir fotos
$fotoPaths = [];
if ($fotos && !empty($fotos['tmp_name'][0])) {
    foreach ($fotos['tmp_name'] as $i => $tmp_name) {
        $nombreFoto = basename($fotos['name'][$i]);
        $ruta = $carpeta . DIRECTORY_SEPARATOR . $nombreFoto;
        if (move_uploaded_file($tmp_name, $ruta)) {
            $fotoPaths[] = $ruta;
        }
    }
}

// Crear PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Sin header ni footer predeterminados
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

// --- Encabezado ---
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'CHECK LIST Y RESPONSIVA DE HERRAMIENTAS DE TRABAJO', 0, 1, 'C');
$pdf->Ln(3);

// --- Datos generales ---
$pdf->SetFont('helvetica', '', 11);
$texto_general = "Fecha de emisión: $fecha
Número de control: SWL-FOR-ADM-003
Clasificación del Documento: Controlado
Tipo de Documento: Formato
Revisión Núm.: 00";
$pdf->MultiCell(0, 6, $texto_general, 0, 'L');
$pdf->Ln(5);

$texto_intro = "Sírvase de este presente como Check List y Responsiva de herramientas de trabajo, con las siguientes características, que pertenece a la empresa de Servicios Marinos y Logísticos Shark S.A. de C.V., el cual se compromete en hacer uso de este de manera exclusiva dentro del ámbito de trabajo a:";
$pdf->MultiCell(0, 6, $texto_intro, 0, 'L');
$pdf->Ln(5);

// --- Datos del empleado ---
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 6, "Nombre: $nombre_usuario", 0, 1);
$pdf->Cell(0, 6, "Área: $area", 0, 1);
$pdf->Cell(0, 6, "Puesto: $puesto", 0, 1);
$pdf->Cell(0, 6, "Fecha: $fecha", 0, 1);
$pdf->Ln(5);

// --- Artículos asignados ---
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 7, "Datos I - Equipos electrónicos", 0, 1);
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 10);
foreach ($articulos as $id_articulo) {
    $id_articulo = intval($id_articulo);
    $sqlArt = $conexion->query("SELECT articulo, marca, modelo, numero_serie FROM articulo WHERE id = $id_articulo");
    if ($sqlArt && $sqlArt->num_rows > 0) {
        $art = $sqlArt->fetch_assoc();
        $texto_articulo = "{$art['articulo']}
  Marca: {$art['marca']}
  Modelo: {$art['modelo']}
  Número de serie: {$art['numero_serie']}
Observaciones: ______________________";
        $pdf->MultiCell(0, 7, $texto_articulo, 0, 'L');
        $pdf->Ln(3);
    }
    // Marcar artículo como asignado
    $conexion->query("UPDATE articulo SET estatus = 1 WHERE id = $id_articulo");
}

// --- Evidencia fotográfica en tabla pequeña ---
if (!empty($fotoPaths)) {
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 8, "Evidencia Fotográfica:", 0, 1);
    $pdf->Ln(2);

    $ancho_celda = 60; // ancho de cada celda
    $alto_celda = 50;  // alto de cada celda
    $imagenes_por_fila = 3; // imágenes por fila
    $x_inicial = $pdf->GetX();
    $y_inicial = $pdf->GetY();

    $contador = 0;
    foreach ($fotoPaths as $foto) {
        // Dibujar borde celda
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $ancho_celda, $alto_celda);

        // Insertar imagen con margen dentro de la celda
        $pdf->Image($foto, $pdf->GetX() + 3, $pdf->GetY() + 3, $ancho_celda - 6, $alto_celda - 6, '', '', '', false, 300, '', false, false, 0, false, false, false);

        // Mover a la siguiente celda horizontalmente
        $pdf->SetXY($pdf->GetX() + $ancho_celda, $pdf->GetY());

        $contador++;
        if ($contador % $imagenes_por_fila == 0) {
            // Saltar a siguiente fila
            $pdf->Ln($alto_celda);
            $pdf->SetX($x_inicial);
        }
    }
    $pdf->Ln($alto_celda + 5);
}

// --- Nota de responsabilidad ---
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$nota = "El receptor $nombre_usuario asume la responsabilidad y el cuidado de las herramientas de trabajo y se compromete a utilizarlo estrictamente de manera laboral. No se podrá instalar programas ajenos que vienen preinstalados por defecto. Cabe mencionar que todo daño o desperfecto que se le ocasione a los equipos antes mencionados será pagado por el receptor.";
$pdf->MultiCell(0, 7, $nota, 0, 'L');
$pdf->Ln(15);

// --- Firmas ---
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(90, 6, "ENTREGA", 0, 0, 'C');
$pdf->Cell(90, 6, "RECIBE", 0, 1, 'C');
$pdf->Ln(20);
$pdf->Cell(90, 6, "_________________________", 0, 0, 'C');
$pdf->Cell(90, 6, "_________________________", 0, 1, 'C');
$pdf->Cell(90, 6, "Nombre y Firma", 0, 0, 'C');
$pdf->Cell(90, 6, "Nombre y Firma", 0, 1, 'C');

// Guardar PDF en ruta absoluta
$nombre_pdf = "responsiva_" . $usuario_id . ".pdf";
$ruta_completa = __DIR__ . DIRECTORY_SEPARATOR . $carpeta . DIRECTORY_SEPARATOR . $nombre_pdf;

$pdf->Output($ruta_completa, 'F');

// Guardar ruta en sesión y redirigir a ver_responsiva.php
$_SESSION['ruta_pdf'] = $ruta_completa;
header('Location: ver_responsiva.php');
exit();
