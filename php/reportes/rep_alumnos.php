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
$pdf->Cell(0,10,utf8_decode('LISTADO GENERAL DE ALUMNOS (Máx. '.$limite.')'),0,1,'L');
$pdf->Ln(5);

$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,6,utf8_decode('Cédula'),1,0,'C',1);
$pdf->Cell(70,6,'Apellidos y Nombres',1,0,'C',1);
$pdf->Cell(60,6,'Email',1,0,'C',1);
$pdf->Cell(30,6,'Estado',1,1,'C',1);

$pdf->SetFont('Arial','',9);

$sql = "SELECT * FROM alumnos ORDER BY apellido ASC LIMIT $limite";
$res = $conn->query($sql);

while($row = $res->fetch_assoc()) {
    $pdf->Cell(30,6,$row['cedula'],1,0,'C');
    $pdf->Cell(70,6,utf8_decode($row['apellido'] . ' ' . $row['nombre']),1,0,'L');
    $pdf->Cell(60,6,utf8_decode($row['email']),1,0,'L');
    $pdf->Cell(30,6,strtoupper($row['estado']),1,1,'C');
}

$pdf->Output('I', 'Reporte_Alumnos.pdf');
?>