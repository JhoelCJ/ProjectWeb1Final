<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/logger.php"); 

$id           = isset($_POST['id_curso']) ? trim($_POST['id_curso']) : '';
$nombre       = trim($_POST['nombre_curso']);
$capacidad    = intval($_POST['capacidad']);
$instructor   = trim($_POST['instructor']); 
$especialidad = trim($_POST['especialidad']); 

if (empty($nombre) || $capacidad <= 0) {
    echo "Error: El nombre y la capacidad son obligatorios.";
    exit;
}

$accionAuditoria = "";

if ($id == "") {
    $check = $conn->query("SELECT id_curso FROM cursos WHERE nombre_curso = '$nombre'");
    if ($check && $check->num_rows > 0) {
        echo "Error: Ya existe un curso con ese nombre.";
        exit;
    }

    $sql = "INSERT INTO cursos (nombre_curso, capacidad, instructor, especialidad, estado_curso) VALUES (?, ?, ?, ?, 'activo')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $nombre, $capacidad, $instructor, $especialidad);
    
    $accionAuditoria = "Crear Curso";

} else {
    $sql = "UPDATE cursos SET nombre_curso=?, capacidad=?, instructor=?, especialidad=? WHERE id_curso=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $nombre, $capacidad, $instructor, $especialidad, $id);
    
    $accionAuditoria = "Editar Curso";
}

if ($stmt->execute()) {
    $detalle = "Curso: $nombre - Instructor: $instructor";
    if($id != "") $detalle .= " (ID: $id)";
    
    registrarAuditoria($conn, $accionAuditoria, $detalle);

    echo "exito";
} else {
    echo "Error en BD: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>