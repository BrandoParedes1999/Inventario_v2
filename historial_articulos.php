<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

// CORRECCIÓN: se reemplaza include 'sesion.php' + include 'Validacion.php' por el nuevo sistema
Session::check();

$pageTitle  = 'Historial de Asignaciones';
$por_pagina = 25;
$pagina     = max(1, intval($_GET['pagina'] ?? 1));
$inicio     = ($pagina - 1) * $por_pagina;

// CORRECCIÓN: calcular total FUERA del while para evitar query dentro del bucle
$total_query    = $conexion->query("SELECT COUNT(*) AS total FROM asignaciones");
$total_articulos = $total_query ? (int)$total_query->fetch_assoc()['total'] : 0;
$total_paginas  = max(1, ceil($total_articulos / $por_pagina));

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-history"></i> Historial de Artículos</h1>

    <div class="card shadow mb-4">

        <div class="mb-4 mt-2 px-3 pt-3">
            <label class="form-label fw-bold">Buscar en historial:</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="barraBusqueda" class="form-control"
                    placeholder="Categoría, Usuario o N° Serie...">
            </div>
        </div>

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Registro Histórico</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Artículo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>No. Serie</th>
                            <th>Categoría</th>
                            <th>Usuario</th>
                            <th>Área</th>
                            <th>Puesto</th>
                            <th>Fecha Asignación</th>
                            <th>Movimiento</th>
                            <th>PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT
                                    a.articulo, a.marca, a.modelo, a.numero_serie, a.categoria,
                                    u.nombre_completo AS usuario,
                                    s.area, s.puesto, s.fecha, s.estatus, s.pdf
                                FROM asignaciones s
                                INNER JOIN articulo  a ON a.id = s.articulo_id
                                INNER JOIN usuarios  u ON u.id = s.usuario_id
                                ORDER BY s.fecha DESC
                                LIMIT ?, ?";

                        $stmtH = $conexion->prepare($sql);
                        $stmtH->bind_param("ii", $inicio, $por_pagina);
                        $stmtH->execute();
                        $res = $stmtH->get_result();
                        $stmtH->close();

                        if ($res && $res->num_rows > 0):
                            while ($row = $res->fetch_assoc()):
                                $estado = match((int)$row['estatus']) {
                                    1 => 'Asignado',
                                    2 => 'Deshabilitado',
                                    default => 'Finalizado',
                                };
                                $pdfLink = (!empty($row['pdf']) && file_exists($row['pdf']))
                                    ? "<a href='" . htmlspecialchars($row['pdf']) . "' target='_blank' class='btn btn-sm btn-outline-primary'>Ver PDF</a>"
                                    : "<span class='text-muted'>Sin PDF</span>";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['articulo']) ?></td>
                            <td><?= htmlspecialchars($row['marca']) ?></td>
                            <td><?= htmlspecialchars($row['modelo']) ?></td>
                            <td><?= htmlspecialchars($row['numero_serie']) ?></td>
                            <td><?= htmlspecialchars($row['categoria']) ?></td>
                            <td><?= htmlspecialchars($row['usuario']) ?></td>
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td><?= htmlspecialchars($row['puesto']) ?></td>
                            <td><?= htmlspecialchars($row['fecha']) ?></td>
                            <td><?= $estado ?></td>
                            <td><?= $pdfLink ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No hay registros en el historial.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
            <nav aria-label="Paginación del historial">
                <ul class="pagination justify-content-center mt-3">
                    <?php if ($pagina > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">&laquo;</a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($pagina === $i) ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $total_paginas): ?>
                    <li class="page-item">
                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">&raquo;</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

        </div><!-- /.card-body -->
    </div><!-- /.card -->

</div><!-- /.container-fluid -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("barraBusqueda");
    input.addEventListener("input", function () {
        const filtro = this.value.toLowerCase();
        document.querySelectorAll("table tbody tr").forEach(row => {
            const categoria = row.children[4]?.textContent.toLowerCase() || "";
            const usuario   = row.children[5]?.textContent.toLowerCase() || "";
            const serie     = row.children[3]?.textContent.toLowerCase() || "";
            row.style.display = (categoria.includes(filtro) || usuario.includes(filtro) || serie.includes(filtro))
                ? "" : "none";
        });
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>