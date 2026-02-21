<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

// CORRECCIÓN: se reemplaza include 'sesion.php' + include 'Validacion.php' por el nuevo sistema
Session::checkAdmin();

$sql    = "SELECT * FROM empresa ORDER BY id ASC";
$result = $conexion->query($sql);

$empresas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $empresas[] = $row;
    }
} else {
    die("Error en la consulta: " . htmlspecialchars($conexion->error));
}

$pageTitle = 'Empresa';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="fas fa-building me-2"></i>Listado de Empresas</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarEmpresa">
            <i class="fas fa-plus-circle me-1"></i> Agregar Empresa
        </button>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nombre de Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empresas as $index => $empresa): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($empresa['nombre']) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" style="border-radius:50%;width:38px;height:38px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar<?= $empresa['id'] ?>"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                &nbsp;
                                <button class="btn btn-danger btn-sm" style="border-radius:50%;width:38px;height:38px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEliminar<?= $empresa['id'] ?>"
                                    title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales editar / eliminar -->
    <?php foreach ($empresas as $empresa): ?>

        <!-- Modal Editar -->
        <div class="modal fade" id="modalEditar<?= $empresa['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="editar_empresa.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-building me-2"></i>Editar Empresa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la empresa</label>
                            <input type="text" class="form-control" name="nombre_empresa"
                                value="<?= htmlspecialchars($empresa['nombre']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Eliminar -->
        <div class="modal fade" id="modalEliminar<?= $empresa['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="eliminar_empresa.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar Empresa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ¿Deseas eliminar la empresa <strong><?= htmlspecialchars($empresa['nombre']) ?></strong>?
                        <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>

    <?php endforeach; ?>

    <!-- Modal Agregar Empresa -->
    <div class="modal fade" id="modalAgregarEmpresa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="agregar_empresa.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-building me-2"></i>Agregar Nueva Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la empresa</label>
                        <input type="text" class="form-control" name="nombre_empresa" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Empresa</button>
                </div>
            </form>
        </div>
    </div>

</div><!-- /.container-fluid -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalAgregar = document.getElementById('modalAgregarEmpresa');
        modalAgregar.addEventListener('hidden.bs.modal', function () {
            var form = modalAgregar.querySelector('form');
            if (form) form.reset();
        });
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>