<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

exigirPermiso('matricular_alumnos'); 
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:600px; margin:auto;">
        <h2 style="border-bottom: 2px solid #003366; color: #003366;">
            Matricular Alumno (Admin)
        </h2>

        <form action="php/curso/asignarAlumno.php" method="POST" onsubmit="window.matricularAlumnoAjax(event)">
            
            <div class="form-group">
                <label style="font-weight:bold; color:#003366;">Seleccionar Curso:</label>
                <select name="id_curso" class="w3-select w3-border" required>
                    <option value="" disabled selected>-- Cursos Activos --</option>
                    <?php
                    $cursos = $conn->query("SELECT id_curso, nombre_curso, capacidad FROM cursos WHERE estado_curso = 'activo'");
                    if ($cursos && $cursos->num_rows > 0) {
                        while($c = $cursos->fetch_assoc()) {
                            echo "<option value='{$c['id_curso']}'>{$c['nombre_curso']} (Cupo: {$c['capacidad']})</option>";
                        }
                    } else {
                        echo "<option disabled>No hay cursos activos</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group" style="margin-top:20px;">
                <label style="font-weight:bold; color:#003366;">Seleccionar Alumno:</label>
                <select name="id_alumno" class="w3-select w3-border" required>
                    <option value="" disabled selected>-- Buscar en tabla Alumnos --</option>
                    <?php
                    $sqlAlumnos = "SELECT id_alumno, nombre, apellido, cedula 
                                   FROM alumnos 
                                   WHERE estado = 'activo' 
                                   ORDER BY apellido ASC";
                    $resAlumnos = $conn->query($sqlAlumnos);
                    
                    if ($resAlumnos && $resAlumnos->num_rows > 0) {
                        while($a = $resAlumnos->fetch_assoc()) {
                            $texto = $a['apellido'] . " " . $a['nombre'] . " (" . $a['cedula'] . ")";
                            echo "<option value='{$a['id_alumno']}'>$texto</option>";
                        }
                    } else {
                        echo "<option disabled>No hay alumnos registrados en el sistema</option>";
                    }
                    ?>
                </select>
            </div>

            <div style="margin-top:30px; text-align:center;">
                <button type="submit" class="w3-button w3-blue w3-round" style="background-color:#003366!important; width:100%; padding:10px;">
                    Matricular
                </button>
            </div>
        </form>
    </div>
</div>