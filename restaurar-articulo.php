<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

$id                   = intval($_POST['articulo_id']         ?? 0);
$motivo_restauracion  = trim($_POST['motivo_restauracion']   ?? 'Restauración sin motivo');
$usuario_id           = intval($_POST['usuario_id']          ?? 0);

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=datos_invalidos');
    exit;
}

// ── Actualizar registro en bajas_articulos ────────────────────────
$stmtBaja = $conexion->prepare(
    "UPDATE bajas_articulos
     SET motivo_restauracion = ?, fecha_restauracion = NOW()
     WHERE articulo_id = ?
     ORDER BY fecha_baja DESC
     LIMIT 1"
);
$stmtBaja->bind_param("si", $motivo_restauracion, $id);
$stmtBaja->execute();
$stmtBaja->close();

// ── Marcar asignación como devuelta ───────────────────────────────
$stmtAsig = $conexion->prepare(
    "UPDATE asignaciones SET fecha_devolucion = NOW(), estatus = 0
     WHERE articulo_id = ? AND fecha_devolucion IS NULL
     ORDER BY fecha DESC
     LIMIT 1"
);
$stmtAsig->bind_param("i", $id);
$stmtAsig->execute();
$stmtAsig->close();

// ── Cambiar artículo a disponible ─────────────────────────────────
$estatus  = ART_DISPONIBLE; // 0
$stmtUpd  = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ?");
$stmtUpd->bind_param("ii", $estatus, $id);
$stmtUpd->execute();
$stmtUpd->close();

header('Location: ' . BASE_URL . 'dashboard.php?msg=restaurado');
exit;