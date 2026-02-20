<?php
ob_start();
include 'conexion.php';
require_once('tcpdf/tcpdf.php');
session_start();

function vacio($v) {
    return (!empty($v)) ? $v : 'N/A';
}

// Recibir datos
$usuario_id = intval($_POST['usuario_id'] ?? 0);
$articulo_ids = $_POST['articulo_id'] ?? [];

if (!is_array($articulo_ids)) {
    $articulo_ids = [$articulo_ids];
}

$articulos_validos = array_filter($articulo_ids, function($id) {
    return intval($id) > 0;
});

if (count($articulos_validos) === 0) {
    die("Error: Debe seleccionar al menos un artículo válido.");
}

$area = vacio($_POST['area'] ?? '');
$puesto = vacio($_POST['puesto'] ?? '');
$fecha = $_POST['fecha'] ?? date('Y-m-d');

// Datos generales
$pc_marca = vacio($_POST['pc_marca'] ?? '');
$pc_modelo = vacio($_POST['pc_modelo'] ?? '');
$pc_serie = vacio($_POST['pc_serie'] ?? '');
$pc_so = vacio($_POST['pc_so'] ?? '');
$pc_obs = vacio($_POST['pc_obs'] ?? '');

$cargador_obs = vacio($_POST['cargador_obs'] ?? '');

$monitor_marca = vacio($_POST['monitor_marca'] ?? '');
$monitor_modelo = vacio($_POST['monitor_modelo'] ?? '');
$monitor_serie = vacio($_POST['monitor_serie'] ?? '');
$monitor_obs = vacio($_POST['monitor_obs'] ?? '');

$cel_marca = vacio($_POST['cel_marca'] ?? '');
$cel_modelo = vacio($_POST['cel_modelo'] ?? '');
$cel_num_mod = vacio($_POST['cel_num_mod'] ?? '');
$cel_serie = vacio($_POST['cel_serie'] ?? '');
$cel_emei = vacio($_POST['cel_emei'] ?? '');
$cel_carga = vacio($_POST['cel_carga'] ?? '');
$cel_obs = vacio($_POST['cel_obs'] ?? '');

// Buscar usuario
$usuario = $conexion->query("SELECT nombre_completo FROM usuarios WHERE id = $usuario_id")->fetch_assoc();
$nombre_usuario = $usuario['nombre_completo'] ?? 'Empleado';

// Función para generar tabla con imágenes de evidencia
function generarTablaImagenes($imagenes) {
    if (empty($imagenes)) return 'N/A';
    $html = '<table style="border: none; width: 100%; text-align: center;"><tr>';
    foreach ($imagenes as $img) {
        if (file_exists($img)) {
            $html .= '<td style="border: none;"><img src="' . $img . '" width="140" height="100" style="border: 1px solid #000; margin: 5px;"></td>';
        }
    }
    $html .= '</tr></table>';
    return $html;
}

// Guardar evidencias
$evidencias_guardadas = [
    'pc_evidencia' => [],
    'monitor_evidencia' => [],
    'cel_evidencia' => [],
    'cargador_evidencia' => []
];

foreach ($evidencias_guardadas as $tipo => &$lista) {
    if (!empty($_FILES[$tipo]['name'][0])) {
        $carpeta = 'evidencias/';
        if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);
        foreach ($_FILES[$tipo]['tmp_name'] as $i => $tmpName) {
            $nombre = basename($_FILES[$tipo]['name'][$i]);
            $ruta = $carpeta . time() . "_$i" . "_" . $nombre;
            if (move_uploaded_file($tmpName, $ruta)) {
                $lista[] = $ruta;
            }
        }
    }
}

// Convertir evidencias a HTML
$pc_evidencia_html = generarTablaImagenes($evidencias_guardadas['pc_evidencia']);
$monitor_evidencia_html = generarTablaImagenes($evidencias_guardadas['monitor_evidencia']);
$cel_evidencia_html = generarTablaImagenes($evidencias_guardadas['cel_evidencia']);
$cargador_evidencia_html = generarTablaImagenes($evidencias_guardadas['cargador_evidencia']);

// Armar tabla HTML para los artículos seleccionados
$articulosHTML = '';
$articulosInsert = [];

foreach ($articulos_validos as $articulo_id) {
    $articulo_id = intval($articulo_id);
    $stmtCheck = $conexion->prepare("SELECT * FROM articulo WHERE id = ? AND estatus = 0");
    $stmtCheck->bind_param("i", $articulo_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows === 0) {
        // Omitir artículo no válido o ya asignado
        continue;
    }

    $articulo = $result->fetch_assoc();
    $articulosInsert[] = $articulo_id;

    $articulosHTML .= "
        <tr>
            <td>" . htmlspecialchars($articulo['articulo']) . "</td>
            <td>" . htmlspecialchars($articulo['marca']) . "</td>
            <td>" . htmlspecialchars($articulo['modelo']) . "</td>
            <td>" . htmlspecialchars($articulo['numero_serie']) . "</td>
            <td>" . htmlspecialchars($articulo['categoria']) . "</td>
        </tr>
    ";
}

if (empty($articulosInsert)) {
    die("Error: Ningún artículo válido para asignar.");
}

// Cargar plantilla HTML
$html = file_get_contents('Responsiva_SWL.html');

// Tabla para insertar en plantilla
$tablaHTML = "
    <table border='1' cellpadding='4' style='border-collapse: collapse; width: 100%;'>
        <thead>
            <tr style='background-color: #ddd;'>
                <th>Artículo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>No. Serie</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody>$articulosHTML</tbody>
    </table>
";

// Datos a reemplazar en plantilla
$datos = [
    '{pc_marca}' => $pc_marca,
    '{pc_modelo}' => $pc_modelo,
    '{pc_serie}' => $pc_serie,
    '{pc_so}' => $pc_so,
    '{pc_obs}' => $pc_obs,

    '{pc_cargador_marca}' => $pc_marca,
    '{pc_cargador_modelo}' => $pc_modelo,
    '{cargador_obs}' => $cargador_obs,

    '{monitor_marca}' => $monitor_marca,
    '{monitor_modelo}' => $monitor_modelo,
    '{monitor_serie}' => $monitor_serie,
    '{monitor_obs}' => $monitor_obs,

    '{cel_marca}' => $cel_marca,
    '{cel_modelo}' => $cel_modelo,
    '{cel_num_mod}' => $cel_num_mod,
    '{cel_serie}' => $cel_serie,
    '{cel_emei}' => $cel_emei,
    '{cel_carga}' => $cel_carga,
    '{cel_obs}' => $cel_obs,

    '{nombre}' => $nombre_usuario,
    '{area}' => $area,
    '{puesto}' => $puesto,
    '{fecha}' => $fecha,

    '{pc_evidencia}' => $pc_evidencia_html,
    '{monitor_evidencia}' => $monitor_evidencia_html,
    '{cel_evidencia}' => $cel_evidencia_html,
    '{cargador_evidencia}' => $cargador_evidencia_html,

    '{tabla_articulos}' => $tablaHTML,

    '{entrega_nombre_firma}' => 'Ing. Juan Pérez',
    '{recibe_nombre_firma}' => $nombre_usuario
];

// Reemplazar variables en plantilla
foreach ($datos as $clave => $valor) {
    $html = str_replace($clave, $valor, $html);
}

// Crear PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$carpetaPDF = "responsivas/";
if (!file_exists($carpetaPDF)) mkdir($carpetaPDF, 0777, true);

$nombreArchivo = 'responsiva_' . preg_replace('/\s+/', '_', $nombre_usuario) . '_' . date('Ymd_His') . '.pdf';
$rutaRelativa = $carpetaPDF . $nombreArchivo;
$pdf->Output(__DIR__ . '/' . $rutaRelativa, 'F');

// Guardar evidencia (JSON con rutas)
$evidenciaJson = $conexion->real_escape_string(json_encode($evidencias_guardadas));

foreach ($articulosInsert as $articulo_id) {
    // Prepara la consulta aquí dentro del ciclo para evitar referencias
    $stmtInsert = $conexion->prepare("INSERT INTO asignaciones (usuario_id, articulo_id, area, puesto, fecha, evidencia, pdf, estatus) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

    if (!$stmtInsert) {
        die("Error en prepare: " . $conexion->error);
    }

    // Variables locales para bind_param para evitar referencias problemáticas
    $uid = $usuario_id;
    $aid = $articulo_id;
    $ar = $area;
    $pu = $puesto;
    $fe = $fecha;
    $ev = $evidenciaJson;
    $pdf = $rutaRelativa;

    $stmtInsert->bind_param("iisssss", $uid, $aid, $ar, $pu, $fe, $ev, $pdf);

    if (!$stmtInsert->execute()) {
        die("Error al insertar asignación para artículo ID $aid: " . $stmtInsert->error);
    }

    $stmtInsert->close();

    // Actualizar estatus del artículo
    $update = $conexion->query("UPDATE articulo SET estatus = 1 WHERE id = $aid");
    if (!$update) {
        die("Error al actualizar estatus para artículo ID $aid: " . $conexion->error);
    }
}


ob_end_clean();
header("Location: $rutaRelativa");
exit;
?>
