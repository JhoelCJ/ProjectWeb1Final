<?php
session_start();
include_once("../php/conexion.php");
if (!isset($_SESSION['tipo_sesion']) || $_SESSION['tipo_sesion'] != 'alumno') { 
    echo "<div class='w3-panel w3-red'>Acceso denegado</div>"; 
    exit; 
}
$idAlumno = $_SESSION['id_alumno'];
?>

<div class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1000px; margin:auto;">
        <h2 style="color: #003366;">Inscripción de Cursos</h2>
        
        <table class="w3-table-all">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Especialidad</th>
                    <th>Cupos</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM cursos c 
                    WHERE c.estado_curso = 'activo' 
                    AND c.id_curso NOT IN (SELECT id_curso FROM notas WHERE id_alumno = $idAlumno)";
            $res = $conn->query($sql);

            if ($res && $res->num_rows > 0) {
                while ($c = $res->fetch_assoc()) {
                    echo "<tr>
                        <td>{$c['nombre_curso']}</td>
                        <td>{$c['especialidad']}</td>
                        <td>{$c['capacidad']}</td>
                        <td>
                            <button class='w3-button w3-blue w3-small w3-round' 
                                onclick='inscribirseCurso({$c['id_curso']})'>
                                Inscribirse
                            </button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='w3-center'>No hay cursos disponibles para inscripción.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function inscribirseCurso(idCurso) {
    if(!confirm("¿Deseas inscribirte en este curso?")) return;
    
    const formData = new FormData();
    formData.append('id_curso', idCurso);

    fetch('php/alumno/procesar_inscripcion.php', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(resp => {
        if(resp.trim() === 'exito') {
            alert("Inscripción exitosa.");
            cargarTab('paginas/alumno/inscripcionCurso.php');
        } else {
            alert(resp);
        }
    });
}
</script>