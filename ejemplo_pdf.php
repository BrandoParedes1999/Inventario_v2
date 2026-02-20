<?php
require_once('tcpdf/tcpdf.php');

// 1. Cargar HTML (tu plantilla ya modificada con {{variables}})
$html = file_get_contents('SWL-FOR-ADM-003.htm');

// 2. Reemplazar etiquetas con datos reales
$html = str_replace('{{nombre}}', 'Juan Pérez', $html);
$html = str_replace('{{area}}', 'Tecnología', $html);
$html = str_replace('{{puesto}}', 'Desarrollador', $html);
$html = str_replace('{{fecha}}', date('d/m/Y'), $html);

// 3. Crear el PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu empresa');
$pdf->SetTitle('Check List y Responsiva');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('responsiva.pdf', 'I'); // 'I' para mostrar en navegador, 'D' para descargar
?>
