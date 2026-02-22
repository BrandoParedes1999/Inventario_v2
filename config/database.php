<?php
//hola new
// ─── Configuración ───────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');           // ⚠️ Cambiar en producción
define('DB_NAME',    'inventario');
define('DB_CHARSET', 'utf8mb4');

// ─── Conexión ────────────────────────────────────────────────────
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conexion->connect_error) {
    // En producción: loggear el error, no mostrarlo
    error_log('DB Error: ' . $conexion->connect_error);
    http_response_code(500);
    die(json_encode(['error' => 'Error de conexión a la base de datos.']));
}

// Charset
$conexion->set_charset(DB_CHARSET);