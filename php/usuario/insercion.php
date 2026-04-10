<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/logger.php"); 

$usuario   = $_POST['usuario'];
$nombre    = $_POST['nombre'];
$apellido  = $_POST['apellido'];
$cedula    = $_POST['cedula'];
$clave     = password_hash($_POST['clave'], PASSWORD_DEFAULT);
$id_rol    = $_POST['rol'];
$fecha_nac = $_POST['fecha_nacimiento'];
$estado    = 'activo';

if (isset($_POST['rol']) && $_POST['rol'] !== "") {
    $id_rol = $_POST['rol'];
} else {
    $id_rol = null; 
}

try {
    $fechaObj = new DateTime($fecha_nac);
    $hoy      = new DateTime();
    $edad     = $hoy->diff($fechaObj);

    if ($edad->y < 18) {
        echo "Error: El usuario debe ser mayor de edad para registrarse (Tiene " . $edad->y . " años).";
        exit; 
    }
} catch (Exception $e) {
    echo "Error: Formato de fecha inválido.";
    exit;
}

$check = $conn->query("SELECT id_user FROM usuarios WHERE usuario_user = '$usuario' OR cedula_user = '$cedula'");
if ($check->num_rows > 0) {
    echo "Error: El usuario o la cédula ya existen.";
    exit;
}

$sql = "INSERT INTO usuarios (usuario_user, nombre_user, apellido_user, cedula_user, clave_user, id_rol, fecha_nacimiento, estado_user, intentos_fallidos) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiss", $usuario, $nombre, $apellido, $cedula, $clave, $id_rol, $fecha_nac, $estado);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Registrar Usuario", "Nuevo registro: $usuario - Cédula: $cedula");

    echo "exito"; 
} else {
    echo "Error al registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>