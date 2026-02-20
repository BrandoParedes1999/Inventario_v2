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
    <title>Panel de Control - Sistema de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <aside class="w-64 bg-blue-700 text-white min-h-screen flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-blue-600">
            📊 Panel de Control
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <div class="flex items-center mb-6 space-x-2">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                    👤
                </div>
                <div>
                    <div class="font-semibold">Usuario Admin</div>
                    <div class="text-sm text-blue-200">Administrador</div>
                </div>
            </div>
            <a href="dashboard.php" class="block px-4 py-2 hover:bg-blue-600">Perfil</a>
            <a href="productos.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Productos</a>
            <a href="users.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Usuarios</a>
            <a href="stock.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Stock</a>
            <a href="settings.php" class="block px-4 py-2 rounded bg-blue-600 rounded">Configuración</a>
            <a href="logout.php" class="block px-4 py-2 hover:bg-blue-600 rounded">Cerrar Sesión</a>
        </nav>
    </aside>

    <main class="flex-1 p-6">
        <h1 class="text-2xl font-semibold mb-6">Panel de Perfil</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-3xl text-gray-500">
                    👤
                </div>
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <h2 class="text-xl font-bold">Usuario Admin</h2>
                        <p class="text-gray-500">Administrador</p>
                        <p>Correo Electrónico: <span class="font-medium">admin@example.com</span></p>
                        <p>Rol: <span class="font-medium">Administrador</span></p>
                    </div>
                    <div>
                        <p>Teléfono: <span class="font-medium">+1 (555) 123-4567</span></p>
                        <p>Miembro Desde: <span class="font-medium">Ene 15, 2023</span></p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Editar perfil</button>
                <button class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">Cambiar contraseña</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-2">Total Productos</h3>
                <p class="text-3xl font-bold text-gray-700">--</p>
                <p class="text-green-500 mt-1">↑ % del mes pasado</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-2">Usuarios Activos</h3>
                <p class="text-3xl font-bold text-gray-700">--</p>
                <p class="text-green-500 mt-1">↑ % del mes pasado</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-2">Artículos con bajo stock</h3>
                <p class="text-3xl font-bold text-gray-700">--</p>
                <p class="text-red-500 mt-1">↑ comparado con la semana pasada</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Tareas Diarias</h3>
                    <a href="#" class="text-blue-600 hover:underline text-sm">Ver todo</a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                            <span class="text-gray-700">Crear nueva tarea para nuevo proyecto</span>
                        </label>
                        <span class="text-gray-500 text-sm">Hoy, 10:00 AM</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded" checked>
                            <span class="text-gray-700 line-through text-gray-500">Llamada con cliente (App móvil)</span>
                        </label>
                        <span class="text-gray-500 text-sm">Hoy, 09:30 AM</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                            <span class="text-gray-700">Reunión con el nuevo personal</span>
                        </label>
                        <span class="text-gray-500 text-sm">Ayer, 04:00 PM</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded">
                            <span class="text-gray-700">Diseñar una nueva creatividad para el equipo de marketing</span>
                        </label>
                        <span class="text-gray-500 text-sm">Dic 10, 2024, 02:00 PM</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold mb-4">Almacenamiento</h3>
                    <div class="flex items-center justify-between text-gray-700 mb-2">
                        <span>256GB</span>
                        <span>500GB Total</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 50%;"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Tienes suficiente espacio para tu proyecto</p>
                    <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm w-full">Administrar almacenamiento</button>
                </div>

                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <h3 class="text-xl font-semibold mb-4">Rendimiento</h3>
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-4xl font-bold relative">
                            80%
                            <div class="absolute -top-2 right-2 text-green-500 text-2xl">↑</div>
                        </div>
                        <p class="text-gray-500 mt-4">Buen rendimiento</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Tu Proyecto</h3>
                <a href="#" class="text-blue-600 hover:underline text-sm">Ver todo</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 text-xl">
                        💡
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Rediseño de Sitio Web</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: 75%;"></div>
                        </div>
                    </div>
                    <span class="text-gray-700 text-sm">75%</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 text-xl">
                        📈
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Campaña de Marketing</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-yellow-600 h-1.5 rounded-full" style="width: 60%;"></div>
                        </div>
                    </div>
                    <span class="text-gray-700 text-sm">60%</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-xl">
                        📦
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Lanzamiento de Producto</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-green-600 h-1.5 rounded-full" style="width: 90%;"></div>
                        </div>
                    </div>
                    <span class="text-gray-700 text-sm">90%</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xl">
                        🛠️
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Corrección de Errores</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-red-600 h-1.5 rounded-full" style="width: 30%;"></div>
                        </div>
                    </div>
                    <span class="text-gray-700 text-sm">30%</span>
                </div>
            </div>
        </div>

    </main>

</body>
</html>