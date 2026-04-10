<?php
session_start();
require_once("conexion.php");
require_once("logger.php"); 

$usuario = $_POST['usuario'];
$clave   = $_POST['clave'];

$sql = "SELECT u.id_user, u.usuario_user, u.clave_user, u.id_rol, 
               r.nombre_rol, r.estado_rol, 
               u.intentos_fallidos, u.bloqueo_hasta, u.estado_user
        FROM usuarios u
        INNER JOIN rol r ON u.id_rol = r.id_rol
        WHERE u.usuario_user = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $fila = $result->fetch_assoc();

    if ($fila['estado_user'] !== 'activo') {
        header("Location: ../index.php?ruta=login&error=usuario_inactivo");
        exit;
    }

    if (password_verify($clave, $fila['clave_user'])) {
        $conn->query("UPDATE usuarios SET intentos_fallidos = 0, bloqueo_hasta = NULL WHERE id_user = " . $fila['id_user']);

        session_regenerate_id(true);
        $_SESSION['id_user'] = $fila['id_user'];
        $_SESSION['usuario'] = $fila['usuario_user'];
        $_SESSION['rol']     = $fila['nombre_rol'];
        $_SESSION['id_rol']  = $fila['id_rol']; 
        $_SESSION['tipo_sesion'] = 'administrativo'; 

        $permisosArray = [];
        if ($fila['estado_rol'] == 'activo') {
            $sqlPermisos = "SELECT p.nombre_clave FROM permisos p 
                            INNER JOIN rol_permisos rp ON p.id_permiso = rp.id_permiso
                            WHERE rp.id_rol = ?";
            $stmtP = $conn->prepare($sqlPermisos);
            $stmtP->bind_param("i", $fila['id_rol']);
            $stmtP->execute();
            $resP = $stmtP->get_result();
            while($rowP = $resP->fetch_assoc()){ $permisosArray[] = $rowP['nombre_clave']; }
            $stmtP->close();
        }
        $_SESSION['permisos'] = $permisosArray;
        $_SESSION['ULTIMA_ACTIVIDAD'] = time();
        
        registrarAuditoria($conn, "Login Admin", "Ingreso al sistema: " . $fila['usuario_user']);

        session_write_close(); 
        header("Location: ../index.php?ruta=inicio");
        exit;

    } else {
        header("Location: ../index.php?ruta=login&error=clave_incorrecta");
        exit;
    }
} 
else {
    $stmt->close(); 

    $sqlAlu = "SELECT * FROM alumnos WHERE usuario = ?";
    $stmt2 = $conn->prepare($sqlAlu);
    $stmt2->bind_param("s", $usuario);
    $stmt2->execute();
    $resAlu = $stmt2->get_result();

    if ($resAlu->num_rows == 1) {
        $filaAlu = $resAlu->fetch_assoc();

        if ($filaAlu['estado'] !== 'activo') {
            header("Location: ../index.php?ruta=login&error=usuario_inactivo");
            exit;
        }

        if (password_verify($clave, $filaAlu['clave'])) {
            session_regenerate_id(true);
            $_SESSION['id_alumno']   = $filaAlu['id_alumno'];
            $_SESSION['usuario']     = $filaAlu['usuario'];
            $_SESSION['rol']         = 'Alumno'; 
            $_SESSION['tipo_sesion'] = 'alumno'; 
            $_SESSION['permisos']    = [];
            $_SESSION['ULTIMA_ACTIVIDAD'] = time();

            registrarAuditoria($conn, "Login Alumno", "Ingreso estudiante: " . $filaAlu['usuario']);

            session_write_close();
            header("Location: ../index.php?ruta=inicio");
            exit;
        } else {
            header("Location: ../index.php?ruta=login&error=clave_incorrecta");
            exit;
        }
    } else {
        header("Location: ../index.php?ruta=login&error=usuario_no_existe");
        exit;
    }
}
$conn->close();
?>