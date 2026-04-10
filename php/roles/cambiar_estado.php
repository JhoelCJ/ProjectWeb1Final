<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/verificarPermiso.php");
include_once("../../php/logger.php");

exigirPermiso('gestionar_roles');

$idRol = isset($_POST['id']) ? intval($_POST['id']) : 0;
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($idRol <= 0 || !in_array($accion, ['activar', 'desactivar'])) {
    echo "Datos inválidos";
    exit;
}

if ($idRol == 1) {
    echo "Error Crítico: El Rol Administrador (ID 1) no puede ser desactivado ni modificado.";
    exit;
}

$nuevoEstado = ($accion === 'activar') ? 'activo' : 'inactivo';

$sql = "UPDATE rol SET estado_rol = ? WHERE id_rol = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nuevoEstado, $idRol);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Estado Rol", "Se cambió el rol ID $idRol a: $nuevoEstado");
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>