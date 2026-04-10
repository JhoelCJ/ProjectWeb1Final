<?php
include_once("../conexion.php");
include_once("Plantilla.php");
session_start();

if (!isset($_SESSION['usuario'])) { exit; }

$limite = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode('REPORTE DE CURSOS (Máx. '.$limite.')'),0,1,'L');
$pdf->Ln(5);

$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(60,6,'Nombre del Curso',1,0,'C',1);
$pdf->Cell(50,6,'Instructor',1,0,'C',1);
$pdf->Cell(40,6,'Especialidad',1,0,'C',1);
$pdf->Cell(15,6,'Cupo',1,0,'C',1);
$pdf->Cell(25,6,'Estado',1,1,'C',1);

$pdf->SetFont('Arial','',8);

$sql = "SELECT * FROM cursos ORDER BY nombre_curso ASC LIMIT $limite";
$res = $conn->query($sql);

while($row = $res->fetch_assoc()) {
    $estado = strtoupper($row['estado_curso']);
    $pdf->Cell(60,6,utf8_decode($row['nombre_curso']),1,0,'L');
    $pdf->Cell(50,6,utf8_decode($row['instructor']),1,0,'L');
    $pdf->Cell(40,6,utf8_decode($row['especialidad']),1,0,'L');
    $pdf->Cell(15,6,$row['capacidad'],1,0,'C');
    $pdf->Cell(25,6,$estado,1,1,'C');
}

$pdf->Output('I', 'Reporte_Cursos.pdf');
?>