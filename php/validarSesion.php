<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    
    $ruta_actual = $_SERVER['SCRIPT_NAME'];
    
    if (strpos($ruta_actual, '/paginas/') !== false || strpos($ruta_actual, '/php/') !== false) {
        header("Location: ../index.php?ruta=login&timeout=1");
    } else {
        header("Location: ../index.php?ruta=login&timeout=1");
    }
    exit;
}
?>