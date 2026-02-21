<?php
// ─── Rutas base ──────────────────────────────────────────────────
// Detecta si está en localhost o producción automáticamente
define('IS_LOCAL', in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']));

define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);   // Raíz del proyecto
define('BASE_URL',  IS_LOCAL ? 'http://localhost/inventario/' : 'https://tudominio.com/');

// ─── Carpetas de uploads ──────────────────────────────────────────
define('UPLOAD_BASE',       BASE_PATH . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_FACTURAS',   UPLOAD_BASE . 'facturas'    . DIRECTORY_SEPARATOR);
define('UPLOAD_IMAGENES',   UPLOAD_BASE . 'imagenes'    . DIRECTORY_SEPARATOR);
define('UPLOAD_EVIDENCIAS', UPLOAD_BASE . 'evidencias'  . DIRECTORY_SEPARATOR);
define('UPLOAD_RESPONSIVAS',UPLOAD_BASE . 'responsivas' . DIRECTORY_SEPARATOR);
define('UPLOAD_LOGOS',      UPLOAD_BASE . 'logos'       . DIRECTORY_SEPARATOR);

// ─── URLs de uploads (para src de imágenes, href de PDFs) ────────
define('URL_FACTURAS',    BASE_URL . 'uploads/facturas/');
define('URL_IMAGENES',    BASE_URL . 'uploads/imagenes/');
define('URL_RESPONSIVAS', BASE_URL . 'uploads/responsivas/');
define('URL_LOGOS',       BASE_URL . 'uploads/logos/');

// ─── Roles ───────────────────────────────────────────────────────
define('ROL_ADMIN',   'Administrador');
define('ROL_USUARIO', 'Usuario');

// ─── Estatus de artículos ─────────────────────────────────────────
define('ART_DISPONIBLE',   0);
define('ART_ASIGNADO',     1);
define('ART_DESHABILITADO', 2);

// ─── Estatus de usuarios ──────────────────────────────────────────
define('USR_ACTIVO',   0);
define('USR_INACTIVO', 1);

// ─── Estatus de asignaciones ──────────────────────────────────────
define('ASIG_ACTIVA',    1);
define('ASIG_DEVUELTA',  0);

// ─── Crear carpetas de uploads si no existen ─────────────────────
$_carpetas = [
    UPLOAD_FACTURAS,
    UPLOAD_IMAGENES,
    UPLOAD_EVIDENCIAS,
    UPLOAD_RESPONSIVAS,
    UPLOAD_LOGOS,
];
foreach ($_carpetas as $_carpeta) {
    if (!is_dir($_carpeta)) {
        mkdir($_carpeta, 0755, true);
    }
}
unset($_carpetas, $_carpeta);