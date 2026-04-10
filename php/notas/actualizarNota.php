<?php
include_once("../conexion.php");
include_once("../validarSesion.php");
include_once("../validarRol.php");

validarRol(['Administrador', 'Instructor']);

$idNota = intval($_POST['id_nota']);
$n1 = floatval($_POST['nota1']);
$n2 = floatval($_POST['nota2']);
$n3 = floatval($_POST['nota3']);

if ($n1 < 0 || $n1 > 20 || $n2 < 0 || $n2 > 20 || $n3 < 0 || $n3 > 20) {
    echo "<script>alert('Las notas deben estar entre 0 y 20'); window.history.back();</script>";
    exit;
}

$promedio = ($n1 + $n2 + $n3) / 3;
$promedio = round($promedio, 2);

$estado = "";
$supletorio = NULL;

if ($promedio >= 14) {
    $estado = "Aprobado";

    $supletorio = NULL; 
} else {
    $estado = "Supletorio";

    if (isset($_POST['supletorio']) && $_POST['supletorio'] !== '') {
        $notaSuple = floatval($_POST['supletorio']);
        
        if ($notaSuple < 0 || $notaSuple > 20) {
            echo "<script>alert('El supletorio debe estar entre 0 y 20'); window.history.back();</script>";
            exit;
        }

        $supletorio = $notaSuple;

        if ($notaSuple >= 14) {
            $estado = "Aprobado (Sup)";
        } else {
            $estado = "Reprobado";
        }
    }
}

$sql = "UPDATE notas 
        SET nota1=?, nota2=?, nota3=?, promedio=?, supletorio=?, estado_academico=? 
        WHERE id_nota=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dddddsi", $n1, $n2, $n3, $promedio, $supletorio, $estado, $idNota);

if ($stmt->execute()) {
    include_once("../logger.php");
    registrarAuditoria($conn, "Calificar", "ID Nota: $idNota - Prom: $promedio - Est: $estado");

    echo "<script>
        alert('Notas guardadas. Estado: $estado');
        window.location.href='../../paginas/gestionNotas.php';
    </script>";
} else {
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>