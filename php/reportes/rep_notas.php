<?php
include_once("../conexion.php");
include_once("Plantilla.php");
session_start();

if (!isset($_SESSION['usuario'])) { header("Location: ../../index.php"); exit; }

$limite = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
if ($limite <= 0) $limite = 100;

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode('REPORTE DE CALIFICACIONES (Máx. '.$limite.')'),0,1,'L');
$pdf->Ln(5);

$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,6,'Curso',1,0,'C',1);
$pdf->Cell(60,6,'Alumno',1,0,'C',1);
$pdf->Cell(15,6,'Prom',1,0,'C',1);
$pdf->Cell(25,6,'Estado',1,1,'C',1);

$pdf->SetFont('Arial','',9);

$sql = "SELECT n.*, a.nombre, a.apellido, c.nombre_curso 
        FROM notas n
        INNER JOIN alumnos a ON n.id_alumno = a.id_alumno
        INNER JOIN cursos c ON n.id_curso = c.id_curso
        ORDER BY c.nombre_curso, a.apellido 
        LIMIT $limite";

$res = $conn->query($sql);

while($row = $res->fetch_assoc()) {
    $pdf->Cell(50,6,utf8_decode($row['nombre_curso']),1,0,'L');
    $pdf->Cell(60,6,utf8_decode($row['apellido'] . ' ' . $row['nombre']),1,0,'L');
    $pdf->Cell(15,6,$row['promedio'],1,0,'C');
    $pdf->Cell(25,6,utf8_decode($row['estado_academico']),1,1,'C');
}

$pdf->Output('I', 'Reporte_Notas.pdf');
?>