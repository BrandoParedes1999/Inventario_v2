<?php
/**
 * includes/navbar.php
 * Topbar — depende de config/session.php y BASE_ASSETS definido en header.php
 */
$_navUserName = Session::userName();
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle Mobile -->
    <button id="sidebarToggle" class="btn btn-link d-md-none rounded-circle me-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ms-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Usuario dropdown -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">
                    <?= htmlspecialchars($_navUserName) ?>
                </span>
                <img class="img-profile rounded-circle"
                     src="<?= BASE_ASSETS ?>img/undraw_profile.svg"
                     alt="Perfil">
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <li>
                    <a class="dropdown-item" href="<?= BASE_URL ?>logout.php">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                        Cerrar Sesión
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>