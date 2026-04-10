<?php
include_once("../../php/conexion.php");
include_once("../../php/validarSesion.php");
include_once("../../php/verificarPermiso.php");
include_once("../../php/logger.php");

exigirPermiso('gestionar_cursos'); 

$idNota = isset($_POST['id_nota']) ? intval($_POST['id_nota']) : 0;
$n1 = isset($_POST['nota1']) ? floatval($_POST['nota1']) : 0;
$n2 = isset($_POST['nota2']) ? floatval($_POST['nota2']) : 0;
$n3 = isset($_POST['nota3']) ? floatval($_POST['nota3']) : 0;
$supletorio = (isset($_POST['supletorio']) && $_POST['supletorio'] !== '') ? floatval($_POST['supletorio']) : null;

if ($idNota <= 0) {
    echo "ID de nota inválido.";
    exit;
}

$promedio = ($n1 + $n2 + $n3) / 3;
$promedio = round($promedio, 2);

$estado = 'Reprobado'; 

if ($promedio >= 14) {
    $estado = 'Aprobado';
    $supletorio = null; 
} else {
    if ($supletorio !== null) {
        if ($supletorio >= 14) {
            $estado = 'Aprobado';
        } else {
            $estado = 'Reprobado';
        }
    } else {
        $estado = 'Supletorio';
    }
}

$sql = "UPDATE notas SET nota1=?, nota2=?, nota3=?, promedio=?, supletorio=?, estado_academico=? WHERE id_nota=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dddddsi", $n1, $n2, $n3, $promedio, $supletorio, $estado, $idNota);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Calificar", "Se actualizó la nota ID: $idNota. Prom: $promedio - Est: $estado");
    echo "exito";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>