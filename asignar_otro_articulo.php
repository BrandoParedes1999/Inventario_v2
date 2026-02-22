<?php
/**
 * asignar_otro_articulo.php — DEPRECADO
 *
 * BUGS encontrados en la versión anterior (nunca llegó a funcionar):
 *   1. bind_param("iisssss s", ...) — espacio en la máscara → PHP Fatal Error inmediato
 *   2. INSERT incluía columnas 'observaciones' y 'carpeta' que NO existen en
 *      la tabla asignaciones (columnas reales: usuario_id, articulo_id, area,
 *      puesto, fecha, evidencia, pdf, estatus, fecha_devolucion)
 *
 * Reemplazado por: crear_pdf.php
 * Este archivo solo redirige para no dejar una URL sin protección.
 */
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/constants.php';

Session::checkAdmin();

header('Location: ' . BASE_URL . 'usuarios.php');
exit;