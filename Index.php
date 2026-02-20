<?php
    include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inventario</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script>
    // Detectar navegación por botón "atrás" y forzar recarga real
    if (performance.navigation.type === 2) {
        location.reload(true);
    }
    </script>


</head>

<body class="bg-gradient-primary">
    
<div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-flex flex-column align-items-center justify-content-center p-5" 
                        style="background: linear-gradient(135deg,rgb(99, 125, 240),rgb(75, 134, 162)); color: white; border-top-left-radius: .35rem; border-bottom-left-radius: .35rem;">
                           
                            <img src="logo.jpeg" alt="Logo" class="mb-4 shadow-lg" style="max-width: 100px; border-radius: 50%;">

                            <h2 class="mb-3 fw-bold display-6 text-white text-center">Sistema de Inventario</h2>

                        <ul class="list-unstyled text-start w-100" style="max-width: 280px; font-size: 1rem;">
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
                                    <form class="user" action="login.php" method="POST"> <!-- Inicio Formulario -->
                                        <div class="form-group">
                                            <input name="usuario" type="text" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Usuario">
                                        </div>
                                        <div class="form-group">
                                            <input name="contrasena" type="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Contraseña">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Recordarme</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Iniciar Sesion
                                        </button>
                                        <!-- Inicio Complemento de Inicio de sesion -->
                                        <!-- <hr>
                                        <a href="index.html" class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i> Login with Google
                                        </a>
                                        <a href="index.html" class="btn btn-facebook btn-user btn-block">
                                            <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                        </a>-->
                                        <!-- Fin complemento de Inicio de sesion -->
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Recuperar Contraseña</a>
                                    </div>
                                    <!-- Inicio Crear cuenta -->
                                <!--<div class="text-center">
                                        <a class="small" href="register.html">Create an Account!</a>
                                    </div>-->
                                    <!-- Fin crear cuenta -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>