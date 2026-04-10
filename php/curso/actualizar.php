<?php
include_once("../conexion.php");
include_once("../validarSesion.php");
include_once("../validarRol.php");
include_once("../logger.php");

validarRol(['Administrador', 'Supervisor']);

$id = intval($_POST['id_curso']);
$nombre = trim($_POST['nombre']);
$especialidad = trim($_POST['especialidad']);
$instructor = trim($_POST['instructor']);
$capacidad = intval($_POST['capacidad']);

$sql = "UPDATE cursos SET nombre_curso=?, especialidad=?, instructor=?, capacidad=? WHERE id_curso=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $nombre, $especialidad, $instructor, $capacidad, $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Editar Curso", "ID: $id - Nombre: $nombre");
    echo "<script>
        alert('Curso actualizado correctamente');
    </script>";

} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
