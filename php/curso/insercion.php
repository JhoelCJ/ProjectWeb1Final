<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../validarRol.php"); 
include_once("../../php/logger.php"); 

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Supervisor')) {
    echo "Error: No tienes permisos.";
    exit;
}

$nombre = trim($_POST['nombre']);
$especialidad = trim($_POST['especialidad']);
$instructor = trim($_POST['instructor']);
$capacidad = intval($_POST['capacidad']);

$sql = "INSERT INTO cursos (nombre_curso, especialidad, instructor, capacidad, estado_curso) VALUES (?, ?, ?, ?, 'activo')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nombre, $especialidad, $instructor, $capacidad);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Crear Curso (Inserción)", "Curso: $nombre - Inst: $instructor");
    
    echo "exito"; 
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>