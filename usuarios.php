
<?php
require_once 'sesion.php';
include 'Validacion.php';
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title> Inventario </title>

    <!-- Custom fonts for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="icofont/icofont.css" rel= "stylesheet">
    <link href="icofont/icofont.min.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script>
    // Detectar navegación por botón "atrás" y forzar recarga real
    if (performance.navigation.type === 2) {
        location.reload(true);
    }
    </script>


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar --> 
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <?php
            include 'nav-left.php'; // Barra de navegacion lateral
            ?>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <!-- Topbar // Inicio barra de navegacion lateral-->
                <?php
                include 'nav-user.php'; // Barra de navegacion de usuario.
                ?>
                <!-- End of Topbar  Fin barra de navegacion lateral-->

                <!-- Begin Page Content // Inicio contenido de la pagina -->
                <div class="container-fluid">          
                    <!-- Inicio Modal / Boton -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                         Agregar Usuario
                        </button>
                    </div>
                        <!-- Modal Encabezado-->
                    <div class="modal fade" id="modalUsuario">
                    <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        
                     <!-- Modal Header -->
                    <div class="modal-header">
                    <h4 class="modal-title">Nuevo usuario</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                     <!-- Modal / Formulario de registro -->
                    <div class="modal-body">
                    <form action="guardar_usuario.php" method="POST">
                    <div class="row">
                    <div class="col-sm-6 mb-2">

              <label>Nombre completo</label>
              <input type="text" name="nombre_completo" class="form-control" required>
              </div>
            <div class="col-sm-6 mb-2">
              <label>Nombre de usuario</label>
              <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="col-sm-6 mb-2">
              <label>Contraseña</label>
              <input type="password" name="contrasena" class="form-control" required>
            </div>
            <div class="col-sm-6 mb-2">
              <label>Correo electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-sm-6 mb-2">
              <label>Rol</label>
              <select name="rol" class="form-control">
                <option value="Administrador">Administrador</option>
                <option value="Usuario">Usuario</option>
              </select>
            </div>
        <input type="hidden" name="estatus" value="0">

          </div>
          <button type="submit" class="btn btn-success mt-3">Agregar Usuario</button>
        </form>
      </div>
    </div>
  </div>
</div>

                    <!-- Fin de modal -->

                    <div class="card shadow mb-4">
                    
                    <style>
                   .card-header { display: flex; align-items: center; }
                   .card-header i { font-size: 24px; margin-right: 10px; color: blue; }
                    </style>

                    

                        <div class="card-header py-3">
                            <i class='icofont-ui-user'></i>
                            <h6 class="m-0 font-weight-bold text-primary">Tabla de Usuario</h6>

                        </div>
                        <div class="card-body">

                   <!-- Tabs de Activos/Inactivos -->
                <ul class="nav nav-tabs mb-3" id="usuarioTabs" role="tablist">
                  <li class="nav-item">
                  <a class="nav-link active" id="activos-tab" data-bs-toggle="tab" href="#activos" role="tab">Usuarios Activos</a>
                </li>
            <li class="nav-item">
            <a class="nav-link" id="inactivos-tab" data-bs-toggle="tab" href="#inactivos" role="tab">Usuarios Inactivos</a>
            </li>
            </ul>

            <div class="tab-content" id="usuarioTabsContent">
            <!-- Tabla Usuarios Activos -->
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
            <?php include 'cargar_usuarios.php'; // Muestra activos ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabla Usuarios Inactivos -->
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
            include 'conexion.php';
            $sql = "SELECT * FROM usuarios WHERE estatus = 1";
            $result = $conexion->query($sql);
            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['nombre_completo']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['correo']}</td>
        <td>{$row['rol']}</td>
        <td><button class='btn btn-outline-secondary btn-sm' disabled>Inactivo</button></td>

        <td>
          <form method='POST' action='restaurar_usuario.php' onsubmit='return confirm(\"¿Seguro que deseas restaurar este usuario?\");'>
            <input type='hidden' name='id' value='{$row['id']}'>
            <button type='submit' class='btn btn-success btn-sm'>Restaurar</button>
          </form>
        </td>
      </tr>";
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

                <!-- /.container-fluid // Fin Contenido de pagina-->

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
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
 
</body>

</html>
