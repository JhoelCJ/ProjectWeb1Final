<?php
session_start();
include_once("../conexion.php");
include_once("../logger.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 'Alumno') { 
    echo "Error: No tienes permisos administrativos.";
    exit;
}

$idAlumno = isset($_POST['id_alumno']) ? intval($_POST['id_alumno']) : 0;
$idCurso  = isset($_POST['id_curso']) ? intval($_POST['id_curso']) : 0;

if ($idAlumno <= 0 || $idCurso <= 0) {
    echo "Error: Seleccione un alumno y un curso válidos.";
    exit;
}

$check = $conn->query("SELECT id_nota FROM notas WHERE id_alumno = $idAlumno AND id_curso = $idCurso");
if ($check && $check->num_rows > 0) {
    echo "Error: El alumno ya está matriculado en este curso.";
    exit;
}

$sql = "INSERT INTO notas (id_alumno, id_curso, estado_academico, nota1, nota2, nota3, promedio) 
        VALUES (?, ?, 'Cursando', 0, 0, 0, 0)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error SQL: " . $conn->error;
    exit;
}

$stmt->bind_param("ii", $idAlumno, $idCurso);

if ($stmt->execute()) {
    $resA = $conn->query("SELECT apellido, nombre FROM alumnos WHERE id_alumno=$idAlumno");
    $resC = $conn->query("SELECT nombre_curso FROM cursos WHERE id_curso=$idCurso");
    
    $nomA = ($resA && $resA->num_rows > 0) ? ($resA->fetch_assoc())['apellido'] : "ID $idAlumno";
    $nomC = ($resC && $resC->num_rows > 0) ? ($resC->fetch_assoc())['nombre_curso'] : "ID $idCurso";

    registrarAuditoria($conn, "Matrícula Admin", "Matriculó a: $nomA en $nomC");
    
    echo "exito";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>