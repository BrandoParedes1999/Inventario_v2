<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::check(); // Verifica sesión válida — redirige si no hay

$pageTitle = 'Inventario';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<!-- ═══════════════════════════════════════════════
     CONTENIDO PRINCIPAL
═══════════════════════════════════════════════ -->
<div class="container-fluid">

    <!-- Botón agregar — solo admin -->
    <div class="mb-4">
        <?php if (Session::isAdmin()): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarArticulo">
            <i class="fas fa-plus me-1"></i> Agregar Artículo
        </button>
        <?php endif; ?>
    </div>

    <!-- ── MODAL AGREGAR ARTÍCULO ── -->
    <?php if (Session::isAdmin()): ?>
    <div class="modal fade" id="modalAgregarArticulo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Artículo Nuevo</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="form-dat.php" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Tipo de Artículo</label>
                                <input list="tipo_articulo" name="articulo" class="form-control"
                                       placeholder="Seleccione o escriba el artículo" required>
                                <datalist id="tipo_articulo">
                                    <option value="Celular">
                                    <option value="Laptop">
                                    <option value="Cable">
                                    <option value="Computadora">
                                    <option value="Monitor">
                                    <option value="Tablet">
                                    <option value="Teléfono Fijo">
                                </datalist>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Marca</label>
                                <input list="marca" name="marca" class="form-control"
                                       placeholder="Seleccione o escriba la marca" required>
                                <datalist id="marca">
                                    <option value="Lenovo">
                                    <option value="HP">
                                    <option value="Dell">
                                    <option value="Apple">
                                    <option value="Samsung">
                                    <option value="Redmi">
                                </datalist>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Modelo</label>
                                <input type="text" class="form-control" name="modelo"
                                       placeholder="Modelo" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" name="serie"
                                       placeholder="Número de Serie" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Categoría</label>
                                <input list="categoria" name="categoria" class="form-control"
                                       placeholder="Seleccione o escriba la categoría" required>
                                <datalist id="categoria">
                                    <option value="Electrónico">
                                    <option value="Herramientas y equipos">
                                    <option value="Accesorios">
                                    <option value="Equipos de red">
                                    <option value="Repuestos y piezas">
                                </datalist>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Factura (PDF)</label>
                                <input type="file" name="factura" class="form-control"
                                       accept=".pdf" required>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="date" class="form-control" name="fecha_compra">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label class="form-label">Foto del Artículo</label>
                                <input type="file" name="imagen" class="form-control"
                                       accept="image/png, image/jpeg" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Agregar Artículo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── TABLA CON TABS ── -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <i class="fas fa-cubes fa-lg text-primary me-2"></i>
            <h6 class="m-0 fw-bold text-primary">Tabla de Artículos</h6>
        </div>
        <div class="card-body">

            <ul class="nav nav-tabs mb-4" id="tabArticulos" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="disponibles-tab"
                       data-bs-toggle="tab" href="#disponibles" role="tab">
                        Disponibles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="asignados-tab"
                       data-bs-toggle="tab" href="#asignados" role="tab">
                        Asignados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="deshabilitados-tab"
                       data-bs-toggle="tab" href="#deshabilitados" role="tab">
                        Deshabilitados
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="tabArticulosContent">

                <!-- Disponibles -->
                <div class="tab-pane fade show active" id="disponibles" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>No. Serie</th>
                                    <th>Categoría</th>
                                    <th>Factura</th>
                                    <th>Fecha de Compra</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include __DIR__ . '/view-table.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Asignados -->
                <div class="tab-pane fade" id="asignados" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>No. Serie</th>
                                    <th>Categoría</th>
                                    <th>Usuario</th>
                                    <th>Fecha Asignación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include __DIR__ . '/view-asignados.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Deshabilitados -->
                <div class="tab-pane fade" id="deshabilitados" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>No. Serie</th>
                                    <th>Categoría</th>
                                    <th>Motivo Baja</th>
                                    <th>Fecha Baja</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include __DIR__ . '/view-deshabilitados.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->

<?php include __DIR__ . '/includes/footer.php'; ?>