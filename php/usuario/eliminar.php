<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/verificarPermiso.php");
include_once("../../php/logger.php");

if ($_SESSION['rol'] != 'Administrador') { echo "No tienes permisos"; exit; }

$id = $_POST['id'];


$check = $conn->query("SELECT u.usuario_user, r.nombre_rol FROM usuarios u INNER JOIN rol r ON u.id_rol = r.id_rol WHERE u.id_user = $id");
$row = $check->fetch_assoc();

if ($row && strcasecmp($row['nombre_rol'], 'Administrador') === 0) {
    echo "Error: No se puede desactivar al Administrador principal.";
    exit;
}
$nombreUsuario = $row['usuario_user']; 


$sql = "UPDATE usuarios SET estado_user = 'inactivo' WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Desactivar Usuario", "Usuario desactivado: " . $nombreUsuario);
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>