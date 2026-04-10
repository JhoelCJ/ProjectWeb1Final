<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

exigirPermiso('gestionar_cursos');

$registrosPorPagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;

$sqlTotal = "SELECT COUNT(*) as total 
             FROM notas n
             INNER JOIN alumnos a ON n.id_alumno = a.id_alumno 
             INNER JOIN cursos c ON n.id_curso = c.id_curso";
             
$resTotal = $conn->query($sqlTotal);
$rowTotal = $resTotal->fetch_assoc();
$totalRegistros = $rowTotal['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT n.*, a.nombre, a.apellido, a.cedula, c.nombre_curso 
        FROM notas n
        INNER JOIN alumnos a ON n.id_alumno = a.id_alumno
        INNER JOIN cursos c ON n.id_curso = c.id_curso
        ORDER BY c.nombre_curso ASC, a.apellido ASC
        LIMIT $offset, $registrosPorPagina";
$result = $conn->query($sql);
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    
    <div class="contenedor" style="max-width:1100px; margin:auto;">
        <h2 style="border-bottom: 2px solid #003366; padding-bottom: 10px; color: #003366;">
            Gestión de Calificaciones (Alumnos)
            <button onclick="window.imprimirReporteRapido('notas')" class="w3-button w3-green w3-small w3-round" title="Exportar Notas a PDF">
                Generar Reporte
            </button>
        </h2>
        
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Alumno</th>
                        <th>Cédula</th>
                        <th class="text-center">N1</th>
                        <th class="text-center">N2</th>
                        <th class="text-center">N3</th>
                        <th class="text-center">Prom</th>
                        <th class="text-center">Suple</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($fila = $result->fetch_assoc()) {
                        
                        $claseEstado = '';
                        if($fila['estado_academico'] == 'Aprobado') $claseEstado = 'estado activo';
                        elseif($fila['estado_academico'] == 'Reprobado') $claseEstado = 'estado inactivo';
                        else $claseEstado = 'supletorio'; 

                        $n1 = $fila['nota1']; $n2 = $fila['nota2']; $n3 = $fila['nota3'];
                        $suple = ($fila['supletorio'] !== NULL) ? $fila['supletorio'] : '-';

                        $nombreCompleto = $fila['apellido'] . ' ' . $fila['nombre'];

                        echo "<tr>
                            <td>{$fila['nombre_curso']}</td>
                            <td>{$nombreCompleto}</td>
                            <td>{$fila['cedula']}</td>
                            <td class='text-center'>$n1</td> 
                            <td class='text-center'>$n2</td> 
                            <td class='text-center'>$n3</td>
                            <td class='text-center'><b>{$fila['promedio']}</b></td>
                            <td class='text-center'>$suple</td>
                            <td class='$claseEstado'>{$fila['estado_academico']}</td>
                            <td>
                                <button class='w3-button w3-blue w3-small' 
                                        style='background-color:#003366; color:white; border-radius:3px;'
                                        onclick='window.abrirModalNotas({$fila['id_nota']}, \"$nombreCompleto\", \"{$fila['nombre_curso']}\", $n1, $n2, $n3, \"$suple\")'>
                                     Calificar
                                </button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center; padding:20px;'>No hay alumnos matriculados.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1) { ?>
        <div class="w3-center w3-padding-16">
            <div class="w3-bar w3-border w3-round w3-light-grey">
                <?php
                if ($pagina > 1) {
                    $pagAnt = $pagina - 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/gestionNotas.php?pagina=$pagAnt')\">&laquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&laquo;</button>";
                }

                $rango = 2;
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    if ($i == 1 || $i == $totalPaginas || ($i >= $pagina - $rango && $i <= $pagina + $rango)) {
                        $claseActiva = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                        echo "<button class='w3-bar-item w3-button $claseActiva' onclick=\"cargarTab('paginas/gestionNotas.php?pagina=$i')\">$i</button>";
                    } elseif ($i == $pagina - $rango - 1 || $i == $pagina + $rango + 1) {
                        echo "<span class='w3-bar-item w3-button w3-disabled'>...</span>";
                    }
                }

                if ($pagina < $totalPaginas) {
                    $pagSig = $pagina + 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/gestionNotas.php?pagina=$pagSig')\">&raquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&raquo;</button>";
                }
                ?>
            </div>
            <div class="w3-tiny w3-text-grey w3-margin-top">
                Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?>
            </div>
        </div>
        <?php } ?>

    </div>

    <div id="modalNotas" class="modal-overlay">
        <div class="modal-content-corp">
            <div class="modal-header">
                <h3>Calificar Alumno</h3>
                <span class="close-btn" onclick="document.getElementById('modalNotas').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formNotas" onsubmit="window.guardarNotaAjax(event)" action="php/notas/guardar_nota.php">
                    <input type="hidden" name="id_nota" id="m_id_nota">
                    <div class="alumno-info">
                        Estudiante: <b id="m_nombre_alumno"></b><br>
                        Curso: <b id="m_curso"></b>
                    </div>
                    <div class="notas-grid">
                        <div class="nota-input-group">
                            <label>Parcial 1</label>
                            <input type="number" step="0.01" min="0" max="20" name="nota1" id="m_nota1" oninput="window.calcularPromedioModal()" required>
                        </div>
                        <div class="nota-input-group">
                            <label>Parcial 2</label>
                            <input type="number" step="0.01" min="0" max="20" name="nota2" id="m_nota2" oninput="window.calcularPromedioModal()" required>
                        </div>
                        <div class="nota-input-group">
                            <label>Parcial 3</label>
                            <input type="number" step="0.01" min="0" max="20" name="nota3" id="m_nota3" oninput="window.calcularPromedioModal()" required>
                        </div>
                    </div>
                    <div class="resultado-panel">
                        <span style="font-size:0.8rem; color:#666; text-transform:uppercase;">Promedio General</span>
                        <span id="m_promedio" class="promedio-valor">0.00</span>
                        <div id="m_mensaje_estado" class="estado-mensaje"></div>
                    </div>
                    <div id="m_div_supletorio" style="display:none; margin-bottom:20px; border-top:1px dashed #ccc; padding-top:15px;">
                        <label style="color:#e65100; font-weight:bold; display:block; margin-bottom:5px;">
                            Examen Supletorio:
                        </label>
                        <input type="number" step="0.01" min="0" max="20" class="w3-input" 
                               style="border-color:#e65100; text-align:center; font-weight:bold;"
                               name="supletorio" id="m_supletorio" placeholder="Nota Supletorio">
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="document.getElementById('modalNotas').style.display='none'" 
                                style="background-color: #6c757d; color: white;">Cancelar</button>
                        <button type="submit" style="background-color: #28a745; color: white;">
                            Guardar Notas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>