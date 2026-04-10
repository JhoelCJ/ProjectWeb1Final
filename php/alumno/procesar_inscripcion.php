<?php
session_start();
include_once("../conexion.php");
include_once("../logger.php");

if (!isset($_SESSION['tipo_sesion']) || $_SESSION['tipo_sesion'] != 'alumno') {
    echo "Acceso denegado."; exit;
}

$idAlumno = $_SESSION['id_alumno'];
$idCurso  = $_POST['id_curso'];

$check = $conn->query("SELECT id_nota FROM notas WHERE id_alumno = $idAlumno AND id_curso = $idCurso");
if ($check->num_rows > 0) {
    echo "Ya estás inscrito."; exit;
}

$sql = "INSERT INTO notas (id_alumno, id_curso, estado_academico, nota1, nota2, nota3, promedio) 
        VALUES (?, ?, 'Cursando', 0, 0, 0, 0)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idAlumno, $idCurso);

if ($stmt->execute()) {
    $resC = $conn->query("SELECT nombre_curso FROM cursos WHERE id_curso = $idCurso");
    $nomCurso = ($resC->num_rows > 0) ? $resC->fetch_assoc()['nombre_curso'] : "ID $idCurso";

    registrarAuditoria($conn, "Auto-Inscripción", "Alumno se inscribió en: $nomCurso");
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
?>