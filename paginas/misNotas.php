<?php
session_start();
include_once("../php/conexion.php");

if ($_SESSION['tipo_sesion'] != 'alumno') { echo "Acceso denegado"; exit; }

$idAlumno = $_SESSION['id_alumno'];

$registrosPorPagina = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;

$sqlTotal = "SELECT COUNT(*) as total FROM notas WHERE id_alumno = $idAlumno";
$resTotal = $conn->query($sqlTotal);
$totalRegistros = $resTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT n.*, c.nombre_curso, c.instructor 
        FROM notas n 
        INNER JOIN cursos c ON n.id_curso = c.id_curso 
        WHERE n.id_alumno = $idAlumno 
        ORDER BY c.nombre_curso ASC 
        LIMIT $offset, $registrosPorPagina";
$res = $conn->query($sql);
?>

<div class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1000px; margin:auto;">
        <h2 style="color: #003366;">Mis Calificaciones</h2>

        <div style="overflow-x:auto;">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-light-grey">
                        <th>Curso</th>
                        <th>Instructor</th>
                        <th class="w3-center">P1</th>
                        <th class="w3-center">P2</th>
                        <th class="w3-center">P3</th>
                        <th class="w3-center">Prom</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    while ($f = $res->fetch_assoc()) {
                        $estado = $f['estado_academico'];
                        $color = ($estado=='Aprobado')?'green':(($estado=='Reprobado')?'red':'orange');
                        
                        echo "<tr>
                            <td>{$f['nombre_curso']}</td>
                            <td>{$f['instructor']}</td>
                            <td class='w3-center'>{$f['nota1']}</td>
                            <td class='w3-center'>{$f['nota2']}</td>
                            <td class='w3-center'>{$f['nota3']}</td>
                            <td class='w3-center'><b>{$f['promedio']}</b></td>
                            <td style='color:$color; font-weight:bold;'>$estado</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='w3-center'>No tienes cursos registrados.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1) { ?>
        <div class="w3-center w3-padding-16">
            <div class="w3-bar w3-border w3-round w3-light-grey">
                <?php
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    $clase = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                    echo "<button class='w3-bar-item w3-button $clase' onclick=\"cargarTab('paginas/alumno/misNotas.php?pagina=$i')\">$i</button>";
                }
                ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>