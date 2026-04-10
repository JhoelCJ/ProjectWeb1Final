<?php
session_start();
include_once("../conexion.php");
include_once("../logger.php"); 

if (!isset($_SESSION['rol']) || ($_SESSION['tipo_sesion'] != 'administrativo')) {
    echo "No tienes permiso.";
    exit;
}

$nombre    = trim($_POST['nombre']);
$apellido  = trim($_POST['apellido']);
$cedula    = trim($_POST['cedula']);
$fecha_nac = $_POST['fecha_nacimiento'];
$nacimiento = new DateTime($fecha_nac);
$hoy        = new DateTime();

$edad = $hoy->diff($nacimiento);

if ($edad->y < 18) {
    echo "Error: El alumno debe ser mayor de edad (Tiene " . $edad->y . " años).";
    exit; 
}
$partes_nombre = explode(" ", $nombre);
$partes_apellido = explode(" ", $apellido);

$primer_nombre = $partes_nombre[0]; 
$primer_apellido = $partes_apellido[0];

$primera_letra_ape = substr($primer_apellido, 0, 1);

$email_base = strtolower($primer_nombre . $primera_letra_ape);

$originales = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
$reemplazos = ['a', 'e', 'i', 'o', 'u', 'n'];
$email_limpio = str_replace($originales, $reemplazos, $email_base);

$email = $email_limpio . "@espe.edu.ec";

$direccion = trim($_POST['direccion']);
$telefono  = trim($_POST['telefono']);

$usuario = $email_limpio;
$clave   = password_hash($cedula, PASSWORD_DEFAULT);

$check = $conn->query("SELECT id_alumno FROM alumnos WHERE cedula = '$cedula' OR email = '$email'");

if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    echo "Error: La cédula o el correo generado ($email) ya existen en el sistema.";
    exit;
}

$sql = "INSERT INTO alumnos (nombre, apellido, cedula, email, usuario, clave, fecha_nacimiento, direccion, telefono, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $nombre, $apellido, $cedula, $email, $usuario, $clave, $fecha_nac, $direccion, $telefono);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Registrar Alumno", "Admin creó al alumno: $nombre $apellido ($cedula) - Email: $email");
    echo "exito";
} else {
    echo "Error BD: " . $stmt->error;
}
$stmt->close();
?>