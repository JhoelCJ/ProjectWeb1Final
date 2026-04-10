<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function tienePermiso($permiso) {
    if (isset($_SESSION['permisos']) && in_array($permiso, $_SESSION['permisos'])) {
        return true;
    }
    return false;
}

function exigirPermiso($permiso) {
    if (!tienePermiso($permiso)) {
        echo "<script>
                alert('ACCESO DENEGADO: No tienes permiso para realizar esta acción.'); 
                window.location.href='../main.php';
              </script>";
        exit;
    }
}
?>