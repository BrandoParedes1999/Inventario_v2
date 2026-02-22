<?php
// view-asignados.php — $conexion disponible desde dashboard.php

$sql = "SELECT
            a.id          AS articulo_id,
            a.nombre,
            a.marca,
            a.modelo,
            a.numero_serie,
            a.categoria,
            u.id          AS usuario_id,
            u.nombre_completo AS usuario,
            s.fecha_asignacion,
            s.pdf
        FROM asignaciones s
        INNER JOIN articulo  a ON a.id = s.articulo_id
        INNER JOIN usuarios  u ON u.id = s.usuario_id
        WHERE a.estatus = 1
          AND s.fecha_devolucion IS NULL
          AND s.estatus = 1
        ORDER BY s.fecha_asignacion DESC";

$resultado = $conexion->query($sql);

$isAdmin = ($_SESSION['rol'] ?? '') === 'Administrador';

if (!$resultado) {
    echo "<tr><td colspan='8' class='text-center text-danger'>Error en la consulta.</td></tr>";
    return;
}

if ($resultado->num_rows === 0) {
    echo "<tr><td colspan='8' class='text-center text-muted'>No hay asignaciones activas.</td></tr>";
} else {
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre'])          . "</td>";
        echo "<td>" . htmlspecialchars($row['marca'] ?? '')     . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo'] ?? '')    . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['categoria'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario'])         . "</td>";
        echo "<td>" . htmlspecialchars($row['fecha_asignacion']) . "</td>";
        echo "<td class='text-center'>";

        // Ver responsiva
        if (!empty($row['pdf'])) {
            echo "<a href='" . htmlspecialchars($row['pdf']) . "' target='_blank'
                     class='btn btn-sm btn-outline-secondary me-1'>
                     <i class='fas fa-file-pdf'></i></a>";
        }

        if ($isAdmin) {
            echo "
            <button class='btn btn-warning btn-sm'
                    data-bs-toggle='modal'
                    data-bs-target='#devolucionModal{$row['articulo_id']}'>
              <i class='fas fa-undo'></i> Devolver
            </button>";
        }

        echo "</td></tr>";

        // ── Modal devolución ──────────────────────────────────────
        if ($isAdmin) {
            echo "
            <div class='modal fade' id='devolucionModal{$row['articulo_id']}' tabindex='-1' aria-hidden='true'>
              <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content'>
                  <form method='POST' action='restaurar-articulo.php'
                        onsubmit='return confirm(\"¿Confirmar devolución?\");'>
                    <div class='modal-header bg-warning text-dark'>
                      <h5 class='modal-title'>Registrar Devolución</h5>
                      <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                    </div>
                    <div class='modal-body'>
                      <p>Artículo: <strong>" . htmlspecialchars($row['nombre']) . "</strong></p>
                      <p>Empleado: <strong>" . htmlspecialchars($row['usuario']) . "</strong></p>
                      <input type='hidden' name='articulo_id' value='{$row['articulo_id']}'>
                      <input type='hidden' name='usuario_id'  value='{$row['usuario_id']}'>
                      <div class='mb-3'>
                        <label>Observaciones de devolución</label>
                        <textarea name='observaciones' class='form-control' rows='2'
                                  placeholder='Estado del equipo, notas...'></textarea>
                      </div>
                    </div>
                    <div class='modal-footer'>
                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                      <button type='submit' class='btn btn-warning'>Confirmar Devolución</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>";
        }
    }
}