<?php
// CORRECCIÓN: solo tenía include 'conexion.php' — cualquiera podía ver asignaciones
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check(); // Cualquier usuario autenticado puede consultar

$usuario_id = intval($_GET['usuario_id'] ?? 0);

if ($usuario_id <= 0) {
    echo "<p class='text-danger'>Error: ID de usuario no válido.</p>";
    exit;
}

$sql = "SELECT
            s.id AS id_asignacion,
            a.articulo,
            a.marca,
            a.modelo,
            a.numero_serie,
            a.categoria,
            s.fecha,
            s.estatus,
            s.pdf
        FROM asignaciones s
        INNER JOIN articulo a ON a.id = s.articulo_id
        WHERE s.usuario_id = ? AND s.estatus = ?
        ORDER BY s.fecha DESC";

$estActiva = ASIG_ACTIVA; // 1
$stmt      = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $estActiva);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped table-sm">';
    echo '<thead class="table-secondary"><tr>
            <th>Artículo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>No. Serie</th>
            <th>Categoría</th>
            <th>Fecha</th>
            <th>Responsiva</th>
          </tr></thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        $responsivaBtn = (!empty($row['pdf']) && file_exists($row['pdf']))
            ? '<a href="' . htmlspecialchars($row['pdf']) . '" class="btn btn-sm btn-outline-primary" target="_blank">Ver Responsiva</a>'
            : '<span class="text-muted">Sin responsiva</span>';

        echo "<tr>
                <td>" . htmlspecialchars($row['articulo'])      . "</td>
                <td>" . htmlspecialchars($row['marca'])          . "</td>
                <td>" . htmlspecialchars($row['modelo'])         . "</td>
                <td>" . htmlspecialchars($row['numero_serie'])   . "</td>
                <td>" . htmlspecialchars($row['categoria'])      . "</td>
                <td>" . htmlspecialchars($row['fecha'])          . "</td>
                <td>$responsivaBtn</td>
              </tr>";
    }

    echo '</tbody></table></div>';
} else {
    echo '<div class="alert alert-info mb-0">Este usuario no tiene artículos asignados actualmente.</div>';
}