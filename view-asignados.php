<?php 
include 'conexion.php';

$sql = "SELECT 
            a.id AS articulo_id,
            a.articulo,
            a.marca,
            a.modelo,
            a.numero_serie,
            a.categoria,
            u.id AS usuario_id,
            u.nombre_completo AS usuario,
            s.fecha,
            s.evidencia
        FROM asignaciones s
        INNER JOIN articulo a ON a.id = s.articulo_id
        INNER JOIN usuarios u ON u.id = s.usuario_id
        WHERE a.estatus = 1 AND s.fecha_devolucion IS NULL AND s.estatus = 1
        ORDER BY s.fecha DESC";

$resultado = $conexion->query($sql);

if (!$resultado) {
    die("Error en consulta: " . $conexion->error);
}

if ($resultado->num_rows === 0) {
    echo "<tr><td colspan='9' class='text-center'>No hay asignaciones registradas.</td></tr>";
} else {
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['articulo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['marca']) . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie']) . "</td>";
        echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";

        echo "<td class='text-center'>";
        if ($_SESSION['rol'] == 'Administrador') {
            echo "<form method='POST' action='restaurar-articulo.php' onsubmit='return confirm(\"¿Deseas restaurar este artículo?\");'>";
            echo "<input type='hidden' name='articulo_id' value='" . htmlspecialchars($row['articulo_id']) . "'>";
            echo "<input type='hidden' name='usuario_id' value='" . htmlspecialchars($row['usuario_id']) . "'>";
            echo "<button type='submit' class='btn btn-success btn-sm'>Restaurar</button>";
            echo "</form>";
        } else {
            echo "<span class='text-muted'>Sin permisos</span>";
        }
        echo "</td>";

        echo "</tr>";
    }
}
