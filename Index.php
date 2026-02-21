<?php
    require_once __DIR__ . '/config/session.php';
    require_once __DIR__ . '/config/constants.php';

    // Si ya hay sesión activa, redirigir al dashboard
    if (Session::isLoggedIn()) {
        header('Location: ' . BASE_URL . 'dashboard.php');
        exit;
    }

    // ── Generar token CSRF para el formulario de login ──────────
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Mensaje de error según parámetro GET
    $errores = [
        'sin_sesion'       => 'Tu sesión ha expirado. Inicia sesión nuevamente.',
        'session_expirada' => 'Tu sesión fue cerrada por inactividad o desde otro dispositivo.',
        'no_token'         => 'Sesión inválida. Por favor inicia sesión.',
        'sin_permiso'      => 'No tienes permisos para acceder a esa sección.',
        'csrf'             => 'Error de seguridad. Intenta de nuevo.',
        'credenciales'     => 'Usuario o contraseña incorrectos.',
        'campos_vacios'    => 'Completa todos los campos.',
    ];
    $errorMsg = $errores[$_GET['error'] ?? ''] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Inventario</title>

    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

    <script>
        if (performance.navigation.type === 2) {
            location.reload(true);
        }
    </script>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center p-5"
                                style="background: linear-gradient(135deg,rgb(99,125,240),rgb(75,134,162)); color: white; border-top-left-radius:.35rem; border-bottom-left-radius:.35rem;">
                                <img src="assets/img/logo.jpeg" alt="Logo" class="mb-4 shadow-lg"
                                    style="max-width:100px;border-radius:50%;">
                                <h2 class="mb-3 fw-bold display-6 text-white text-center">Sistema de Inventario</h2>
                                <ul class="list-unstyled text-start w-100" style="max-width:280px;font-size:1rem;">
                                    <li class="mb-3">Control de inventario</li>
                                    <li class="mb-3">Generar QR</li>
                                    <li class="mb-3">Accesibilidad</li>
                                </ul>
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Bienvenido</h1>
                                    </div>

                                    <?php if (!empty($errorMsg)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($errorMsg) ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" action="login.php" method="POST">
                                        <!-- TOKEN CSRF -->
                                        <input type="hidden" name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                        <div class="form-group">
                                            <input name="usuario" type="text"
                                                class="form-control form-control-user"
                                                placeholder="Usuario" required autocomplete="username">
                                        </div>
                                        <div class="form-group">
                                            <input name="contrasena" type="password"
                                                class="form-control form-control-user"
                                                placeholder="Contraseña" required autocomplete="current-password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recordarme</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Iniciar Sesión
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Recuperar Contraseña</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

</body>
</html>