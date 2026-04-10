<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/verificarPermiso.php");
include_once("../../php/logger.php");

exigirPermiso('gestionar_roles');

$nombreRol = trim($_POST['nombre_rol']);
$descRol = trim($_POST['descripcion_rol']);
$permisosSeleccionados = isset($_POST['permisos']) ? $_POST['permisos'] : [];
$idRolEdit = isset($_POST['id_rol_edit']) ? $_POST['id_rol_edit'] : '';

if (!empty($idRolEdit)) {
    $idRol = intval($idRolEdit);

    if ($idRol == 1) {
        echo "<script>
            alert('Error Crítico: El Rol Administrador (ID 1) es inmutable y no puede ser modificado.');
            window.location.href='../../main.php';
        </script>";
        registrarAuditoria($conn, "Seguridad", "Intento de modificación del Rol Administrador bloqueado.");
        exit;
    }

    if ($_SESSION['rol'] == 'Administrador') {
        
        $sql = "UPDATE rol SET nombre_rol=?, descripcion_rol=? WHERE id_rol=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombreRol, $descRol, $idRol);
        $stmt->execute();
        $stmt->close();

        if (tienePermiso('asignar_permisos')) {
            $conn->query("DELETE FROM rol_permisos WHERE id_rol=$idRol");
            if (!empty($permisosSeleccionados)) {
                foreach ($permisosSeleccionados as $idPermiso) {
                    $conn->query("INSERT INTO rol_permisos (id_rol, id_permiso) VALUES ($idRol, $idPermiso)");
                }
            }
        }

        registrarAuditoria($conn, "Editar Rol", "Se modificó el rol ID: $idRol - Nombre: $nombreRol");
        $mensaje = "Rol actualizado correctamente.";

    } else {
        echo "<script>
            alert('Error: Solo el Administrador puede editar roles existentes.');
            window.location.href='../../main.php';
        </script>";
        exit;
    }

} else {
    $stmt = $conn->prepare("INSERT INTO rol (nombre_rol, descripcion_rol) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombreRol, $descRol);
    
    if ($stmt->execute()) {
        $idRolNuevo = $conn->insert_id;
        
        if (tienePermiso('asignar_permisos') && !empty($permisosSeleccionados)) {
            foreach ($permisosSeleccionados as $idPermiso) {
                $conn->query("INSERT INTO rol_permisos (id_rol, id_permiso) VALUES ($idRolNuevo, $idPermiso)");
            }
        }

        registrarAuditoria($conn, "Crear Rol", "Se creó el nuevo rol: $nombreRol");
        $mensaje = "Rol creado correctamente.";
    } else {
        $mensaje = "Error al crear rol: " . $conn->error;
    }
}

echo "<script>
    alert('$mensaje');
    window.location.href='../../main.php'; 
</script>";

$conn->close();
?>