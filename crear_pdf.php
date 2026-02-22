<?php
// ─── MOSTRAR ERRORES (quitar en producción) ────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check();

// ─── CSRF ──────────────────────────────────────────────────────────
$csrfToken = $_POST['csrf_token'] ?? '';
if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    header('Location: ' . BASE_URL . 'usuarios.php?error=csrf');
    exit;
}

// ─── TCPDF: detectar ruta automáticamente ──────────────────────────
$posiblesTcpdf = [
    __DIR__ . '/tcpdf/tcpdf.php',
    __DIR__ . '/lib/tcpdf/tcpdf.php',
    __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php',
    __DIR__ . '/TCPDF/tcpdf.php',
];
$tcpdfCargado = false;
foreach ($posiblesTcpdf as $ruta) {
    if (file_exists($ruta)) {
        require_once $ruta;
        $tcpdfCargado = true;
        break;
    }
}
if (!$tcpdfCargado) {
    ob_end_clean();
    die('<div style="font-family:Arial;padding:30px;background:#fff3cd;border:1px solid #ffc107;">'
      . '<h3>&#9888; Error: No se encontró TCPDF</h3>'
      . '<p>Se buscó en:</p><ul>'
      . implode('', array_map(fn($r) => "<li><code>$r</code></li>", $posiblesTcpdf))
      . '</ul><p>Verifica que la carpeta <strong>tcpdf</strong> esté dentro de <code>inventario_v2/</code>.</p>'
      . '<a href="javascript:history.back()">← Regresar</a>'
      . '</div>');
}

function vacio($v) {
    return (!empty($v)) ? $v : 'N/A';
}

// ─── 1. LEER ADICIONALES ANTES DE VALIDAR ──────────────────────────
$adic_cantidades   = $_POST['adic_cantidad']    ?? [];
$adic_articulos    = $_POST['adic_articulo']    ?? [];
$adic_series       = $_POST['adic_serie']       ?? [];
$adic_obs_arr      = $_POST['adic_obs']         ?? [];
// IDs de los artículos del inventario seleccionados en adicionales
// Vacío ('') si la fila fue escrita a mano
$adic_articulo_ids = $_POST['adic_articulo_id'] ?? [];

$adic_validos     = array_filter($adic_articulos, fn($a) => trim($a) !== '');
$tieneAdicionales = !empty($_POST['activar_adicionales']) && !empty($adic_validos);

// ─── 2. ARTÍCULOS DEL INVENTARIO (secciones PC/Monitor/Cel/Tel) ────
$articulo_ids = $_POST['articulo_id'] ?? [];
if (!is_array($articulo_ids)) {
    $articulo_ids = [$articulo_ids];
}
$articulos_validos = array_filter($articulo_ids, fn($id) => intval($id) > 0);

// ─── 3. VALIDACIÓN COMBINADA ────────────────────────────────────────
if (count($articulos_validos) === 0 && !$tieneAdicionales) {
    $seccionesActivas = array_filter([
        !empty($_POST['activar_pc'])       ? 'PC/Laptop'     : null,
        !empty($_POST['activar_monitor'])  ? 'Monitor'       : null,
        !empty($_POST['activar_celular'])  ? 'Celular'       : null,
        !empty($_POST['activar_telefono']) ? 'Teléfono fijo' : null,
    ]);
    $hint = !empty($seccionesActivas)
        ? 'Activaste: ' . implode(', ', $seccionesActivas) .
          '. Para registrar un equipo debes <strong>seleccionar su N&deg; de serie desde la lista desplegable</strong>.'
        : 'Activa al menos una sección y selecciona el N&deg; de serie, o activa "Artículos adicionales" y llena al menos una fila.';
    ob_end_clean();
    die('<div style="font-family:Arial;padding:30px;">'
      . '<h4 style="color:#c00;">Error: No hay artículos para asignar</h4>'
      . '<p>' . $hint . '</p>'
      . '<a href="javascript:history.back()" style="color:blue;">&larr; Regresar</a>'
      . '</div>');
}

// ─── DATOS GENERALES ────────────────────────────────────────────────
$usuario_id = intval($_POST['usuario_id'] ?? 0);
$area       = vacio($_POST['area']   ?? '');
$puesto     = vacio($_POST['puesto'] ?? '');
$fecha      = $_POST['fecha'] ?? date('Y-m-d');

// ─── DATOS EQUIPOS ──────────────────────────────────────────────────
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

// ─── BUSCAR USUARIO ─────────────────────────────────────────────────
$stmtUsr = $conexion->prepare("SELECT nombre_completo FROM usuarios WHERE id = ?");
$stmtUsr->bind_param("i", $usuario_id);
$stmtUsr->execute();
$usuarioRow = $stmtUsr->get_result()->fetch_assoc();
$stmtUsr->close();
$nombre_usuario = $usuarioRow['nombre_completo'] ?? 'Empleado';

// ─── FUNCIÓN: TABLA DE IMÁGENES ─────────────────────────────────────
function generarTablaImagenes($imagenes) {
    if (empty($imagenes)) return 'N/A';
    $html = '<table style="border:none;width:100%;text-align:center;"><tr>';
    foreach ($imagenes as $img) {
        $rutaAbs = BASE_PATH . $img;
        if (file_exists($rutaAbs)) {
            $html .= '<td style="border:none;"><img src="' . $rutaAbs . '" width="140" height="100" style="border:1px solid #000;margin:5px;"></td>';
        }
    }
    $html .= '</tr></table>';
    return $html;
}

// ─── GUARDAR EVIDENCIAS ─────────────────────────────────────────────
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
        $timestamp = time();
        foreach ($_FILES[$tipo]['tmp_name'] as $i => $tmpName) {
            $nombre      = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES[$tipo]['name'][$i]));
            $nombreFinal = $timestamp . '_' . $i . '_' . $nombre;
            $rutaAbs     = UPLOAD_EVIDENCIAS . $nombreFinal;
            $rutaRel     = 'uploads/evidencias/' . $nombreFinal;
            if (move_uploaded_file($tmpName, $rutaAbs)) {
                $lista[] = $rutaRel;
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

// ─── FILAS ADICIONALES PARA EL PDF ──────────────────────────────────
// El texto visible del artículo puede ser "Tipo — Marca Modelo" (del datalist)
// o texto libre. Limpiamos el "— Marca Modelo" para el PDF mostrando solo el tipo.
$articulos_adicionales_filas = '';
foreach ($adic_articulos as $i => $art) {
    $art = trim($art);
    if ($art === '') continue;

    // Si viene del datalist, el value tiene formato "Articulo — Marca Modelo"
    // Extraemos solo la parte antes del " — " para el PDF
    $artNombre = strpos($art, ' — ') !== false
        ? trim(explode(' — ', $art)[0])
        : $art;

    $cant  = htmlspecialchars($adic_cantidades[$i] ?? '1');
    $artH  = htmlspecialchars($artNombre);
    $serie = htmlspecialchars($adic_series[$i]  ?? 'N/A');
    $obs   = htmlspecialchars($adic_obs_arr[$i] ?? '');

    $articulos_adicionales_filas .= "
    <tr>
      <td style='text-align:center;'>$cant</td>
      <td>$artH</td>
      <td>$serie</td>
      <td>$obs</td>
    </tr>";
}
if (empty($articulos_adicionales_filas)) {
    $articulos_adicionales_filas = "<tr><td colspan='4' style='text-align:center;'>N/A</td></tr>";
}

// ─── PROCESAR ARTÍCULOS DEL INVENTARIO (PC, Monitor, Cel, Tel) ──────
$articulosHTML   = '';
$articulosInsert = [];

foreach ($articulos_validos as $art_id) {
    $art_id  = intval($art_id);
    $estDisp = ART_DISPONIBLE;

    $stmtChk = $conexion->prepare("SELECT * FROM articulo WHERE id = ? AND estatus = ?");
    $stmtChk->bind_param("ii", $art_id, $estDisp);
    $stmtChk->execute();
    $result  = $stmtChk->get_result();
    $stmtChk->close();

    if ($result->num_rows === 0) continue;

    $artRow            = $result->fetch_assoc();
    $articulosInsert[] = $art_id;

    $articulosHTML .= "
    <tr>
        <td>" . htmlspecialchars($artRow['articulo'])     . "</td>
        <td>" . htmlspecialchars($artRow['marca'])        . "</td>
        <td>" . htmlspecialchars($artRow['modelo'])       . "</td>
        <td>" . htmlspecialchars($artRow['numero_serie']) . "</td>
        <td>" . htmlspecialchars($artRow['categoria'])    . "</td>
    </tr>";
}

if (empty($articulosInsert)) {
    $articulosHTML = "<tr><td colspan='5' style='text-align:center;'>N/A</td></tr>";
}

// ─── CARGAR PLANTILLA ───────────────────────────────────────────────
$templatePath = __DIR__ . '/templates/Responsiva_SWL.html';
if (!file_exists($templatePath)) {
    ob_end_clean();
    die("Error: No se encontró la plantilla en <code>$templatePath</code>");
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

// ─── GENERAR PDF ────────────────────────────────────────────────────
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$nombreArchivo = 'responsiva_' . preg_replace('/\s+/', '_', $nombre_usuario) . '_' . date('Ymd_His') . '.pdf';
$rutaAbsoluta  = UPLOAD_RESPONSIVAS . $nombreArchivo;
$rutaRelativa  = 'uploads/responsivas/' . $nombreArchivo;

$pdf->Output($rutaAbsoluta, 'F');

// ─── GUARDAR EN BASE DE DATOS ────────────────────────────────────────
$evidenciaJson = json_encode($evidencias_guardadas);

// ── A. Artículos del inventario principal → tabla asignaciones ──────
foreach ($articulosInsert as $aid) {
    $stmtIns = $conexion->prepare(
        "INSERT INTO asignaciones
             (usuario_id, articulo_id, area, puesto, fecha, evidencia, pdf, estatus)
         VALUES (?, ?, ?, ?, ?, ?, ?, 1)"
    );
    if (!$stmtIns) {
        ob_end_clean();
        die('Error prepare asignaciones: ' . htmlspecialchars($conexion->error));
    }
    $stmtIns->bind_param('iisssss',
        $usuario_id, $aid, $area, $puesto, $fecha, $evidenciaJson, $rutaRelativa
    );
    if (!$stmtIns->execute()) {
        ob_end_clean();
        die('Error execute asignaciones: ' . htmlspecialchars($stmtIns->error));
    }
    $stmtIns->close();

    // Marcar artículo como asignado
    $stmtEst = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ?");
    $est      = ART_ASIGNADO;
    $stmtEst->bind_param('ii', $est, $aid);
    $stmtEst->execute();
    $stmtEst->close();
}

// ── B. Artículos adicionales → tabla asignaciones_adicionales ───────
if ($tieneAdicionales) {
    $stmtAdic = $conexion->prepare(
        "INSERT INTO asignaciones_adicionales
             (pdf_lote, usuario_id, cantidad, articulo, numero_serie, observacion)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$stmtAdic) {
        ob_end_clean();
        die('Error prepare adicionales: ' . htmlspecialchars($conexion->error));
    }

    // IDs de artículos del inventario usados en adicionales (para marcar como asignados)
    $idsAdicInventario = [];

    foreach ($adic_articulos as $i => $art) {
        $art = trim($art);
        if ($art === '') continue;

        // Nombre limpio para guardar en BD (sin "— Marca Modelo")
        $artNombre = strpos($art, ' — ') !== false
            ? trim(explode(' — ', $art)[0])
            : $art;

        $cant  = max(1, intval($adic_cantidades[$i] ?? 1));
        $serie = trim($adic_series[$i]  ?? '') ?: 'N/A';
        $obs   = trim($adic_obs_arr[$i] ?? '');

        $stmtAdic->bind_param('siisss',
            $rutaRelativa, $usuario_id, $cant, $artNombre, $serie, $obs
        );
        if (!$stmtAdic->execute()) {
            error_log('crear_pdf adicionales execute: ' . $stmtAdic->error);
        }

        // Registrar ID de inventario si viene del catálogo
        $adid = intval($adic_articulo_ids[$i] ?? 0);
        if ($adid > 0) {
            $idsAdicInventario[] = $adid;
        }
    }
    $stmtAdic->close();

    // Marcar artículos adicionales del inventario como asignados
    foreach ($idsAdicInventario as $adid) {
        $stmtEst2 = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ? AND estatus = ?");
        $estAsig  = ART_ASIGNADO;   // 1
        $estDisp  = ART_DISPONIBLE; // 0
        $stmtEst2->bind_param('iii', $estAsig, $adid, $estDisp);
        $stmtEst2->execute();
        $stmtEst2->close();
    }
}

ob_end_clean();
header('Location: ' . BASE_URL . $rutaRelativa);
exit;