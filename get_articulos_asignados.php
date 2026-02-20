<?php
include 'conexion.php';

$usuario_id = intval($_GET['usuario_id'] ?? 0);

if ($usuario_id <= 0) {
    echo "<p>Error: ID de usuario no válido.</p>";
    exit;
}

$sql = "
    SELECT 
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
    WHERE s.usuario_id = ? AND s.estatus = 1
    ORDER BY s.fecha DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped">';
    echo '<thead><tr>
            <th>Artículo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>No. Serie</th>
            <th>Categoría</th>
            <th>Fecha</th>
            <th>Responsiva</th>
          </tr></thead>';
    echo '<tbody>';

    while ($row = $result->fetch_assoc()) {
        $responsivaBtn = '';

        if (!empty($row['pdf']) && file_exists($row['pdf'])) {
            $pdfPath = htmlspecialchars($row['pdf']);
            $responsivaBtn = '<a href="' . $pdfPath . '" class="btn btn-sm btn-outline-primary" target="_blank">Ver Responsiva</a>';
        } else {
            $responsivaBtn = '<span class="text-muted">Sin responsiva</span>';
        }

        echo "<tr>
                <td>" . htmlspecialchars($row['articulo']) . "</td>
                <td>" . htmlspecialchars($row['marca']) . "</td>
                <td>" . htmlspecialchars($row['modelo']) . "</td>
                <td>" . htmlspecialchars($row['numero_serie']) . "</td>
                <td>" . htmlspecialchars($row['categoria']) . "</td>
                <td>" . htmlspecialchars($row['fecha']) . "</td>
                <td>$responsivaBtn</td>
              </tr>";
    }

    echo '</tbody></table>';
    echo '</div>';
} else {
    echo '<div class="alert alert-info">Este usuario no tiene artículos asignados actualmente.</div>';
}

$stmt->close();
$conexion->close();
?>
