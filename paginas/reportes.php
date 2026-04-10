<?php
include_once("../php/validarSesion.php");
include_once("../php/verificarPermiso.php");

if ($_SESSION['tipo_sesion'] == 'alumno') { echo "Acceso denegado"; exit; }
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1000px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366;">
            Centro de Reportes
        </h2>
        <p>Seleccione la cantidad de registros y genere el reporte.</p>

        <div class="w3-row-padding w3-margin-top">

            <div class="w3-col m6 l3 w3-margin-bottom">
                <div class="w3-card w3-hover-shadow w3-center w3-padding-16">
                    <h4>Alumnos</h4>
                    
                    <div class="w3-padding">
                        <label class="w3-small">Registros:</label>
                        <input type="number" id="cant_alumnos" class="w3-input w3-border w3-center" value="100" min="1">
                    </div>

                    <button onclick="generarReporte('alumnos')" class="w3-button w3-blue w3-round w3-block">
                        Generar PDF
                    </button>
                </div>
            </div>

            <div class="w3-col m6 l3 w3-margin-bottom">
                <div class="w3-card w3-hover-shadow w3-center w3-padding-16">
                    <h4>Notas</h4>
                    
                    <div class="w3-padding">
                        <label class="w3-small">Registros:</label>
                        <input type="number" id="cant_notas" class="w3-input w3-border w3-center" value="100" min="1">
                    </div>

                    <button onclick="generarReporte('notas')" class="w3-button w3-blue w3-round w3-block">
                        Generar PDF
                    </button>
                </div>
            </div>

            <div class="w3-col m6 l3 w3-margin-bottom">
                <div class="w3-card w3-hover-shadow w3-center w3-padding-16">
                    <h4>Cursos</h4>
                    
                    <div class="w3-padding">
                        <label class="w3-small">Registros:</label>
                        <input type="number" id="cant_cursos" class="w3-input w3-border w3-center" value="50" min="1">
                    </div>

                    <button onclick="generarReporte('cursos')" class="w3-button w3-blue w3-text-white w3-round w3-block">
                        Generar PDF
                    </button>
                </div>
            </div>

            <?php if($_SESSION['rol'] == 'Administrador') { ?>
            <div class="w3-col m6 l3 w3-margin-bottom">
                <div class="w3-card w3-hover-shadow w3-center w3-padding-16">
                    <h4>Auditoría</h4>
                    
                    <div class="w3-padding">
                        <label class="w3-small">Registros:</label>
                        <input type="number" id="cant_auditoria" class="w3-input w3-border w3-center" value="50" min="1">
                    </div>

                    <button onclick="generarReporte('auditoria')" class="w3-button w3-blue w3-round w3-block">
                        Generar PDF
                    </button>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

