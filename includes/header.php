<?php
/**
 * includes/header.php
 * Layout base: <head> + sidebar
 *
 * Assets en: /assets/vendor/, /assets/css/, /assets/icofont/, /assets/js/
 */

if (!defined('DB_HOST')) {
    die('Acceso directo no permitido.');
}

$pageTitle   = $pageTitle ?? 'Inventario';
$currentPage = basename($_SERVER['PHP_SELF']);

if (!defined('BASE_ASSETS')) {
    define('BASE_ASSETS', BASE_URL . 'assets/');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?> | Sistema de Inventario</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="<?= BASE_ASSETS ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- IcoFont -->
    <link href="<?= BASE_ASSETS ?>icofont/icofont.min.css" rel="stylesheet">

    <!-- SB Admin 2 ← dibuja el sidebar -->
    <link href="<?= BASE_ASSETS ?>css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<script>
    if (performance.navigation.type === 2) { location.reload(true); }
</script>

<div id="wrapper">

    <!-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Brand / Logo -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center"
           href="<?= BASE_URL ?>dashboard.php">
            <div class="sidebar-brand-icon">
                <img src="<?= BASE_ASSETS ?>img/logo.jpeg"
                     alt="Logo" style="width:40px;height:40px;border-radius:50%;">
            </div>
            <div class="sidebar-brand-text mx-3">Sistema Inventario</div>
        </a>

        <hr class="sidebar-divider my-0">

        <!-- Inventario -->
        <li class="nav-item <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= BASE_URL ?>dashboard.php">
                <i class="fas fa-cubes"></i>
                <span>Inventario</span>
            </a>
        </li>

        <?php if (Session::isAdmin()): ?>

        <!-- Usuarios -->
        <li class="nav-item <?= $currentPage === 'usuarios.php' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= BASE_URL ?>usuarios.php">
                <i class="icofont-ui-user"></i>
                <span>Usuarios</span>
            </a>
        </li>

        <!-- Empresa -->
        <li class="nav-item <?= $currentPage === 'empresa.php' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= BASE_URL ?>empresa.php">
                <i class="fas fa-building"></i>
                <span>Empresa</span>
            </a>
        </li>

        <?php endif; ?>

        <!-- Historial -->
        <li class="nav-item <?= $currentPage === 'historial_articulos.php' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= BASE_URL ?>historial_articulos.php">
                <i class="fas fa-history"></i>
                <span>Historial de Asignaciones</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">