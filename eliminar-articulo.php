<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit;
}

$id          = intval($_POST['id']);
$motivo_baja = trim($_POST['motivo_baja'] ?? '');

if ($id <= 0) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=datos_invalidos');
    exit;
}

// ── Obtener datos del artículo ────────────────────────────────────
$stmtSel = $conexion->prepare(
    "SELECT articulo, marca, modelo, numero_serie, categoria FROM articulo WHERE id = ?"
);
$stmtSel->bind_param("i", $id);
$stmtSel->execute();
$row = $stmtSel->get_result()->fetch_assoc();
$stmtSel->close();

if (!$row) {
    header('Location: ' . BASE_URL . 'dashboard.php?error=no_encontrado');
    exit;
}

// ── Insertar en bajas_articulos ───────────────────────────────────
$stmtBaja = $conexion->prepare(
    "INSERT INTO bajas_articulos (articulo_id, articulo, marca, modelo, numero_serie, categoria, motivo_baja)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmtBaja->bind_param(
    "issssss",
    $id,
    $row['articulo'], $row['marca'], $row['modelo'],
    $row['numero_serie'], $row['categoria'],
    $motivo_baja
);

if ($stmtBaja->execute()) {
    $stmtBaja->close();

    // Marcar artículo como deshabilitado
    $stmtUpd = $conexion->prepare("UPDATE articulo SET estatus = ? WHERE id = ?");
    $estatus  = ART_DESHABILITADO; // 2
    $stmtUpd->bind_param("ii", $estatus, $id);
    $stmtUpd->execute();
    $stmtUpd->close();

    header('Location: ' . BASE_URL . 'dashboard.php?msg=articulo_dado_de_baja');
    exit;
} else {
    $err = $stmtBaja->error;
    $stmtBaja->close();
    die("Error al registrar la baja: " . htmlspecialchars($err));
}