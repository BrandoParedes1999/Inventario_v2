<?php
// view-table.php — $conexion disponible desde dashboard.php

$stmtDisp = $conexion->prepare("SELECT * FROM articulo WHERE estatus = 0 ORDER BY created_at DESC");
$stmtDisp->execute();
$result = $stmtDisp->get_result();
$stmtDisp->close();

$isAdmin = ($_SESSION['rol'] ?? '') === 'Administrador';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='text-center'>";
        echo "<td>" . htmlspecialchars($row['nombre'])            . "</td>";
        echo "<td>" . htmlspecialchars($row['marca'] ?? '')       . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo'] ?? '')      . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['categoria'] ?? '')   . "</td>";

        // ── Factura ──────────────────────────────────────────────
        $factura = $row['factura'] ?? '';
        $exFact  = !empty($factura) && file_exists(__DIR__ . '/' . $factura);
        echo "<td><div style='display:flex;align-items:center;gap:10px;justify-content:center;'>";
        if ($exFact) {
            echo "<a href='" . htmlspecialchars($factura) . "' target='_blank'
                     class='text-secondary' style='text-decoration:none;'>
                     <i class='fas fa-file-pdf'></i> PDF</a>";
        } else {
            echo "<a href='#' data-bs-toggle='modal'
                     data-bs-target='#modalNoPDF{$row['id']}'
                     class='text-muted' style='text-decoration:none;'>
                     <i class='fas fa-file-pdf'></i> PDF</a>";
        }
        echo "</div></td>";

        echo "<td>" . htmlspecialchars($row['fecha_adquisicion'] ?? '') . "</td>";

        // ── Acciones ─────────────────────────────────────────────
        echo "<td><div style='display:flex;align-items:center;gap:12px;justify-content:center;'>";
        echo "<a href='#' data-bs-toggle='modal' data-bs-target='#qrModal{$row['id']}'
                 class='text-dark' title='Ver QR'>
                 <i class='fas fa-qrcode'></i></a>";
        echo "<a href='#' data-bs-toggle='modal' data-bs-target='#imagenModal{$row['id']}'
                 class='text-primary' title='Ver imagen'>
                 <i class='fas fa-image'></i></a>";
        if ($isAdmin) {
            echo "<a href='#' data-bs-toggle='modal' data-bs-target='#editModal{$row['id']}'
                     class='text-info' title='Editar'>
                     <i class='fas fa-edit'></i></a>";
            echo "<a href='#' data-bs-toggle='modal' data-bs-target='#deleteModal{$row['id']}'
                     class='text-danger' title='Dar de baja'>
                     <i class='fas fa-trash-alt'></i></a>";
        }
        echo "</div></td></tr>";

        // ── Modal imagen ──────────────────────────────────────────
        $imgSrc = !empty($row['imagen']) ? htmlspecialchars($row['imagen']) : '';
        echo "
        <div class='modal fade' id='imagenModal{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-body text-center'>
                " . ($imgSrc ? "<img src='$imgSrc' class='img-fluid rounded' style='max-height:500px;'>" : "<p class='text-muted'>Sin imagen</p>") . "
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
              </div>
            </div>
          </div>
        </div>";

        // ── Modal editar ──────────────────────────────────────────
        if ($isAdmin) {
            $imgActual = !empty($row['imagen'])
                ? "<div class='mt-2'><strong>Imagen actual:</strong><br>
                   <img src='" . htmlspecialchars($row['imagen']) . "'
                        class='img-fluid rounded' style='max-height:150px;border:1px solid #ddd;'></div>"
                : '';

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
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <div class='row'>
                        <div class='col-sm-6 mb-2'>
                          <label>Nombre / Tipo</label>
                          <input list='tipo_articulo_edit' class='form-control' name='nombre'
                                 value='" . htmlspecialchars($row['nombre']) . "' required>
                          <datalist id='tipo_articulo_edit'>
                            <option value='Laptop'><option value='Computadora'>
                            <option value='Mouse'><option value='Teclado'>
                            <option value='Monitor'><option value='Impresora'>
                            <option value='Multifuncional'><option value='Escáner'>
                            <option value='Celular'><option value='Teléfono fijo'>
                            <option value='Tablet'><option value='Switch'>
                            <option value='Router'><option value='Access Point'>
                            <option value='Cable HDMI'><option value='Cable USB'>
                            <option value='Hub USB'><option value='Regulador'>
                            <option value='UPS'><option value='Diadema/Headset'>
                            <option value='Webcam'><option value='Bocinas'>
                          </datalist>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Marca</label>
                          <input class='form-control' name='marca'
                                 value='" . htmlspecialchars($row['marca'] ?? '') . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Modelo</label>
                          <input class='form-control' name='modelo'
                                 value='" . htmlspecialchars($row['modelo'] ?? '') . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>N° Serie</label>
                          <input class='form-control' name='numero_serie'
                                 value='" . htmlspecialchars($row['numero_serie'] ?? '') . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>N° Inventario</label>
                          <input class='form-control' name='numero_inventario'
                                 value='" . htmlspecialchars($row['numero_inventario'] ?? '') . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Categoría</label>
                          <input list='categoria_edit' class='form-control' name='categoria'
                                 value='" . htmlspecialchars($row['categoria'] ?? '') . "'>
                          <datalist id='categoria_edit'>
                            <option value='Cómputo'><option value='Periféricos'>
                            <option value='Impresión'><option value='Telefonía'>
                            <option value='Redes'><option value='Accesorios'>
                            <option value='Mobiliario TI'>
                          </datalist>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Fecha de Adquisición</label>
                          <input type='date' class='form-control' name='fecha_adquisicion'
                                 value='" . htmlspecialchars($row['fecha_adquisicion'] ?? '') . "'>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Factura (PDF)</label>
                          <input type='file' name='factura' accept='application/pdf' class='form-control'>
                        </div>
                        <div class='col-sm-12 mb-2'>
                          <label>Descripción / Especificaciones</label>
                          <textarea class='form-control' name='descripcion' rows='2'>" . htmlspecialchars($row['descripcion'] ?? '') . "</textarea>
                        </div>
                        <div class='col-sm-6 mb-2'>
                          <label>Imagen</label>
                          <input type='file' class='form-control' name='imagen' accept='image/png,image/jpeg'>
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

            // ── Modal dar de baja ─────────────────────────────────
            echo "
            <div class='modal fade' id='deleteModal{$row['id']}' tabindex='-1' aria-hidden='true'>
              <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                  <form action='eliminar-articulo.php' method='POST'>
                    <div class='modal-header bg-danger text-white'>
                      <h5 class='modal-title'>Dar de Baja Artículo</h5>
                      <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                      <p>¿Dar de baja <strong>" . htmlspecialchars($row['nombre']) . "</strong>?</p>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <div class='mb-3'>
                        <label class='form-label'>Motivo</label>
                        <select name='motivo' class='form-control' required>
                          <option value=''>— Selecciona —</option>
                          <option value='Rotura'>Rotura</option>
                          <option value='Pérdida'>Pérdida</option>
                          <option value='Obsolescencia'>Obsolescencia</option>
                          <option value='Robo'>Robo</option>
                          <option value='Transferencia'>Transferencia</option>
                          <option value='Otro'>Otro</option>
                        </select>
                      </div>
                      <div class='mb-3'>
                        <label class='form-label'>Descripción adicional</label>
                        <textarea name='descripcion' class='form-control' rows='3'
                                  placeholder='Detalla el motivo...'></textarea>
                      </div>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                      <button type='submit' class='btn btn-danger'>Dar de Baja</button>
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
                No hay factura PDF para <strong>" . htmlspecialchars($row['nombre']) . "</strong>.
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
                <h5 class='modal-title'>Código QR — " . htmlspecialchars($row['nombre']) . "</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body text-center'>
                <img id='qr{$row['id']}' src='qr.php?id={$row['id']}'
                     class='img-fluid rounded' style='max-height:400px;'>
                <br><br>
                <button onclick='imprimirQR(\"qr{$row['id']}\")' type='button'
                        class='btn btn-success'>
                  <i class='fas fa-print'></i> Imprimir
                </button>
              </div>
              <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
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
    if (!img) return;
    const w = window.open('', '_blank');
    w.document.write(`<html><head><title>QR</title>
    <style>body{text-align:center;margin-top:50px;}img{max-width:90%;}</style>
    </head><body onload="window.print()"><img src="${img.src}"></body></html>`);
    w.document.close();
}
</script>