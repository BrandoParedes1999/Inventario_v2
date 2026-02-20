<?php
require_once 'sesion.php';
include 'conexion.php';
include 'Validacion.php';
$por_pagina = 25; // Artículos por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $por_pagina;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

   <title>Historial de Artículos</title>


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
                    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-history"></i> Historial de Artículos</h1>

        <div class="card shadow mb-4">
            <!-- Barra de búsqueda con espaciado -->
             <div class="mb-4 mt-2 px-2">
              <label for="barraBusqueda" class="form-label fw-bold">Buscar en historial:</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="barraBusqueda" class="form-control" placeholder="Categoría, Usuario o N° Serie...">
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
                    <th>Categoria</th>
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
        INNER JOIN articulo a ON a.id = s.articulo_id
        INNER JOIN usuarios u ON u.id = s.usuario_id
        ORDER BY s.fecha DESC
        LIMIT $inicio, $por_pagina";

                $res = $conexion->query($sql);
                while ($row = $res->fetch_assoc()) {
                  $estado = ($row['estatus'] == 1) ? 'Asignado' : (($row['estatus'] == 2) ? 'Deshabilitado' : 'Finalizado');
                  $total_query = $conexion->query("SELECT COUNT(*) AS total FROM asignaciones");
                  $total_row = $total_query->fetch_assoc();
                  $total_articulos = $total_row['total'];
                  $total_paginas = ceil($total_articulos / $por_pagina);

                  echo "<tr>
                          <td>{$row['articulo']}</td>
                          <td>{$row['marca']}</td>
                          <td>{$row['modelo']}</td>
                          <td>{$row['numero_serie']}</td>
                          <td>{$row['categoria']}</td>
                          <td>{$row['usuario']}</td>
                          <td>{$row['area']}</td>
                          <td>{$row['puesto']}</td>
                          <td>{$row['fecha']}</td>
                          <td>{$estado}</td>
                          <td><a href='{$row['pdf']}' target='_blank'>Ver PDF</a></td>
                        </tr>";
                }
                ?>
                </tbody>
              </table>

              <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                  <?php if ($pagina > 1): ?>
                    <li class="page-item">
                      <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                      </a>
                    </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                      <li class="page-item <?php echo ($pagina == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                      <?php endfor; ?>
                      <?php if ($pagina < $total_paginas): ?>
                        <li class="page-item">
                          <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>" aria-label="Siguiente">
                            <span aria-hidden="true">&raquo;</span>
                          </a>
                        </li>
                        <?php endif; ?>
                      </ul>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
</div>
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const input = document.getElementById("barraBusqueda");

  input.addEventListener("input", function () {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll("table tbody tr").forEach(row => {
      const categoria = row.children[4]?.textContent.toLowerCase() || "";
      const usuario = row.children[5]?.textContent.toLowerCase() || "";
      const serie = row.children[3]?.textContent.toLowerCase() || "";

      const coincide = categoria.includes(filtro) || usuario.includes(filtro) || serie.includes(filtro);
      row.style.display = coincide ? "" : "none";
    });
  });
});
</script>
 