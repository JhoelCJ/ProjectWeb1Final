<?php
include_once("../conexion.php");
include_once("Plantilla.php");
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'Administrador') { exit; }

$limite = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
if ($limite <= 0) $limite = 50;

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode('REPORTE DE AUDITORÍA (Últimos '.$limite.' registros)'),0,1,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(0,5,utf8_decode('Generado el: ' . date('d/m/Y H:i:s')),0,1,'L');
$pdf->Ln(5);

$pdf->SetFillColor(232,232,232);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(35,6,'Fecha/Hora',1,0,'C',1);
$pdf->Cell(30,6,'Usuario',1,0,'C',1);
$pdf->Cell(40,6,utf8_decode('Acción'),1,0,'C',1);
$pdf->Cell(170,6,'Detalle',1,1,'C',1);

$pdf->SetFont('Arial','',8);

$sql = "SELECT * FROM auditoria ORDER BY fecha DESC LIMIT $limite";
$res = $conn->query($sql);

while($row = $res->fetch_assoc()) {
    $pdf->Cell(35,6,$row['fecha'],1,0,'C');
    $pdf->Cell(30,6,utf8_decode($row['usuario']),1,0,'L');
    $pdf->Cell(40,6,utf8_decode($row['accion']),1,0,'L');
    
    $detalle = substr($row['detalle'], 0, 110); 
    if(strlen($row['detalle']) > 110) $detalle .= '...';

    $pdf->Cell(170,6,utf8_decode($detalle),1,1,'L');
}

$pdf->Output('I', 'Reporte_Auditoria.pdf');
?>