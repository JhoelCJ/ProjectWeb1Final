<?php
session_start();
include_once("../conexion.php");
include_once("../logger.php");

if (!isset($_SESSION['tipo_sesion']) || $_SESSION['tipo_sesion'] != 'alumno') {
    echo "Acceso denegado."; exit;
}

$id    = $_SESSION['id_alumno'];
$email = $_POST['email'];
$dir   = $_POST['direccion'];
$tel   = $_POST['telefono'];
$claveActual = isset($_POST['clave_actual']) ? $_POST['clave_actual'] : '';
$claveNueva  = isset($_POST['clave_nueva']) ? $_POST['clave_nueva'] : '';

if (!preg_match("/@espe\.edu\.ec$/", $email)) {
    echo "Error: El correo debe terminar en @espe.edu.ec";
    exit;
}

if (!empty($claveNueva)) {
    
    if (empty($claveActual)) {
        echo "Error: Para cambiar la contraseña, debes ingresar tu contraseña actual.";
        exit;
    }

    $sqlCheck = "SELECT clave FROM alumnos WHERE id_alumno = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    $fila = $resCheck->fetch_assoc();
    $hashGuardado = $fila['clave'];
    $stmtCheck->close();

    if (!password_verify($claveActual, $hashGuardado)) {
        echo "Error: La contraseña actual es incorrecta. No se realizaron cambios.";
        exit;
    }

    $nuevoHash = password_hash($claveNueva, PASSWORD_DEFAULT);
    
    $sql = "UPDATE alumnos SET email=?, direccion=?, telefono=?, clave=? WHERE id_alumno=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $email, $dir, $tel, $nuevoHash, $id);
    $detalle = "Alumno actualizó perfil y cambió su contraseña";

} else {
    $sql = "UPDATE alumnos SET email=?, direccion=?, telefono=? WHERE id_alumno=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $email, $dir, $tel, $id);
    $detalle = "Alumno actualizó datos de contacto";
}

if ($stmt->execute()) {
    registrarAuditoria($conn, "Perfil Alumno", $detalle);
    echo "exito";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>