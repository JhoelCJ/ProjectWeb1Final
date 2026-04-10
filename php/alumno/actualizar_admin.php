<?php
include_once("../conexion.php");
include_once("../validarSesion.php");
include_once("../logger.php");

if ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Supervisor') {
    echo "No tienes permisos.";
    exit;
}

$id        = $_POST['id_alumno'];
$nombre    = trim($_POST['nombre']);
$apellido  = trim($_POST['apellido']);
$email     = trim($_POST['email']);
$telefono  = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);

$check = $conn->query("SELECT id_alumno FROM alumnos WHERE email = '$email' AND id_alumno != $id");
if ($check->num_rows > 0) {
    echo "Error: El correo ya pertenece a otro alumno.";
    exit;
}

$sql = "UPDATE alumnos SET nombre=?, apellido=?, email=?, telefono=?, direccion=? WHERE id_alumno=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $direccion, $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Editar Alumno (Admin)", "Se modificó al alumno ID: $id");
    echo "exito";
} else {
    echo "Error BD: " . $stmt->error;
}
$stmt->close();
?>