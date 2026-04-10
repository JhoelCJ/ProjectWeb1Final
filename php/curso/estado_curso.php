<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/logger.php");

$id = $_POST['id'];
$accion = $_POST['accion'];
$estado = ($accion == 'activar') ? 'activo' : 'inactivo';

$qry = $conn->prepare("SELECT nombre_curso FROM cursos WHERE id_curso = ?");
$qry->bind_param("i", $id);
$qry->execute();
$qry->bind_result($nombreCurso);
$qry->fetch();
$qry->close();

$sql = "UPDATE cursos SET estado_curso = ? WHERE id_curso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Estado Curso", "Curso '$nombreCurso' cambiado a: $estado");
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>