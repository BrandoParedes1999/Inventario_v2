<?php
// view-asignados.php — $conexion y Session disponibles desde dashboard.php

$sql = "SELECT
            a.id AS articulo_id,
            a.articulo, a.marca, a.modelo, a.numero_serie, a.categoria,
            u.id AS usuario_id,
            u.nombre_completo AS usuario,
            s.fecha, s.evidencia
        FROM asignaciones s
        INNER JOIN articulo  a ON a.id = s.articulo_id
        INNER JOIN usuarios  u ON u.id = s.usuario_id
        WHERE a.estatus = ? AND s.fecha_devolucion IS NULL AND s.estatus = ?
        ORDER BY s.fecha DESC";

$estAsig = ART_ASIGNADO;   // 1
$asigAct = ASIG_ACTIVA;    // 1

$stmtA = $conexion->prepare($sql);
$stmtA->bind_param("ii", $estAsig, $asigAct);
$stmtA->execute();
$resultado = $stmtA->get_result();
$stmtA->close();

if (!$resultado) {
    echo "<tr><td colspan='8' class='text-center text-danger'>Error en la consulta.</td></tr>";
    return;
}

$isAdmin = Session::isAdmin();

if ($resultado->num_rows === 0) {
    echo "<tr><td colspan='8' class='text-center text-muted'>No hay asignaciones registradas.</td></tr>";
} else {
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['articulo'])      . "</td>";
        echo "<td>" . htmlspecialchars($row['marca'])         . "</td>";
        echo "<td>" . htmlspecialchars($row['modelo'])        . "</td>";
        echo "<td>" . htmlspecialchars($row['numero_serie'])  . "</td>";
        echo "<td>" . htmlspecialchars($row['categoria'])     . "</td>";
        echo "<td>" . htmlspecialchars($row['usuario'])       . "</td>";
        echo "<td>" . htmlspecialchars($row['fecha'])         . "</td>";
        echo "<td class='text-center'>";

        if ($isAdmin) {
            echo "
            <form method='POST' action='restaurar-articulo.php'
                  onsubmit='return confirm(\"¿Deseas restaurar este artículo?\");'>
              <input type='hidden' name='articulo_id' value='" . htmlspecialchars($row['articulo_id']) . "'>
              <input type='hidden' name='usuario_id'  value='" . htmlspecialchars($row['usuario_id'])  . "'>
              <button type='submit' class='btn btn-success btn-sm'>Restaurar</button>
            </form>";
        } else {
            echo "<span class='text-muted'>Sin permisos</span>";
        }

        echo "</td></tr>";
    }
}