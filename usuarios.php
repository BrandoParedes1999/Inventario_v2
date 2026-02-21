<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin(); // Solo administradores

$pageTitle = 'Usuarios';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid">

    <!-- Botón agregar usuario -->
    <div class="mb-4">
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="fas fa-user-plus me-1"></i> Agregar Usuario
        </button>
    </div>

    <!-- ── MODAL AGREGAR USUARIO ── -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Usuario</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="guardar_usuario.php" method="POST">
                        <input type="hidden" name="csrf_token"
                               value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" name="nombre_completo"
                                       class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Nombre de usuario</label>
                                <input type="text" name="usuario"
                                       class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="contrasena"
                                       class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="correo"
                                       class="form-control" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Rol</label>
                                <select name="rol" class="form-control">
                                    <option value="<?= ROL_ADMIN ?>">Administrador</option>
                                    <option value="<?= ROL_USUARIO ?>">Usuario</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="estatus" value="<?= USR_ACTIVO ?>">
                        <button type="submit" class="btn btn-success mt-2">
                            <i class="fas fa-save me-1"></i> Agregar Usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TABLA USUARIOS ── -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <i class="icofont-ui-user fa-lg text-primary me-2"></i>
            <h6 class="m-0 fw-bold text-primary">Tabla de Usuarios</h6>
        </div>
        <div class="card-body">

            <ul class="nav nav-tabs mb-3" id="usuarioTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="activos-tab"
                       data-bs-toggle="tab" href="#activos" role="tab">
                        Usuarios Activos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="inactivos-tab"
                       data-bs-toggle="tab" href="#inactivos" role="tab">
                        Usuarios Inactivos
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- Activos -->
                <div class="tab-pane fade show active" id="activos" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include __DIR__ . '/cargar_usuarios.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Inactivos -->
                <div class="tab-pane fade" id="inactivos" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nombre Completo</th>
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlInactivos = "SELECT * FROM usuarios WHERE estatus = ?";
                                $stmtInact    = $conexion->prepare($sqlInactivos);
                                $est          = USR_INACTIVO;
                                $stmtInact->bind_param("i", $est);
                                $stmtInact->execute();
                                $resInact = $stmtInact->get_result();
                                $stmtInact->close();

                                if ($resInact && $resInact->num_rows > 0):
                                    while ($row = $resInact->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                                    <td><?= htmlspecialchars($row['correo']) ?></td>
                                    <td><?= htmlspecialchars($row['rol']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    </td>
                                    <td>
                                        <form method="POST" action="restaurar_usuario.php">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="id"
                                                   value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('¿Restaurar este usuario?')">
                                                <i class="fas fa-undo me-1"></i> Restaurar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No hay usuarios inactivos.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<?php include __DIR__ . '/includes/footer.php'; ?>