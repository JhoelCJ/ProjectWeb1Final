<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function validarRol($rolesPermitidos) {
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $rolesPermitidos)) {
        header("Location: ../main.php");
        exit;
    }
}

