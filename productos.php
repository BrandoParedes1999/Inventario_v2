<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Sistema de Inventario</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-blue-700 text-white min-h-screen flex flex-col">
    <div class="p-6 text-2xl font-bold border-b border-blue-600">
      📊 Dashboard
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
      <a href="productos.php" class="block px-4 py-2 rounded bg-blue-600">Productos</a>
      <a href="users.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Usuarios</a>
      <a href="stock.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Stock</a>
      <a href="#" class="block px-4 py-2 hover:bg-blue-600 rounded">Settings</a>
      <a href="logout.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Cerrar Sesion</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <h1 class="text-2xl font-semibold mb-6">Profile Dashboard</h1>

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex items-center space-x-6">
        <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-3xl text-gray-500">
          👤
        </div>
        <div class="flex-1 grid grid-cols-2 gap-4">
          <div>
            <h2 class="text-xl font-bold">Admin User</h2>
            <p class="text-gray-500">Administrador</p>
            <p>Email: <span class="font-medium">admin@example.com</span></p>
            <p>Rol: <span class="font-medium">Administrador</span></p>
          </div>
          <div>
            <p>Phone: <span class="font-medium">+1 (555) 123-4567</span></p>
            <p>Member Since: <span class="font-medium">Jan 15, 2023</span></p>
          </div>
        </div>
      </div>
      <div class="mt-4 flex space-x-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar perfil</button>
        <button class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">Cambiar contraseña</button>
      </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold mb-2">Total Productos</h3>
        <p class="text-3xl font-bold text-gray-700">--</p>
        <p class="text-green-500 mt-1">↑ % from last month</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold mb-2">Usuarios Activos</h3>
        <p class="text-3xl font-bold text-gray-700">--</p>
        <p class="text-green-500 mt-1">↑ % from last month</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold mb-2">Artículos con bajo stock</h3>
        <p class="text-3xl font-bold text-gray-700">--</p>
        <p class="text-red-500 mt-1">↑ compared to last week</p>
      </div>
    </div>

  </main>

</body>
</html>
