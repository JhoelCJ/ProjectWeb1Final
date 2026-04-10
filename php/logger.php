<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function registrarAuditoria($conn, $accion, $detalle = "") {

    $idUser   = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : NULL;
    $usuario  = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Sistema/Desconocido';
    $rol      = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'Sin Rol';

    $permisos = isset($_SESSION['permisos']) ? json_encode($_SESSION['permisos'], JSON_UNESCAPED_UNICODE) : '[]';

    $ip = $_SERVER['REMOTE_ADDR'];

    $sql = "INSERT INTO auditoria (id_user, usuario, rol, permisos, accion, detalle, ip) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $idUser, $usuario, $rol, $permisos, $accion, $detalle, $ip);
    $stmt->execute();
    $stmt->close();
}
?>