<?php
// acceso_denegado.php

session_start();
$_SESSION['acceso_denegado'] = true;
header('Location: dashboard.php'); // o a la página que quieras mostrar el modal
exit;
?>
