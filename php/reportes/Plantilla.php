<?php
require('../fpdf/fpdf.php');

class PDF extends FPDF
{

    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,utf8_decode('SISTEMA EMPRESARIAL Y ACADEMICO'),0,1,'C');
        $this->SetFont('Arial','I',10);
        $this->Cell(0,5,utf8_decode('Reporte Generado por el Sistema'),0,1,'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }
}
?>