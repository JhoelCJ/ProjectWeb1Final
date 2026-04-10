<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/verificarPermiso.php");
include_once("../../php/logger.php");

if ($_SESSION['rol'] != 'Administrador') { echo "No tienes permisos"; exit; }

$id = $_POST['id'];

$qry = $conn->prepare("SELECT usuario_user FROM usuarios WHERE id_user = ?");
$qry->bind_param("i", $id);
$qry->execute();
$qry->bind_result($nombreUsuario);
$qry->fetch();
$qry->close();

$sql = "UPDATE usuarios SET estado_user = 'activo', intentos_fallidos = 0, bloqueo_hasta = NULL WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Activar Usuario", "Usuario reactivado: " . $nombreUsuario);
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>