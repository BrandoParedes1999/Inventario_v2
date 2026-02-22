<?php
ob_start();

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check();

// ─── Validar CSRF ─────────────────────────────────────────────────
// CORRECCIÓN: este handler no validaba el token CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=csrf');
    exit;
}

require_once __DIR__ . '/tcpdf/tcpdf.php';

function vacio($v) {
    return (!empty($v)) ? $v : 'N/A';
}

// ── Datos recibidos ───────────────────────────────────────────────
$usuario_id   = intval($_POST['usuario_id'] ?? 0);
$articulo_ids = $_POST['articulo_id'] ?? [];

if (!is_array($articulo_ids)) {
    $articulo_ids = [$articulo_ids];
}

$articulos_validos = array_filter($articulo_ids, fn($id) => intval($id) > 0);

if (count($articulos_validos) === 0) {
    die("Error: Debe seleccionar al menos un artículo válido.");
}

$area   = vacio($_POST['area']   ?? '');
$puesto = vacio($_POST['puesto'] ?? '');
$fecha  = $_POST['fecha'] ?? date('Y-m-d');

// ── Datos equipos ─────────────────────────────────────────────────
$pc_marca  = vacio($_POST['pc_marca']  ?? '');
$pc_modelo = vacio($_POST['pc_modelo'] ?? '');
$pc_serie  = vacio($_POST['pc_serie']  ?? '');
$pc_so     = vacio($_POST['pc_so']     ?? '');
$pc_obs    = vacio($_POST['pc_obs']    ?? '');

$cargador_obs = vacio($_POST['cargador_obs'] ?? '');

$monitor_marca  = vacio($_POST['monitor_marca']  ?? '');
$monitor_modelo = vacio($_POST['monitor_modelo'] ?? '');
$monitor_serie  = vacio($_POST['monitor_serie']  ?? '');
$monitor_obs    = vacio($_POST['monitor_obs']    ?? '');

$cel_marca   = vacio($_POST['cel_marca']   ?? '');
$cel_modelo  = vacio($_POST['cel_modelo']  ?? '');
$cel_num_mod = vacio($_POST['cel_num_mod'] ?? '');
$cel_serie   = vacio($_POST['cel_serie']   ?? '');
$cel_emei    = vacio($_POST['cel_emei']    ?? '');
$cel_carga   = vacio($_POST['cel_carga']   ?? '');
$cel_obs     = vacio($_POST['cel_obs']     ?? '');

$tel_marca     = vacio($_POST['tel_marca']     ?? '');
$tel_modelo    = vacio($_POST['tel_modelo']    ?? '');
$tel_serie     = vacio($_POST['tel_serie']     ?? '');
$tel_cargador  = vacio($_POST['tel_cargador']  ?? '');
$tel_extension = vacio($_POST['tel_extension'] ?? '');
$tel_obs       = vacio($_POST['tel_obs']       ?? '');

$adic_cantidades = $_POST['adic_cantidad'] ?? [];
$adic_articulos  = $_POST['adic_articulo'] ?? [];
$adic_series     = $_POST['adic_serie']    ?? [];
$adic_obs_arr    = $_POST['adic_obs']      ?? [];

// ── Buscar usuario ────────────────────────────────────────────────
$stmtUsr = $conexion->prepare("SELECT nombre_completo FROM usuarios WHERE id = ?");
$stmtUsr->bind_param("i", $usuario_id);
$stmtUsr->execute();
$usuario = $stmtUsr->get_result()->fetch_assoc();
$stmtUsr->close();
$nombre_usuario = $usuario['nombre_completo'] ?? 'Empleado';

// ── Función para generar tabla de imágenes ────────────────────────
function generarTablaImagenes($imagenes) {
    if (empty($imagenes)) return 'N/A';
    $html = '<table style="border:none;width:100%;text-align:center;"><tr>';
    foreach ($imagenes as $img) {
        // CORRECCIÓN: usar ruta absoluta para TCPDF
        $rutaAbs = BASE_PATH . $img;
        if (file_exists($rutaAbs)) {
            $html .= '<td style="border:none;"><img src="' . $rutaAbs . '" width="140" height="100" style="border:1px solid #000;margin:5px;"></td>';
        }
    }
    $html .= '</tr></table>';
    return $html;
}

// ── Guardar evidencias ────────────────────────────────────────────
// CORRECCIÓN: race condition — time() se llamaba dos veces (al guardar el archivo
// y al construir la ruta relativa), pudiendo devolver valores distintos si el
// segundo cruza un límite de segundo.
// Fix: fijar el timestamp UNA SOLA VEZ antes del bucle.
$evidencias_guardadas = [
    'pc_evidencia'          => [],
    'monitor_evidencia'     => [],
    'cel_evidencia'         => [],
    'cargador_evidencia'    => [],
    'tel_evidencia'         => [],
    'adicionales_evidencia' => [],
];

foreach ($evidencias_guardadas as $tipo => &$lista) {
    if (!empty($_FILES[$tipo]['name'][0])) {
        $timestamp = time(); // ← UN solo timestamp por lote de archivos
        foreach ($_FILES[$tipo]['tmp_name'] as $i => $tmpName) {
            $nombre      = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES[$tipo]['name'][$i]));
            $nombreFinal = $timestamp . '_' . $i . '_' . $nombre;
            $rutaAbs     = UPLOAD_EVIDENCIAS . $nombreFinal;
            $rutaRel     = 'uploads/evidencias/' . $nombreFinal;

            if (move_uploaded_file($tmpName, $rutaAbs)) {
                $lista[] = $rutaRel; // ruta relativa para guardar en BD y PDF
            }
        }
    }
}
unset($lista);

$pc_evidencia_html          = generarTablaImagenes($evidencias_guardadas['pc_evidencia']);
$monitor_evidencia_html     = generarTablaImagenes($evidencias_guardadas['monitor_evidencia']);
$cel_evidencia_html         = generarTablaImagenes($evidencias_guardadas['cel_evidencia']);
$cargador_evidencia_html    = generarTablaImagenes($evidencias_guardadas['cargador_evidencia']);
$tel_evidencia_html         = generarTablaImagenes($evidencias_guardadas['tel_evidencia']);
$adicionales_evidencia_html = generarTablaImagenes($evidencias_guardadas['adicionales_evidencia']);

// ── Filas artículos adicionales ───────────────────────────────────
$articulos_adicionales_filas = '';
if (!empty($adic_articulos)) {
    foreach ($adic_articulos as $i => $art) {
        if (empty(trim($art))) continue;
        $cant  = htmlspecialchars($adic_cantidades[$i] ?? '1');
        $art   = htmlspecialchars($art);
        $serie = htmlspecialchars($adic_series[$i]    ?? 'N/A');
        $obs   = htmlspecialchars($adic_obs_arr[$i]   ?? '');
        $articulos_adicionales_filas .= "
        <tr>
          <td style='text-align:center;'>$cant</td>
          <td>$art</td>
          <td>$serie</td>
          <td>$obs</td>
        </tr>";
    }
}
if (empty($articulos_adicionales_filas)) {
    $articulos_adicionales_filas = "<tr><td colspan='4' style='text-align:center;'>N/A</td></tr>";
}

// ── Procesar artículos del inventario ─────────────────────────────
$articulosHTML   = '';
$articulosInsert = [];

foreach ($articulos_validos as $articulo_id) {
    $articulo_id = intval($articulo_id);
    $estDisp     = ART_DISPONIBLE;

    $stmtChk = $conexion->prepare("SELECT * FROM articulo WHERE id = ? AND estatus = ?");
    $stmtChk->bind_param("ii", $articulo_id, $estDisp);
    $stmtChk->execute();
    $result = $stmtChk->get_result();
    $stmtChk->close();

    if ($result->num_rows === 0) continue;

    $articulo          = $result->fetch_assoc();
    $articulosInsert[] = $articulo_id;

    $articulosHTML .= "
        <tr>
            <td>" . htmlspecialchars($articulo['articulo'])     . "</td>
            <td>" . htmlspecialchars($articulo['marca'])        . "</td>
            <td>" . htmlspecialchars($articulo['modelo'])       . "</td>
            <td>" . htmlspecialchars($articulo['numero_serie']) . "</td>
            <td>" . htmlspecialchars($articulo['categoria'])    . "</td>
        </tr>";
}

if (empty($articulosInsert)) {
    die("Error: Ningún artículo válido para asignar.");
}

// ── Cargar plantilla HTML ─────────────────────────────────────────
$templatePath = __DIR__ . '/templates/Responsiva_SWL.html';
if (!file_exists($templatePath)) {
    die("Error: No se encontró la plantilla en $templatePath");
}
$html = file_get_contents($templatePath);

$tablaHTML = "
    <table border='1' cellpadding='4' style='border-collapse:collapse;width:100%;'>
        <thead>
            <tr style='background-color:#ddd;'>
                <th>Artículo</th><th>Marca</th><th>Modelo</th><th>No. Serie</th><th>Categoría</th>
            </tr>
        </thead>
        <tbody>$articulosHTML</tbody>
    </table>";

// ── CORRECCIÓN: logo — TCPDF necesita ruta absoluta de sistema de archivos ──
// La plantilla usa <img src="Logo_SWL.png"> (ruta relativa) → imagen en blanco.
// Se reemplaza con la ruta absoluta antes de pasar a TCPDF.
$logoAbsoluto = __DIR__ . '/Logo_SWL.png';
$html = str_replace('src="Logo_SWL.png"', 'src="' . $logoAbsoluto . '"', $html);

$datos = [
    '{pc_marca}'                    => $pc_marca,
    '{pc_modelo}'                   => $pc_modelo,
    '{pc_serie}'                    => $pc_serie,
    '{pc_so}'                       => $pc_so,
    '{pc_obs}'                      => $pc_obs,
    '{pc_cargador_marca}'           => $pc_marca,
    '{pc_cargador_modelo}'          => $pc_modelo,
    '{cargador_obs}'                => $cargador_obs,
    '{monitor_marca}'               => $monitor_marca,
    '{monitor_modelo}'              => $monitor_modelo,
    '{monitor_serie}'               => $monitor_serie,
    '{monitor_obs}'                 => $monitor_obs,
    '{cel_marca}'                   => $cel_marca,
    '{cel_modelo}'                  => $cel_modelo,
    '{cel_num_mod}'                 => $cel_num_mod,
    '{cel_serie}'                   => $cel_serie,
    '{cel_emei}'                    => $cel_emei,
    '{cel_carga}'                   => $cel_carga,
    '{cel_obs}'                     => $cel_obs,
    '{tel_marca}'                   => $tel_marca,
    '{tel_modelo}'                  => $tel_modelo,
    '{tel_serie}'                   => $tel_serie,
    '{tel_cargador}'                => $tel_cargador,
    '{tel_extension}'               => $tel_extension,
    '{tel_obs}'                     => $tel_obs,
    '{nombre}'                      => htmlspecialchars($nombre_usuario),
    '{area}'                        => htmlspecialchars($area),
    '{puesto}'                      => htmlspecialchars($puesto),
    '{fecha}'                       => htmlspecialchars($fecha),
    '{pc_evidencia}'                => $pc_evidencia_html,
    '{monitor_evidencia}'           => $monitor_evidencia_html,
    '{cel_evidencia}'               => $cel_evidencia_html,
    '{cargador_evidencia}'          => $cargador_evidencia_html,
    '{tel_evidencia}'               => $tel_evidencia_html,
    '{adicionales_evidencia}'       => $adicionales_evidencia_html,
    '{articulos_adicionales_filas}' => $articulos_adicionales_filas,
    '{tabla_articulos}'             => $tablaHTML,
    '{entrega_nombre_firma}'        => 'Ing. Juan Pérez',
    '{recibe_nombre_firma}'         => htmlspecialchars($nombre_usuario),
];

foreach ($datos as $clave => $valor) {
    $html = str_replace($clave, $valor, $html);
}

// ── Generar PDF ───────────────────────────────────────────────────
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$nombreArchivo = 'responsiva_' . preg_replace('/\s+/', '_', $nombre_usuario) . '_' . date('Ymd_His') . '.pdf';
$rutaAbsoluta  = UPLOAD_RESPONSIVAS . $nombreArchivo;
$rutaRelativa  = 'uploads/responsivas/' . $nombreArchivo;

$pdf->Output($rutaAbsoluta, 'F');

// ── Guardar asignaciones en BD ────────────────────────────────────
// Nota: evidencia almacena el JSON completo → la columna ahora es TEXT (migracion_v2.sql)
$evidenciaJson = json_encode($evidencias_guardadas);

foreach ($articulosInsert as $aid) {
    $stmtIns = $conexion->prepare(
        "INSERT INTO asignaciones (usuario_id, articulo_id, area, puesto, fecha, evidencia, pdf, estatus)
         VALUES (?, ?, ?, ?, ?, ?, ?, 1)"
    );
    if (!$stmtIns) die("Error en prepare: " . $conexion->error);

    $stmtIns->bind_param("iisssss",
        $usuario_id, $aid, $area, $puesto, $fecha, $evidenciaJson, $rutaRelativa
    );
    if (!$stmtIns->execute()) die("Error al insertar asignación: " . $stmtIns->error);
    $stmtIns->close();

    $stmtEst = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ?");
    $est     = ART_ASIGNADO;
    $stmtEst->bind_param("ii", $est, $aid);
    $stmtEst->execute();
    $stmtEst->close();
}

ob_end_clean();
header('Location: ' . BASE_URL . $rutaRelativa);
exit;