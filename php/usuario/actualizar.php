<?php
session_start();
include_once("../../php/conexion.php");
include_once("../../php/logger.php");
include_once("../../php/verificarPermiso.php");

$idLogueado = $_SESSION['id_user'];
$rolSesion  = $_SESSION['rol'];

$idTarget   = intval($_POST['id_user']);

$esAdmin        = ($rolSesion === 'Administrador');
$tienePermiso   = tienePermiso('editar_usuario');
$esAutoEdicion  = ($idLogueado == $idTarget);

if (!$esAdmin && !$tienePermiso && !$esAutoEdicion) {
    echo "Acceso Denegado: No tienes permisos para realizar esta acción.";
    exit;
}

if ($idTarget == 1) {
    echo "Error Crítico: El Usuario Super-Administrador (ID 1) es inmutable.";
    registrarAuditoria($conn, "Intento de Hackeo", "Usuario ID $idLogueado intentó editar al Super Admin ID 1");
    exit;
}

$nombre       = trim($_POST['nombre']);
$apellido     = trim($_POST['apellido']);
$usuario      = trim($_POST['usuario']);
$cedula       = trim($_POST['cedula']);
$rolPost      = intval($_POST['rol']); 

$claveNueva   = isset($_POST['clave_nueva']) ? $_POST['clave_nueva'] : '';
$claveConfirm = isset($_POST['clave_confirm']) ? $_POST['clave_confirm'] : '';
$passActual   = isset($_POST['admin_password']) ? $_POST['admin_password'] : ''; 

if ($esAdmin) {
    $rolFinal = $rolPost;
} else {
    $qRol = $conn->query("SELECT id_rol FROM usuarios WHERE id_user = $idTarget");
    $rRol = $qRol->fetch_assoc();
    $rolFinal = $rRol['id_rol'];
}

if (empty($passActual)) {
    echo "Error: Debe ingresar SU contraseña actual para confirmar los cambios.";
    exit;
}

$sqlCheckPass = "SELECT clave_user FROM usuarios WHERE id_user = ?";
$stmtA = $conn->prepare($sqlCheckPass);
$stmtA->bind_param("i", $idLogueado);
$stmtA->execute();
$resA = $stmtA->get_result();
$filaA = $resA->fetch_assoc();

if (!$filaA || !password_verify($passActual, $filaA['clave_user'])) {
    registrarAuditoria($conn, "Fallo de Seguridad", "Usuario ID $idLogueado falló contraseña al editar usuario ID $idTarget");
    echo "Error: Su contraseña de autorización es incorrecta. Acción denegada.";
    exit;
}

$check = $conn->query("SELECT id_user FROM usuarios WHERE (usuario_user = '$usuario' OR cedula_user = '$cedula') AND id_user != $idTarget");
if ($check->num_rows > 0) {
    echo "Error: El usuario o la cédula ya están en uso por otra persona.";
    exit;
}

if (!empty($claveNueva)) {
    if ($claveNueva !== $claveConfirm) {
        echo "Error: La confirmación de la nueva contraseña no coincide.";
        exit;
    }

    $nuevoHash = password_hash($claveNueva, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET nombre_user=?, apellido_user=?, usuario_user=?, cedula_user=?, id_rol=?, clave_user=? WHERE id_user=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisi", $nombre, $apellido, $usuario, $cedula, $rolFinal, $nuevoHash, $idTarget);
    
    $detalle = "Usuario actualizó perfil y contraseña";

} else {
    $sql = "UPDATE usuarios SET nombre_user=?, apellido_user=?, usuario_user=?, cedula_user=?, id_rol=? WHERE id_user=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $nombre, $apellido, $usuario, $cedula, $rolFinal, $idTarget);
    
    $detalle = "Usuario actualizó datos de perfil";
}

if ($stmt->execute()) {
    $autor = ($esAutoEdicion) ? "Auto-edición" : "Editado por ID $idLogueado";
    registrarAuditoria($conn, "Gestión de Usuarios", "$detalle: $usuario ($cedula). [$autor]");
    
    echo "exito";
} else {
    echo "Error BD: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>