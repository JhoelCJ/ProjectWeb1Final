<?php
include_once("../conexion.php");
include_once("../validarSesion.php");
include_once("../logger.php");

if ($_SESSION['rol'] != 'Administrador') { echo "No autorizado"; exit; }

$id = $_POST['id'];
$accion = $_POST['accion'];
$estado = ($accion == 'activar') ? 'activo' : 'inactivo';

$res = $conn->query("SELECT apellido FROM alumnos WHERE id_alumno = $id");
$nom = $res->fetch_assoc()['apellido'];

$sql = "UPDATE alumnos SET estado = ? WHERE id_alumno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Estado Alumno", "Alumno $nom cambiado a: $estado");
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
?>