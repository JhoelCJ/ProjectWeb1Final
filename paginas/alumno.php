<?php
include_once("../php/validarSesion.php");
include_once("../php/verificarPermiso.php");
exigirPermiso('gestionar_alumnos');
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:600px; margin:auto;">
        <h2 style="border-bottom: 2px solid #003366; color: #003366;">
            Registrar Alumno
        </h2>

        <form action="php/alumno/insercionAlumno.php" method="POST" onsubmit="window.registrarAlumnoAjax(event)">
            
            <fieldset>
                <legend>Datos del Estudiante</legend>
                
                <p>
                    <label>Nombres:</label>
                    <input type="text" name="nombre" class="w3-input w3-border" required>
                </p>
                
                <p>
                    <label>Apellidos:</label>
                    <input type="text" name="apellido" class="w3-input w3-border" required>
                </p>

                <p>
                    <label>Cédula:</label>
                    <input type="text" name="cedula" class="w3-input w3-border" maxlength="10" required>
                </p>

                <p>
                    <label>Fecha Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" 
                           class="w3-input w3-border" onchange="window.calcularEdad()" required>
                </p>

                <p>
                    <label>Edad:</label>
                    <input type="text" name="edad" id="edad" class="w3-input w3-border" 
                           readonly style="background-color: #eee;">
                </p>

                <p>
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="w3-input w3-border">
                </p>
                
                <p>
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="w3-input w3-border" maxlength="10">
                </p>
            </fieldset>

            <div style="margin-top:20px; text-align:center;">
                <button type="submit" class="w3-button w3-blue w3-round" style="background-color:#003366!important; padding: 10px 20px;">
                    Guardar Alumno
                </button>
            </div>
        </form>
    </div>
</div>