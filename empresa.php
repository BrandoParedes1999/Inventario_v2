<?php
include 'conexion.php';
include 'sesion.php';
include 'Validacion.php';

$sql = "SELECT * FROM empresa ORDER BY id ASC";
$result = $conexion->query($sql);

$empresas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $empresas[] = $row;
    }
} else {
    die("Error en la consulta: " . $conexion->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Empresa</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700" rel="stylesheet" />
    <link href="icofont/icofont.min.css" rel="stylesheet" />
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />

    <style>
        .btn-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .logo-img {
            height: 50px;
            object-fit: contain;
        }

        .logo-preview {
            max-height: 80px;
            margin-top: 10px;
            object-fit: contain;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <?php include 'nav-left.php'; ?>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php include 'nav-user.php'; ?>

                <!-- Page Content -->
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
                                            <!-- <th>Logo</th> -->
                                            <th>Nombre de Empresa</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php
                                        foreach ($empresas as $index => $empresa) {
                                            echo '<tr>';
                                            echo '<td>' . ($index + 1) . '</td>';
                                            // echo '<td><img src="' . htmlspecialchars($empresa['logo']) . '" class="logo-img" alt="Logo Empresa"></td>';
                                            echo '<td>' . htmlspecialchars($empresa['nombre']) . '</td>';
                                            echo '<td>
                                                <button class="btn btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#modalEditar' . $empresa['id'] . '" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                &nbsp;
                                                <button class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#modalEliminar' . $empresa['id'] . '" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($empresas as $empresa) : ?>
                        <!-- Modal Editar Empresa -->
                        <div class="modal fade" id="modalEditar<?= $empresa['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $empresa['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <form class="modal-content" method="POST" action="editar_empresa.php" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditarLabel<?= $empresa['id'] ?>">
                                            <i class="fas fa-building me-2"></i>Editar Empresa
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                                        <div class="mb-3">
                                            <label for="nombre_empresa_<?= $empresa['id'] ?>" class="form-label">Nombre de la empresa</label>
                                            <input type="text" class="form-control" id="nombre_empresa_<?= $empresa['id'] ?>" name="nombre_empresa" value="<?= htmlspecialchars($empresa['nombre']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <!-- Logo input commented out -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar Empresa -->
                        <div class="modal fade" id="modalEliminar<?= $empresa['id'] ?>" tabindex="-1" aria-labelledby="modalEliminarLabel<?= $empresa['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <form class="modal-content" method="POST" action="eliminar_empresa.php">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Eliminar Empresa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Deseas eliminar la empresa <strong><?= htmlspecialchars($empresa['nombre']) ?></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="empresa_id" value="<?= $empresa['id'] ?>">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Modal Agregar Empresa -->
                    <div class="modal fade" id="modalAgregarEmpresa" tabindex="-1" aria-labelledby="modalAgregarEmpresaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form class="modal-content" method="POST" action="agregar_empresa.php" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalAgregarEmpresaLabel">
                                        <i class="fas fa-building me-2"></i>Agregar Nueva Empresa
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nombre_empresa" class="form-label">Nombre de la empresa</label>
                                        <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" required />
                                    </div>
                                    <!-- Logo input commented out -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Guardar Empresa</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div> <!-- End Container -->
            </div> <!-- End Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; CARDUMEN 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div> <!-- End Content Wrapper -->
    </div> <!-- End Page Wrapper -->

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        // Resetear formulario modal agregar empresa al cerrarlo
        document.addEventListener('DOMContentLoaded', function() {
            var modalAgregar = document.getElementById('modalAgregarEmpresa');
            modalAgregar.addEventListener('hidden.bs.modal', function() {
                var form = modalAgregar.querySelector('form');
                if (form) {
                    form.reset();
                }
            });
        });
    </script>
</body>

</html>
