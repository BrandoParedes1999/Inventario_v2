<?php
include 'sesion.php';
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Inventario</title>

  <!-- Bootstrap y fuentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
  <link href="icofont/icofont.css" rel="stylesheet">
  <link href="icofont/icofont.min.css" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
<script>
    // Detectar navegación por botón "atrás" y forzar recarga real
    if (performance.navigation.type === 2) {
        location.reload(true);
    }
    </script>

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
        <?php include 'nav-user.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Botón Modal -->
          <div class="mb-4">
            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
              Agregar Articulo
            </button>
            <?php endif; ?>

            <!-- Modal -->
            <div class="modal fade" id="myModal">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Articulo Nuevo</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <!-- Formulario -->
                  <div class="modal-body">
                    <form action="form-dat.php" method="POST" enctype="multipart/form-data">
                      <div class="form-group row">
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Tipo de Articulo</label>
                          <input list="tipo_articulo" name="articulo" class="form-control" placeholder="Seleccione o escriba el articulo">
                          <datalist id="tipo_articulo">
                            <option value="Celular">
                            <option value="Laptop">
                            <option value="Cable">
                            <option value="Computadora">
                            <option value="Monitor">
                            <option value="Tablet">
                            <option value="Electrónico">
                          </datalist>
                        </div>
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Marca</label>
                          <input list="marca" name="marca" class="form-control" placeholder="Seleccione o escriba la marca">
                          <datalist id="marca">
                            <option value="Lenovo">
                            <option value="HP">
                            <option value="Dell">
                            <option value="Apple">
                            <option value="Samsung">
                            <option value="Redmi">
                          </datalist>
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Modelo</label>
                          <input type="text" class="form-control" name="modelo" placeholder="Modelo">
                        </div>
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Numero de Serie</label>
                          <input type="text" class="form-control" name="serie" placeholder="Numero de Serie">
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Categoria</label>
                          <input list="categoria" name="categoria" class="form-control" placeholder="Seleccione o escriba la categoría">
                          <datalist id="categoria">
                            <option value="Electrónico">
                            <option value="Herramientas y equipos">
                            <option value="Accesorios">
                            <option value="Equipos de red">
                            <option value="Repuestos y piezas">
                          </datalist>
                        </div>
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Factura (PDF):</label>
                          <input type="file" name="factura" class="form-control" accept=".pdf" required>
                        </div>
                      </div>

                      <div class="form-group row">
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Fecha de Compra</label>
                          <input type="date" class="form-control" name="fecha_compra">
                        </div>
                        <div class="col-sm-6 mb-2">
                          <label class="form-label">Foto del Articulo</label>
                          <input type="file" name="imagen" class="form-control" accept="image/png, image/jpeg" required>
                        </div>
                      </div>

                      <button type="submit" class="btn btn-success">Agregar Articulo</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="card shadow mb-4">
            <style>
              .card-header { display: flex; align-items: center; }
              .card-header i { font-size: 24px; margin-right: 10px; color: blue; }
            </style>

            <div class="card-header py-3">
              <i class='fas fa-cubes'></i>
              <h6 class="m-0 font-weight-bold text-primary">Tabla de Articulos</h6>
            </div>

            <div class="card-body">
              <!-- Tabs -->
              <ul class="nav nav-tabs mb-4" id="tabArticulos" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="disponibles-tab" data-bs-toggle="tab" href="#disponibles" role="tab">Artículos Disponibles</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="asignados-tab" data-bs-toggle="tab" href="#asignados" role="tab">Artículos Asignados</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="deshabilitados-tab" data-bs-toggle="tab" href="#deshabilitados" role="tab">Artículos Deshabilitados</a>
                </li>
              </ul>

              <div class="tab-content" id="tabArticulosContent">
                <!-- Disponibles -->
                <div class="tab-pane fade show active" id="disponibles" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tablaDisponibles" width="100%" cellspacing="0">
                      <thead class="table-dark text-center">
                        <tr>
                          <th>Articulo</th>
                          <th>Marca</th>
                          <th>Modelo</th>
                          <th>No. Serie</th>
                          <th>Categoria</th>
                          <th>Factura</th>
                          <th>Fecha de Compra</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php include 'view-table.php'; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Asignados -->
                <div class="tab-pane fade" id="asignados" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tablaAsignados" width="100%" cellspacing="0">
                      <thead class="table-dark text-center">
                        <tr>
                          <th>Articulo</th>
                          <th>Marca</th>
                          <th>Modelo</th>
                          <th>No. Serie</th>
                          <th>Categoria</th>
                          <th>Usuario</th>
                          <th>Fecha Asignación</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php include 'view-asignados.php'; ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Deshabilitados -->
                <div class="tab-pane fade" id="deshabilitados" role="tabpanel">
                  <div class="table-responsive p-3">
                    <table class="table table-hover table-bordered" id="tablaDeshabilitados" width="100%" cellspacing="0">
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
                        <?php include 'view-deshabilitados.php'; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <!-- Fin Tabs -->
            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; CARDUMEN 2025</span>
          </div>
        </div>
      </footer>
    </div>
    <!-- End of Content Wrapper -->
  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- JS -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>

  <?php
  if (!empty($_SESSION['acceso_denegado'])):
    unset($_SESSION['acceso_denegado']); // Limpiar para que no se repita
  ?>
<!-- Modal Acceso Denegado -->
<div class="modal fade" id="modalAccesoDenegado" tabindex="-1" aria-labelledby="modalAccesoDenegadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title text-danger w-100" id="modalAccesoDenegadoLabel">Acceso Denegado</h5>
      </div>
      <div class="modal-body">
        <p>No tienes permisos para acceder a esta página.</p>
      </div>
      <div class="modal-footer">
        <a href="dashboard.php" class="btn btn-primary w-100">Regresar al Inventario</a>
      </div>
    </div>
  </div>
</div>

<!-- Mostrar automáticamente -->
<script>
  const modal = new bootstrap.Modal(document.getElementById('modalAccesoDenegado'));
  modal.show();
</script>
<?php endif; ?>

<script>
    window.onpageshow = function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            // Si viene del caché del navegador, recargar
            window.location.reload();
        }
    };
</script>


</body>

</html>
