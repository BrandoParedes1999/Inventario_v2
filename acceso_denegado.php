<?php
/**
 * acceso_denegado.php
 *
 * CORRECCIÓN: usaba session_start() raw sin cargar config/session.php,
 * lo que impedía leer SESSION correctamente y no tenía $conexion disponible.
 * Ahora usa el sistema unificado.
 *
 * Nota: Session::checkRole() en config/session.php ya establece el flag
 * acceso_denegado y redirige al dashboard. Este archivo actúa como
 * fallback para URLs directas antiguas.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

// Si no hay sesión, redirigir al login
if (!Session::isLoggedIn()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Establecer flag para que footer.php muestre el modal de acceso denegado
$_SESSION['acceso_denegado'] = true;
header('Location: ' . BASE_URL . 'dashboard.php');
exit;