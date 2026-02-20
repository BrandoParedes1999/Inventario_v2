<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}

$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "inventario";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario para agregar datos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo_articulo = $_POST['tipo_articulo'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $no_serie = $_POST['no_serie'];
    $categoria = $_POST['categoria'];
    $factura = $_POST['factura'];
    $fecha_adquisicion = $_POST['fecha_adquisicion'];
    $cantidad = $_POST['cantidad'];

    // Manejo de archivos (imagen y QR)
    $imagen = $_FILES['imagen']['name'];
    $qr = $_FILES['qr']['name'];

    // Cargar la imagen
    $target_dir = "img/productos/";// Carpeta donde se guardará la imagen
    $imagen_path = $target_dir . basename($imagen);// Ruta completa del archivo


    $qr_path = $target_dir . basename($qr);
    
     move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_path);
     move_uploaded_file($_FILES['qr']['tmp_name'], $qr_path);

    $sql_insert = "INSERT INTO stock (tipo_articulo, modelo, marca, no_serie, categoria, imagen, factura, fecha_adquisicion, cantidad, qr)
               VALUES ('$tipo_articulo', '$modelo', '$marca', '$no_serie', '$categoria', '$imagen_path', '$factura', '$fecha_adquisicion', '$cantidad', '$qr_path')";


    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Artículo agregado exitosamente'); window.location.href='stock.php';</script>";
    } else {
        echo "Error al insertar los datos: " . $conn->error;
    }
}

// Eliminar registro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM stock WHERE id=$id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>alert('Artículo eliminado exitosamente'); window.location.href='stock.php';</script>";
    } else {
        echo "Error al eliminar los datos: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Stock - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar (sin cambios) -->
   <aside class="w-64 bg-blue-700 text-white min-h-screen flex flex-col">
    <div class="p-6 text-2xl font-bold border-b border-blue-600">
      📦 Inventario
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2">
      <div class="flex items-center mb-6 space-x-2">
        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
          👤
        </div>
        <div>
          <div class="font-semibold">Admin User</div>
          <div class="text-sm text-blue-200">Administrador</div>
        </div>
      </div>
      <a href="dashboard.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Perfil</a>
      <a href="productos.php" class="block px-4 py-2 hover:bg-blue-600">Productos</a>
      <a href="users.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Usuarios</a>
      <a href="stock.php" class="block px-4 py-2 rounded bg-blue-600 rounded">Stock</a>
      <a href="settings.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Settings</a>
      <a href="logout.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Cerrar Sesion</a>
    </nav>
  </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-auto">
        <h1 class="text-2xl font-bold mb-6">Agregar Nuevo Artículo</h1>

   <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-full">
  <div class="grid grid-cols-2 gap-4">

    <div>
      <label class="block text-sm font-medium text-gray-700">Tipo de Artículo</label>
      <input type="text" name="tipo_articulo" placeholder="Ej: Laptop, Proyector..." class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Modelo</label>
      <input type="text" name="modelo" placeholder="Ej: Inspiron 15" class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Marca</label>
      <input type="text" name="marca" placeholder="Ej: Dell, HP..." class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Número de Serie</label>
      <input type="text" name="no_serie" placeholder="Ej: SN-123456" class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Categoría</label>
      <input type="text" name="categoria" placeholder="Ej: Electrónicos, Mobiliario..." class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Imagen del Artículo</label>
      <input type="file" name="imagen" class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Factura</label>
      <input type="text" name="factura" placeholder="Número o referencia de factura" class="p-2 border rounded w-full">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Fecha de Adquisición</label>
      <input type="date" name="fecha_adquisicion" class="p-2 border rounded w-full" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Cantidad</label>
      <input type="number" name="cantidad" placeholder="Ej: 1, 2, 3..." class="p-2 border rounded w-full" min="1" required>
    </div>
  </div>

  <div class="mt-6">
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
      Agregar Artículo
    </button>
  </div>
</form>


        <h2 class="text-xl font-bold mt-6">Listado de Stock</h2>

        <div class="overflow-x-auto bg-white p-4 rounded shadow">
            <table class="min-w-full table-auto text-sm text-left">
                <thead class="bg-gray-100 font-bold">
                    <tr>
                        <th class="px-4 py-2">Tipo de Artículo</th>
                        <th class="px-4 py-2">Modelo</th>
                        <th class="px-4 py-2">Marca</th>
                        <th class="px-4 py-2">No. Serie</th>
                        <th class="px-4 py-2">Categoría</th>
                        <th class="px-4 py-2">Imagen</th>
                        <th class="px-4 py-2">Factura</th>
                        <th class="px-4 py-2">Fecha Adquisición</th>
                        <th class="px-4 py-2">Cantidad</th>
                        <th class="px-4 py-2">QR</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM stock";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='border-t text-gray-700 text-center'>";
                            echo "<td>{$row['tipo_articulo']}</td>";
                            echo "<td>{$row['modelo']}</td>";
                            echo "<td>{$row['marca']}</td>";
                            echo "<td>{$row['no_serie']}</td>";
                            echo "<td>{$row['categoria']}</td>";
                            echo "<td><img src='{$row['imagen']}' class='h-12'></td>";
                            echo "<td>{$row['factura']}</td>";
                            echo "<td>{$row['fecha_adquisicion']}</td>";
                            echo "<td>{$row['cantidad']}</td>";
                            echo "<td><button type='button' class='btn btn-success'>Generar</button></td>";
                            //echo "<td><a href='stock.php?delete={$row['id']}' class='text-red-600'>Eliminar</a></td>";
                            echo "<td><a href='stock.php?delete={$row['id']}' class='btn btn-danger'>Eliminar</a></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>