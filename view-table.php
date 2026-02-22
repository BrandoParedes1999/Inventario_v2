<?php
// view-table.php — $conexion y $_SESSION ya disponibles desde config/session.php cargado en dashboard.php

$sqlDisp   = "SELECT * FROM articulo WHERE estatus = ?";
$stmtDisp  = $conexion->prepare($sqlDisp);
$estDisp   = ART_DISPONIBLE; // 0
$stmtDisp->bind_param("i", $estDisp);
$stmtDisp->execute();
$result = $stmtDisp->get_result();
$stmtDisp->close();

$isAdmin  = Session::isAdmin();
$csrfMeta = htmlspecialchars($_SESSION['csrf_token'] ?? '');

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='text-center'>";
        echo "<td>" . htmlspecialchars($row['articulo'])    . "</td>";
        echo "<td>" . htmlspecialchars($row['marca'])       . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo'])      . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie']). "</td>";
        echo "<td>" . htmlspecialchars($row['categoria'])   . "</td>";

        // ── Columna Factura ───────────────────────────────────────
        $factura = $row['factura'] ?? '';
        $exFact  = !empty($factura) && file_exists(BASE_PATH . $factura);

        echo "<td><div style='display:flex;align-items:center;gap:10px;justify-content:center;'>";
        if ($exFact) {
            echo "<a class='icofont-ui-file icofont-1x text-secondary'
                     href='" . htmlspecialchars(BASE_URL . $factura) . "'
                     target='_blank' style='text-decoration:none;'> PDF</a>";
        } else {
            echo "<a class='icofont-ui-file icofont-1x text-muted' href='#'
                     data-bs-toggle='modal'
                     data-bs-target='#modalNoPDF{$row['id']}'
                     style='text-decoration:none;'> PDF</a>";
        }
        echo "</div></td>";

        echo "<td>" . htmlspecialchars($row['fecha_adquisicion']) . "</td>";

        // ── Columna Acciones ──────────────────────────────────────
        echo "<td><div style='display:flex;align-items:center;gap:12px;justify-content:center;'>";

        echo "<a class='icofont-ui-file icofont-1x text-dark' href='#'
                 data-bs-toggle='modal'
                 data-bs-target='#qrModal{$row['id']}'> QR</a>";

        echo "<a href='#' data-bs-toggle='modal'
                 data-bs-target='#imagenModal{$row['id']}'
                 title='Ver imagen' style='text-decoration:none;'>
                 <i class='fas fa-image text-primary'></i>
               </a>";

        if ($isAdmin) {
            echo "<a href='#' data-bs-toggle='modal'
                     data-bs-target='#editModal{$row['id']}'
                     title='Editar' style='text-decoration:none;'>
                     <i class='fas fa-edit text-info'></i>
                   </a>";
            echo "<a href='#' data-bs-toggle='modal'
                     data-bs-target='#deleteModal{$row['id']}'
                     title='Eliminar' style='text-decoration:none;'>
                     <i class='fas fa-trash-alt text-danger'></i>
                   </a>";
        }

        echo "</div></td>";
        echo "</tr>";

        // ── Modal imagen ──────────────────────────────────────────
        $imgSrc = !empty($row['imagen']) ? htmlspecialchars(BASE_URL . $row['imagen']) : '';
        echo "
        <div class='modal fade' id='imagenModal{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-body text-center'>
                <img src='$imgSrc' class='img-fluid rounded' style='max-height:500px;'>
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
              </div>
            </div>
          </div>
        </div>";

        // ── Modal edición + eliminación (solo admin) ──────────────
        if ($isAdmin) {
            $imgActual = !empty($row['imagen'])
                ? "<div class='mt-2'><strong>Imagen actual:</strong><br>
                   <img src='" . htmlspecialchars(BASE_URL . $row['imagen']) . "'
                        class='img-fluid rounded' style='max-height:150px;border:1px solid #ddd;'></div>"
                : '';

            // CORRECCIÓN: ambos formularios carecían de csrf_token
            echo "
            <div class='modal fade' id='editModal{$row['id']}' tabindex='-1' aria-hidden='true'>
              <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                  <div class='modal-header'>
                    <h5 class='modal-title'>Editar Artículo</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                  </div>
                  <div class='modal-body'>
                    <form action='editar-articulo.php' method='POST' enctype='multipart/form-data'>
                      <input type='hidden' name='csrf_token' value='$csrfMeta'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <div class='row'>
                        <div class='col-sm-6 mb-2'>
                          <label>Artículo</label>
                          <input class='form-control' name='articulo'
                                 value='" . htmlspecialchars($row['articulo']) . "' required>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Marca</label>
                          <input class='form-control' name='marca'
                                 value='" . htmlspecialchars($row['marca']) . "' required>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Modelo</label>
                          <input class='form-control' name='modelo'
                                 value='" . htmlspecialchars($row['modelo']) . "' required>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Número de Serie</label>
                          <input class='form-control' name='numero_serie'
                                 value='" . htmlspecialchars($row['numero_serie']) . "' required>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Categoría</label>
                          <input class='form-control' name='categoria'
                                 value='" . htmlspecialchars($row['categoria']) . "' required>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Factura (PDF)</label>
                          <input type='file' name='factura' accept='application/pdf' class='form-control'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Fecha de Adquisición</label>
                          <input type='date' class='form-control' name='fecha_adquisicion'
                                 value='" . htmlspecialchars($row['fecha_adquisicion']) . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Imagen</label>
                          <input type='file' class='form-control' name='imagen'
                                 accept='image/png, image/jpeg'>
                          <small class='text-muted'>Dejar vacío para mantener la imagen actual.</small>
                          $imgActual
                        </div>
                      </div>
                      <div class='modal-footer'>
                        <button type='submit' class='btn btn-primary'>Guardar Cambios</button>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>";

            echo "
            <div class='modal fade' id='deleteModal{$row['id']}' tabindex='-1' aria-hidden='true'>
              <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                  <form action='eliminar-articulo.php' method='POST'>
                    <input type='hidden' name='csrf_token' value='$csrfMeta'>
                    <div class='modal-header'>
                      <h5 class='modal-title'>¿Eliminar artículo?</h5>
                      <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                      ¿Está seguro de eliminar <strong>" . htmlspecialchars($row['articulo']) . "</strong>?
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <div class='mt-3'>
                        <label class='form-label'>Motivo de baja:</label>
                        <textarea name='motivo_baja' class='form-control' rows='3' required
                                  placeholder='Escriba el motivo'></textarea>
                      </div>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                      <button type='submit' class='btn btn-danger'>Sí, eliminar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>";
        }

        // ── Modal sin PDF ─────────────────────────────────────────
        echo "
        <div class='modal fade' id='modalNoPDF{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-header bg-warning text-dark'>
                <h5 class='modal-title'>PDF no disponible</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body'>
                No hay ningún archivo PDF o factura para el artículo
                <strong>" . htmlspecialchars($row['articulo']) . "</strong>.
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
              </div>
            </div>
          </div>
        </div>";

        // ── Modal QR ──────────────────────────────────────────────
        echo "
        <div class='modal fade' id='qrModal{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-header'>
                <h5 class='modal-title'>Código QR</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body text-center'>
                <img id='qr{$row['id']}' src='qr.php?id={$row['id']}'
                     class='img-fluid rounded' style='max-height:500px;'>
                <br><br>
                <button onclick='imprimirQR(\"qr{$row['id']}\")' type='button'
                        class='btn btn-success'>Imprimir</button>
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>Cerrar</button>
              </div>
            </div>
          </div>
        </div>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-muted'>No hay artículos disponibles.</td></tr>";
}
?>

<script>
function imprimirQR(id) {
    const img = document.getElementById(id);
    if (!img) { alert("No se encontró la imagen del QR."); return; }
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <html><head><title>Imprimir QR</title>
        <style>body{text-align:center;margin-top:50px;}img{max-width:90%;height:auto;}</style>
        </head><body onload="window.print()">
        <img src="${img.src}" alt="Código QR"></body></html>`);
    ventana.document.close();
}
</script>