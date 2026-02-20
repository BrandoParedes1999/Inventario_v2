<?php
include 'conexion.php';
$sql = "SELECT * FROM articulo where estatus ='0'";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='border-t text-gray-700 text-center'>";
        echo "<td>{$row['articulo']}</td>";
        echo "<td>{$row['marca']}</td>";
        echo "<td>{$row['modelo']}</td>";
        echo "<td>{$row['numero_serie']}</td>";
        echo "<td>{$row['categoria']}</td>";

$factura = $row['factura'] ?? '';
$existe_factura = !empty($factura) && file_exists($factura);

echo "<td>
  <div style='display: flex; align-items: center; gap: 20px;'>
    <div style='display: flex; align-items: center; gap: 10px;'>";

if ($existe_factura) {
  // Enlace al PDF con ícono original
  echo "<a class='icofont-ui-file icofont-1x text-secondary' href='{$factura}' target='_blank' style='text-decoration: none;'> PDF</a>";
} else {
  // Botón para mostrar modal con mensaje de que no hay PDF
  echo "<a class='icofont-ui-file icofont-1x text-muted' href='#' data-bs-toggle='modal' data-bs-target='#modalNoPDF{$row['id']}' style='text-decoration: none;'> PDF</a>";
}

echo "</div></div></td>";

echo "<td>{$row['fecha_adquisicion']}</td>";
echo "<td>
<div style='display: flex; align-items: center; gap: 20px;'>
    <div style='display: flex; align-items: center; gap: 10px;'>
        <a class='icofont-ui-file icofont-1x text-dark' href='#' data-bs-toggle='modal' data-bs-target='#qrModal{$row['id']}'> QR</a>                
    </div>
    <div style='display: flex; align-items: center; gap: 10px;'>
        <a class='icofont-ui-image icofont-1x text-primary' href='#' data-bs-toggle='modal' data-bs-target='#imagenModal{$row['id']}' style='text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;'></a>
    </div>";

if ($_SESSION['rol'] == 'Administrador') { // Solo si es admin muestra editar y eliminar
    echo "
    <div style='display: flex; align-items: center; gap: 10px;'>
        <a class='icofont-ui-edit icofont-1x text-info' href='#' data-bs-toggle='modal' data-bs-target='#editModal{$row['id']}'></a>
    </div>
    <div style='display: flex; align-items: center; gap: 10px;'>
        <a class='icofont-ui-delete icofont-1x text-danger' href='#' data-bs-toggle='modal' data-bs-target='#deleteModal{$row['id']}'></a>
    </div>";
}

echo "</div>
</td>";

        
        // Modal para mostrar imagen
        echo "
        <div class='modal fade' id='imagenModal{$row['id']}' tabindex='-1' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-body text-center'>
                <img src='{$row['imagen']}' class='img-fluid rounded' style='max-height: 500px;'>
              </div>
            </div>
          </div>
        </div>";
        
         // Modal de edición
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
              <label>Artículo</label>
              <input class='form-control' name='articulo' value='{$row['articulo']}' required>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Marca</label>
              <input class='form-control' name='marca' value='{$row['marca']}' required>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Modelo</label>
              <input class='form-control' name='modelo' value='{$row['modelo']}' required>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Número de Serie</label>
              <input class='form-control' name='numero_serie' value='{$row['numero_serie']}' required>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Categoría</label>
              <input class='form-control' name='categoria' value='{$row['categoria']}' required>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Factura (PDF)</label>
              <input type='file' name='factura' accept='application/pdf' class='form-control'>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Fecha de Adquisición</label>
              <input type='date' class='form-control' name='fecha_adquisicion' value='{$row['fecha_adquisicion']}'>
            </div>
            <div class='col-sm-6 mb-2'>
              <label>Imagen</label>
              <input type='file' class='form-control' name='imagen' accept='image/png, image/jpeg'>
              <small class='text-muted'>Dejar vacío para mantener la imagen actual.</small>";
              
              if (!empty($row['imagen'])) {
                echo "
                <div class='mt-2'>
                  <strong>Imagen actual:</strong><br>
                  <img src='{$row['imagen']}' alt='Imagen actual' class='img-fluid rounded' style='max-height: 150px; border: 1px solid #ddd;'>
                </div>";
              }
              
             echo "
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
        
        // Modal de eliminación
echo "
<div class='modal fade' id='deleteModal{$row['id']}' tabindex='-1' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered'>
    <div class='modal-content'>
      <form action='eliminar-articulo.php' method='POST'>
        <div class='modal-header'>
          <h5 class='modal-title'>¿Eliminar artículo?</h5>
          <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
        </div>
        <div class='modal-body'>
          ¿Está seguro de que desea eliminar <strong>{$row['articulo']}</strong>?
          <input type='hidden' name='id' value='{$row['id']}'>
          
          <div class='mt-3'>
            <label for='motivo_baja_{$row['id']}' class='form-label'>Motivo de baja:</label>
            <textarea name='motivo_baja' id='motivo_baja_{$row['id']}' class='form-control' rows='3' required placeholder='Escriba el motivo por el cual se elimina el artículo'></textarea>
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

// Modal si no hay PDF disponible
echo "
<div class='modal fade' id='modalNoPDF{$row['id']}' tabindex='-1' aria-labelledby='modalNoPDFLabel{$row['id']}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered'>
    <div class='modal-content'>
      <div class='modal-header bg-warning text-dark'>
        <h5 class='modal-title' id='modalNoPDFLabel{$row['id']}'>PDF no disponible</h5>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
      </div>
      <div class='modal-body'>
        No hay ningún archivo PDF o factura para el artículo <strong>{$row['articulo']}</strong>.
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
      </div>
    </div>
  </div>
</div>";

       // Modal para mostrar QR
      echo "
      <div class='modal fade' id='qrModal{$row['id']}' tabindex='-1' aria-hidden='true'>
        <div class='modal-dialog modal-lg modal-dialog-centered'>
        <div class='modal-content'>
         <div class='modal-header'>
        <h5 class='modal-title'>Código QR</h5>
        </div>
      <div class='modal-body text-center'>
        <img id='qr{$row['id']}' src='qr.php?id={$row['id']}' class='img-fluid rounded' style='max-height: 500px;'>
        <br><br>
        <button onclick='imprimirQR(\"qr{$row['id']}\")' type='button' class='btn btn-success'>Imprimir</button>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-danger' data-bs-dismiss='modal'>Cerrar</button>
      </div>
    </div>
  </div>
</div>";

    }
}
?>

<script>
function imprimirQR(id) {
  const img = document.getElementById(id);
  if (!img) {
    alert("No se encontró la imagen del QR.");
    return;
  }

  const qrSrc = img.src;
  const ventana = window.open('', '_blank');

  ventana.document.write(`
    <html>
    <head>
      <title>Imprimir QR</title>
      <style>
        body { text-align: center; margin-top: 50px; }
        img { max-width: 90%; height: auto; }
      </style>
    </head>
    <body onload="window.print()">
      <img src="${qrSrc}" alt="Código QR">
    </body>
    </html>
  `);
  ventana.document.close();
}
</script>


