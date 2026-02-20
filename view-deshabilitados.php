<?php
include 'conexion.php';

$sql = "SELECT 
            a.id, a.articulo, a.marca, a.modelo, a.numero_serie, a.categoria,
            b.motivo_baja, b.fecha_baja
        FROM articulo a
        LEFT JOIN bajas_articulos b ON b.id = (
            SELECT id FROM bajas_articulos 
            WHERE articulo_id = a.id 
            ORDER BY fecha_baja DESC 
            LIMIT 1
        )
        WHERE a.estatus = 2
        ORDER BY b.fecha_baja DESC";

// Ejecutar la consulta
$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['articulo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie']) . "</td>";
        echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($row['motivo_baja'] ?? 'Sin motivo') . "</td>";
        echo "<td>" . htmlspecialchars($row['fecha_baja'] ?? 'Sin fecha') . "</td>";

        echo "<td class='text-center'>";
        if ($_SESSION['rol'] == 'Administrador') {
            echo "<button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#restaurarModal{$row['id']}'>Restaurar</button>";
        } else {
            echo "<span class='text-muted'>Sin permisos</span>";
        }
        echo "</td>";

        // Modal solo para admins
        if ($_SESSION['rol'] == 'Administrador') {
            echo "
            <div class='modal fade' id='restaurarModal{$row['id']}' tabindex='-1' aria-labelledby='restaurarModalLabel{$row['id']}' aria-hidden='true'>
              <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                  <form method='POST' action='restaurar-articulo.php'>
                    <div class='modal-header'>
                      <h5 class='modal-title'>¿Restaurar artículo?</h5>
                      <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                      ¿Estás seguro de que deseas restaurar <strong>{$row['articulo']}</strong>?
                      <input type='hidden' name='articulo_id' value='{$row['id']}'>
                      <div class='mt-3'>
                        <label for='motivo_restauracion_{$row['id']}'>Motivo de restauración:</label>
                        <textarea name='motivo_restauracion' id='motivo_restauracion_{$row['id']}' class='form-control' required placeholder='Describe por qué restauras este artículo'></textarea>
                      </div>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                      <button type='submit' class='btn btn-success'>Restaurar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>";
        }

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No hay artículos deshabilitados.</td></tr>";
}
